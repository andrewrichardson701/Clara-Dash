<?php
$dir = '/opt/observium/html/api';
include '/opt/observium/html/api/includes/functions.php';

$config = require $dir . '/config.php';
require_once $dir . '/lib/DB.php';
$db     = new DB($config['db']);

$devices = [];

$q_hostname = '%'.$_GET['hostname'].'%' ?? null;
$q_ports    = $_GET['ports']    ?? 1;
$q_sensors  = $_GET['sensors']  ?? 1;
$q_with_max = $_GET['with_max'] ?? 0;

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

                // Max value (optional)
                if ($q_with_max) {
                    $rrd = sprintf('/opt/observium/rrd/%s/port-%d.rrd', $device_info['device']['hostname'], $port['ifIndex']);
                    $ds = rrd_ds_indexes($rrd);

                    $ports['ports'][$i]['max_in_octets']  = isset($ds['INOCTETS'])  ? rrd_max_safe($rrd, $ds['INOCTETS'], '-1y')  : null;
                    $ports['ports'][$i]['max_out_octets'] = isset($ds['OUTOCTETS']) ? rrd_max_safe($rrd, $ds['OUTOCTETS'], '-1y') : null;


                    if (!empty($port['ifSpeed'])) {
                        $ports['ports'][$i]['max_in_bps']  = $ports['ports'][$i]['max_in_octets']  * 8;
                        $ports['ports'][$i]['max_out_bps'] = $ports['ports'][$i]['max_out_octets'] * 8;

                        $ports['ports'][$i]['max_in_util_pct']  = round($ports['ports'][$i]['max_in_bps']  / $port['ifSpeed'] * 100, 2);
                        $ports['ports'][$i]['max_out_util_pct'] = round($ports['ports'][$i]['max_out_bps'] / $port['ifSpeed'] * 100, 2);
                    }
                }
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

            // Max value (optional)
            if ($q_with_max) {
                $rrd = sprintf(
                    '/opt/observium/rrd/%s/sensor-%s-%s-%s.rrd',
                    $device_info['device']['hostname'],       // hostname
                    $sensor['sensor_class'],                  // e.g., temperature, current
                    $sensor['sensor_type'],                   // e.g., ATEN-IPMI-MIB-sensorReading
                    $sensor['sensor_index']                   // numeric or dotted index
                );

                $sensors['sensors'][$i]['max_value'] = rrd_max_any($rrd, null, '-7d', 'AVERAGE');
            }
        }
        
        // add the sensors to the array
        $devices[$device]['sensors'] = array_key_exists('sensors', $sensors) ? $sensors['sensors'] : [];
    }

}

header('Content-Type: application/json');
echo json_encode($devices);

?>
