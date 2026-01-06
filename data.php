<?php
$dir = '/opt/observium/html/api';
include '/opt/observium/html/api/includes/functions.php';

$config = require $dir . '/config.php';
require_once $dir . '/lib/DB.php';
$db     = new DB($config['db']);

$devices = [];

$q_hostname = $_GET['hostname'] ?? null;
$q_ports    = $_GET['ports']    ?? 1;
$q_sensors  = $_GET['sensors']  ?? 1;

// check if a hostname is set, and if it is, search directly for it
if ($q_hostname) {
    $device_list = getApiData(['action' => 'devices', 'hostname' => $q_hostname]); 
} else {
    $device_list = getApiData(['action' => 'devices']); 
}

foreach ($device_list['devices'] as $device) {  
    $devices[$device['hostname']]['device'] = $device;
}

foreach($devices as $device => $device_info) {
    $device_id = $device_info['device']['device_id'];

    // check if ports are required from the api url
    if ($q_ports == 1) {
        $ports   = getApiData(['action' => 'ports', 'device_id' => $device_id])   ?? [];

        if (array_key_exists('ports', $ports)) {
            foreach ($ports['ports'] as $i => $port) {
                // print_r($port);
                // exit();
                $graph = getApiData(['action' => 'graph', 'device_id' => $device_id, 'graph_type' => 'port', 'port_id' => $port['port_id'], 'legend' => 'yes']);
                if (is_array($graph) && array_key_exists('graph', $graph)) {
                    $graph = $graph['graph'];
                }
                $ports['ports'][$i]['graph'] = $graph;
            }
        }

        // add the ports to the array
        $devices[$device]['ports']   = array_key_exists('ports', $ports) ? $ports['ports'] : [];
    }

    // check if sensors are required from the api url
    if ($q_sensors == 1) {
        $sensors = getApiData(['action' => 'sensors', 'device_id' => $device_id]) ?? [];

        if (array_key_exists('sensors', $sensors)) {
            foreach ($sensors['sensors'] as $i => $sensor) {
                // print_r($sensor);
                // exit();
                $graph = getApiData(['action' => 'graph', 'device_id' => $device_id, 'graph_type' => 'sensor', 'sensor_id' => $sensor['sensor_id'], 'legend' => 'yes']);
                if (array_key_exists('graph', $graph)) {
                    $graph = $graph['graph'];
                }
                $sensors['sensors'][$i]['graph'] = $graph;
            }
        }
        
        // add the sensors to the array
        $devices[$device]['sensors'] = array_key_exists('sensors', $sensors) ? $sensors['sensors'] : [];
    }

}

header('Content-Type: application/json');
echo json_encode($devices);

?>
