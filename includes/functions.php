<?php

function getApiData($q = []) {

    global $dir;
    // Back up original $_GET to avoid polluting global state
    $old_get = $_GET;

    $_GET = $q;

    ob_start();
    try {
        include $dir . '/api.php';
    } catch (Throwable $e) {
        ob_end_clean(); // Ensure no partial output
        $_GET = $old_get; // Restore original $_GET
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
    $response = ob_get_clean();

    $_GET = $old_get;

    $data = json_decode($response, true);

    if ($data === null) {
        return null;
    }

    if ($data['status'] == 'error') {
        return null;
    }

    return $data;
}

function covnertPortMetric($bytes) {
    $bits = $bytes*8;

    $data = $bits;
    $unit = 'bps';

    if ($data > 1000) {
        $data = $data/1000;
        $unit = 'kbps';
    }

    if ($data > 1000) {
        $data = $data/1000;
        $unit = 'mbps';
    }

    if ($data > 1000) {
        $data = $data/1000;
        $unit = 'gbps';
    }
    if ($data > 1000) {
        $data = $data/1000;
        $unit = 'tbps';
    }

    return $data.$unit;
}

function search_file($dir, $file_to_search) {
    foreach (scandir($dir) as $file) {
        if (!is_dir($dir . DIRECTORY_SEPARATOR . $file) && $file === $file_to_search) {
            return true;
        }
    }
    return false;
}

function rrd_max_value(string $rrd_file): ?float
{
    if (!is_file($rrd_file)) {
        return null;
    }

    $cmd = sprintf(
        'rrdtool fetch %s MAX --start 0',
        escapeshellarg($rrd_file)
    );

    exec($cmd, $output, $rc);
    if ($rc !== 0) {
        return null;
    }

    $max = null;

    foreach ($output as $line) {
        // Timestamped lines only
        if (preg_match('/^\d+:/', $line)) {
            $values = preg_split('/\s+/', trim(substr($line, strpos($line, ':') + 1)));
            foreach ($values as $v) {
                if (is_numeric($v)) {
                    $v = (float)$v;
                    if ($max === null || $v > $max) {
                        $max = $v;
                    }
                }
            }
        }
    }

    return $max;
}



function rrd_ds_indexes(string $rrd): array
{
    if (!is_file($rrd)) {
        return [];
    }

    exec(
        'rrdtool info ' . escapeshellarg($rrd),
        $out,
        $rc
    );

    if ($rc !== 0) {
        return [];
    }

    $map = [];

    foreach ($out as $line) {
        // Example:
        // ds[ifInOctets].index = 0
        if (preg_match('/^ds\[(.+?)\]\.index\s+=\s+(\d+)/', $line, $m)) {
            $map[$m[1]] = (int)$m[2];
        }
    }

    return $map;
}

function rrd_max_safe(string $rrd, int $ds_index = 0, string $start = '-7d'): ?float
{
    if (!is_file($rrd)) {
        return null; // RRD missing
    }

    exec("rrdtool fetch " . escapeshellarg($rrd) . " MAX --start " . escapeshellarg($start), $output, $rc);

    if ($rc !== 0 || empty($output)) {
        return null; // fetch failed
    }

    $max = null;

    foreach ($output as $line) {
        if (preg_match('/^\d+:/', $line)) {
            $vals = preg_split('/\s+/', trim(substr($line, strpos($line, ':') + 1)));

            if (!isset($vals[$ds_index])) continue;

            $v = $vals[$ds_index];

            if ($v === 'nan') continue; // skip NaN
            if (!is_numeric($v)) continue;

            $v = (float)$v;

            if ($max === null || $v > $max) $max = $v;
        }
    }

    return $max; // null if no valid values found
}

function rrd_max_sensor(string $rrd, string $start = '-7d'): ?float
{
    if (!is_file($rrd)) return null;

    // Get DS names
    $ds = rrd_ds_indexes($rrd);
    $first_ds_index = reset($ds);
    if ($first_ds_index === false) return null;

    // Fetch MAX
    exec(
        'rrdtool fetch ' . escapeshellarg($rrd) . ' MAX --start ' . escapeshellarg($start),
        $out,
        $rc
    );
    if ($rc !== 0 || empty($out)) return null;

    $max = null;
    foreach ($out as $line) {
        if (preg_match('/^\d+:/', $line)) {
            $vals = preg_split('/\s+/', trim(substr($line, strpos($line, ':') + 1)));
            if (!isset($vals[$first_ds_index])) continue;
            $v = $vals[$first_ds_index];
            if ($v === 'nan') continue;
            if (!is_numeric($v)) continue;
            $v = (float)$v;
            if ($max === null || $v > $max) $max = $v;
        }
    }

    return $max;
}

function rrd_max_port(string $rrd, int $ds_index, string $start = '-7d'): ?float
{
    if (!is_file($rrd)) return null;

    exec("rrdtool fetch " . escapeshellarg($rrd) . " MAX --start " . escapeshellarg($start), $out, $rc);
    if ($rc !== 0 || empty($out)) return null;

    $max = null;
    foreach ($out as $line) {
        if (preg_match('/^\d+:/', $line)) {
            $vals = preg_split('/\s+/', trim(substr($line, strpos($line, ':') + 1)));
            if (!isset($vals[$ds_index])) continue;
            $v = $vals[$ds_index];
            if ($v === 'nan') continue;
            if (!is_numeric($v)) continue;
            $v = (float)$v;
            if ($max === null || $v > $max) $max = $v;
        }
    }

    return $max;
}

/**
 * Get the max value from any Observium RRD (GAUGE or DERIVE)
 *
 * @param string $rrd      Full path to RRD file
 * @param int|null $ds_index Optional DS index (0 if null)
 * @param string $start    RRD fetch start time (default: '-7d')
 * @param string $cf       Consolidation function: 'AVERAGE' or 'MAX' (default: 'AVERAGE')
 *
 * @return float|null      Max value or null if unavailable
 */
function rrd_max_any(string $rrd, ?int $ds_index = null, string $start = '-7d', string $cf = 'AVERAGE'): ?float
{
    if (!is_file($rrd)) return null;

    // If DS index not provided, get first DS
    if ($ds_index === null) {
        $ds_info = rrd_ds_indexes($rrd);
        if (empty($ds_info)) return null;
        $ds_index = reset($ds_info); // first DS index
    }

    // Fetch RRD data
    exec("rrdtool fetch " . escapeshellarg($rrd) . " " . escapeshellarg($cf) . " --start " . escapeshellarg($start), $output, $rc);
    if ($rc !== 0 || empty($output)) return null;

    $max = null;

    foreach ($output as $line) {
        if (preg_match('/^\d+:/', $line)) {
            $vals = preg_split('/\s+/', trim(substr($line, strpos($line, ':') + 1)));
            if (!isset($vals[$ds_index])) continue;

            $v = $vals[$ds_index];
            if ($v === 'nan') continue;

            $v = (float)$v;
            if ($max === null || $v > $max) $max = $v;
        }
    }

    return $max;
}

function rrd_max_observium(string $rrd, ?int $ds_index = null, string $start = '-7d'): ?float
{
    if (!file_exists($rrd)) return null;

    // Get DS indexes if not provided
    if ($ds_index === null) {
        $ds_info = rrd_ds_indexes($rrd);
        if (empty($ds_info)) return null;
        $ds_index = reset($ds_info);
    }

    exec("rrdtool fetch " . escapeshellarg($rrd) . " AVERAGE --start " . escapeshellarg($start), $out, $rc);
    if ($rc !== 0 || empty($out)) return null;

    $max = null;
    foreach ($out as $line) {
        if (preg_match('/^\d+:/', $line)) {
            $vals = preg_split('/\s+/', trim(substr($line, strpos($line, ':') + 1)));
            if (!isset($vals[$ds_index])) continue;

            $v = $vals[$ds_index];
            if ($v === 'nan') continue;

            $v = (float)$v;
            if ($max === null || $v > $max) $max = $v;
        }
    }

    return $max;
}
