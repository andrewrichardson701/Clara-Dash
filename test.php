<?php
$dir = '/opt/observium/html/api';
include '/opt/observium/html/api/includes/functions.php';

$config = require $dir . '/config.php';
require_once $dir . '/lib/DB.php';
$db     = new DB($config['db']);

// List of devices

$device_list = [
    'localhost',
    'ar-fw',
    'ar-sw',
    'jumpbox',
    'jumpcli',
    'pi-hole',
    'plex',
    'proxmox',
    'todo',
    'torrent',
    'truenas',
    'web'
];

$devices = [];

// for each device get the device_id
foreach ($device_list as $device) {  
    $device_info = getApiData(['action' => 'device', 'hostname' => $device]);

    $devices[$device]['device'] = $device_info['device'];
}



foreach($devices as $device => $device_info) {
    $device_id = $device_info['device']['device_id'];

    $ports   = getApiData(['action' => 'ports', 'device_id' => $device_id])   ?? [];
    $sensors = getApiData(['action' => 'sensors', 'device_id' => $device_id]) ?? [];


    if (array_key_exists('ports', $ports)) {
        foreach ($ports['ports'] as $i => $port) {
            // print_r($port);
            // exit();
            $graph = getApiData(['action' => 'graph', 'device_id' => $device_id, 'graph_type' => 'port', 'port_id' => $port['port_id'], 'legend' => 'yes']);
            if (array_key_exists('graph', $graph)) {
                $graph = $graph['graph'];
            }
            $ports['ports'][$i]['graph'] = $graph;
        }
    }

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
    

    $devices[$device]['ports']   = array_key_exists('ports', $ports) ? $ports['ports'] : [];
    $devices[$device]['sensors'] = array_key_exists('sensors', $sensors) ? $sensors['sensors'] : [];
}

// header('Content-Type: application/json');
// print_r($devices);

?>
<table>
    <thead>
        <tr>
            <th>Device</th>
            <th>Graph Type</th>
            <th>Graph Name</th>
            <th>Graph</th>
            <th>Current Data</th>
        </tr>
    </thead>
    <tbody>
<?php
foreach($devices as $device) {
    foreach ($device['ports'] as $i => $port) {
        // if ($device['ports'][$i]['ifInOctets_rate'] > 0 || $device['ports'][$i]['ifOutOctets_rate'] > 0){ 
            ?>
            <tr>
                <th><?php echo $device['device']['hostname']; ?></th>
                <th>Port</th>
                <th><?php echo $device['ports'][$i]['ifName']; ?></th>
                <th><?php echo($device['ports'][$i]['graph']['img_full_tag']); ?></th>
                <th>
                    In: <?php echo(covnertPortMetric($device['ports'][$i]['ifInOctets_rate'])); ?><br>
                    Out: <?php echo(covnertPortMetric($device['ports'][$i]['ifOutOctets_rate'])); ?>
                </th>
            </tr>
            <?php
        // }
    }
    foreach ($device['sensors'] as $i => $sensor) {
        ?>
        <tr>
            <th><?php echo $device['device']['hostname']; ?></th>
            <th>Sensor - <?php echo $device['sensors'][$i]['sensor_class']; ?></th>
            <th><?php echo $device['sensors'][$i]['sensor_descr']; ?></th>
            <th><?php echo($device['sensors'][$i]['graph']['img_full_tag']); ?></th>
            <th>
                <?php echo($device['sensors'][$i]['sensor_value']); ?>
            </th>
        </tr>
        <?php
        
    }
}
?>
    </tbody>
</table>