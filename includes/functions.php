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

