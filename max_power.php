<?php

$_GET['sensors'] = 1;
$_GET['ports'] = 0;
$_GET['with_max'] = 1;

ob_start();
include 'data.php';
$api_data = ob_get_clean();

$data = json_decode($api_data, true);

// print_r($data);

$power_data = [];

foreach ($data as $device => $device_info) {
    $power_data[$device] = array('hostname' => $device, 
                            'max_inlet' => $device_info['sensors'][0]['max_value'] ?? null, 
                            'current_inlet' => $device_info['sensors'][0]['sensor_value'] ?? null,
                            'max_inlet_1.1' => $device_info['sensors'][6]['max_value'] ?? null, 
                            'current_inlet_1.1' => $device_info['sensors'][6]['sensor_value'] ?? null,
                            'max_inlet_1.2' => $device_info['sensors'][7]['max_value'] ?? null, 
                            'current_inlet_1.2' => $device_info['sensors'][7]['sensor_value'] ?? null);
}

print_r($power_data);