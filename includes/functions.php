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


/**
 * Get the max value from an Observium RRD (ports or sensors)
 * 
 * Tries to use DS max first; falls back to scanning the data manually.
 *
 * @param string $rrd       Full path to RRD file
 * @param int|null $ds_index Optional DS index (0 if null)
 * @param string $start     Start time for manual fetch (default: '-7d')
 * @return float|null       Max value or null if unavailable
 */
/**
 * Get the maximum value from an Observium RRD (ports or sensors)
 * Works for DERIVE (counters) and GAUGE (temperatures, currents, PDU values)
 *
 * @param string $rrd       Full path to the RRD
 * @param int|null $ds_index Optional DS index (0 if null)
 * @param string $start     Start time for manual scan / xport (default -7d)
 * @param string $end       End time for scan (default now)
 * @return float|null       Max value or null if unavailable
 */
function rrd_max_smart(string $rrd, ?int $ds_index = null, string $start='-7d', string $end='now'): ?float {
    if (!file_exists($rrd)) return null;

    // Get DS info
    $ds_info = rrd_ds_indexes($rrd);
    if (empty($ds_info)) return null;
    $ds_index = $ds_index ?? reset($ds_info);

    // Try DS max from rrdtool info
    $ds_max = null;
    exec("rrdtool info " . escapeshellarg($rrd), $info, $rc);
    if ($rc === 0 && !empty($info)) {
        foreach ($info as $line) {
            if (preg_match('/ds\[[^\]]+\]\.max\s*=\s*(.*)/', $line, $matches)) {
                $v = trim($matches[1]);
                if (is_numeric($v)) {
                    $ds_max = floatval($v);
                    break;
                }
            }
        }
    }

    if ($ds_max !== null) return $ds_max;

    // Fall back to scanning data with xport (works for sparse GAUGE)
    $cmd = sprintf(
        'rrdtool xport --start %s --end %s DEF:ds=%s:ds:AVERAGE XPORT:ds:"val"',
        escapeshellarg($start),
        escapeshellarg($end),
        escapeshellarg($rrd)
    );

    exec($cmd, $output, $rc);
    if ($rc !== 0 || empty($output)) return null;

    $max = null;

    foreach ($output as $line) {
        if (strpos($line, '|') === false) continue; // skip headers

        list($ts, $val) = explode('|', $line);
        $val = trim($val);
        if ($val === 'nan') continue;

        // handle scientific notation
        if (!is_numeric($val) && !preg_match('/^[+-]?\d+(\.\d+)?(e[+-]?\d+)?$/i', $val)) continue;

        $v = floatval($val);
        if ($max === null || $v > $max) $max = $v;
    }

    return $max;
}



