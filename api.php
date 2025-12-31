<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Content-Type: application/json');
}

// $dir = __DIR__;
$dir = '/opt/observium/html/api';
require_once $dir . '/config.php';
require_once $dir . '/lib/DB.php';

$config = require $dir . '/config.php';
$db     = new DB($config['db']);

// print_r($config);

// Simple router

$device_id       = $_GET['device_id'] ?? null;
$hostname        = $_GET['hostname'] ?? null;
$port_id         = $_GET['port_id'] ?? null;
$sensor_id       = $_GET['sensor_id'] ?? null;
$sensor_type     = $_GET['sensor_type'] ?? null;
$index           = $_GET['index'] ?? null;
$where           = $_GET['where'];
$params          = $_GET['params'];

$where_conditions = array();
if ($where) {
    $where_array = explode(',', $where);
    if ($params) {
        $params_array = explode(',', $params);
        if (count($params_array) == count($where_array)) {
            foreach ($where_array as $where_field => $where_param) {
                $where_conditions[$where_field] = $where_param;
            }
        } 
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'] ?? '';

    try {
        switch ($action) {
            // List all devices
            case 'devices':
                if ($hostname) {
                    $devices = $db->fetchAll("SELECT * FROM devices WHERE hostname LIKE ?", [$hostname]);
                } else {
                    $devices = $db->fetchAll("SELECT * FROM devices");
                }
                echo json_encode(['status' => 'ok', 'devices' => $devices]);
                break;

            // Get single device info
            case 'device':
                if (!$device_id && !$hostname) throw new Exception("Missing device id / hostname");
                
                if ($device_id) { 
                    $device = $db->query("SELECT * FROM devices WHERE device_id=?", [$device_id])->fetch();
                } else {
                    $device = $db->query("SELECT * FROM devices WHERE hostname=?", [$hostname])->fetch();
                }
                if (!$device) throw new Exception("Device not found");

                // // Optionally fetch live SNMP sysDescr
                // if (!empty($_GET['live']) && $_GET['live'] == 1) {
                //     $sysDescr = $snmp->get($device['ip_address'], $device['community'], 'SNMPv2-MIB::sysDescr.0');
                //     $device['sysDescr_live'] = $sysDescr;
                // }

                echo json_encode(['status' => 'ok', 'device' => $device]);
                break;

            // List all ports for a device
            case 'ports':
                if (!$device_id) throw new Exception("Missing device id");
                $ports = $db->query("SELECT * FROM ports WHERE device_id=? AND deleted=0", [$device_id])->fetchAll();
                if (!$ports) throw new Exception("No ports found or device unknown.");
                
                echo json_encode(['status' => 'ok', 'ports' => $ports]);
                break;

            // Get a single port info
            case 'port':
                if (!$device_id) throw new Exception("Missing device id");
                if (!$port_id && !$index) throw new Exception("No port specified. Use: '&port_id=[id] or &index=[port_index]");
                if ($port_id) {
                    $port = $db->query("SELECT * FROM ports WHERE device_id=? AND port_id=? AND deleted=0", [$device_id, $port_id])->fetch();
                } elseif ($index) {
                    $port = $db->query("SELECT * FROM ports WHERE device_id=? AND ifIndex=? AND deleted=0", [$device_id, $index])->fetch();
                } else {
                    throw new Exception("No port specified. Use: '&port_id=[id] or &index=[port_index]");
                }
                
                if (!$port) throw new Exception("Port not found");

                echo json_encode(['status' => 'ok', 'port' => $port]);
                break;
            // List all sensors for a device
            case 'sensors':
                if (!$device_id) throw new Exception("Missing device id");
                $sensors = $db->query("SELECT * FROM sensors WHERE device_id=?", [$device_id])->fetchAll();
                if (!$sensors) throw new Exception("No sensors found");
                
                echo json_encode(['status' => 'ok', 'sensors' => $sensors]);
                break;

            // Get a single sensor info
            case 'sensor':
                if (!$device_id) throw new Exception("Missing device id");
                if (!$sensor_id && !$index && !$sensor_type) throw new Exception("No sensor specified. Use: '&sensor_id=[id] or &index=[sensor_index] or &sensor_type=[sensor_type]");
                if ($sensor_id) {
                    $sensor = $db->query("SELECT * FROM sensors WHERE device_id=? AND sensor_id=?", [$device_id, $sensor_id])->fetch();
                } elseif($sensor_type) {
                    $sensor = $db->query("SELECT * FROM sensors WHERE device_id=? AND sensor_type=?", [$device_id, $sensor_type])->fetch();
                } elseif ($index) {
                    $sensor = $db->query("SELECT * FROM sensors WHERE device_id=? AND sensor_index=?", [$device_id, $index])->fetch();
                } else {
                    throw new Exception("No sensor specified. Use: '&sensor_id=[id] or &index=[sensor_index]");
                }
                
                if (!$sensor) throw new Exception("sensor not found");

                echo json_encode(['status' => 'ok', 'sensor' => $sensor]);
                break;
            // Get a graph for a sensor/port
            case 'graph':
                if (!$device_id) throw new Exception("Missing device id");

                // Load Observium environment
                require_once '/opt/observium/includes/observium.inc.php';

                // Input parameters
                $graph_type = strtolower($_GET['graph_type'] ?? 'sensor'); // sensor / port / processor / mempool
                $timescale  = $_GET['timescale'] ?? '1d';
                $width      = $_GET['width'] ?? 600;
                $height     = $_GET['height'] ?? 200;
                $from       = $_GET['from'] ?? "now-" . $timescale;
                $to         = $_GET['to'] ?? "now";
                $legend     = $_GET['legend'] ?? 'no';
                $bg         = $_GET['bg'] ?? 'ffffff';

                $graph_array = [
                    'from'   => $from,
                    'to'     => $to,
                    'width'  => $width,
                    'height' => $height,
                    'legend' => $legend,
                    'bg'     => $bg,
                ];

                switch ($graph_type) {
                    case 'port':
                        if (!$port_id) throw new Exception("Missing port_id for port graph");
                        $port = $db->query("SELECT * FROM ports WHERE device_id=? AND port_id=?", [$device_id, $port_id])->fetch();
                        if (!$port) throw new Exception("Port not found");

                        $graph_array['type'] = 'port_bits';
                        $graph_array['id']   = $port['port_id'];
                        break;

                    case 'sensor':
                        if (!$sensor_id) throw new Exception("Missing sensor_id for sensor graph");
                        $sensor = $db->query("SELECT * FROM sensors WHERE device_id=? AND sensor_id=?", [$device_id, $sensor_id])->fetch();
                        if (!$sensor) throw new Exception("Sensor not found");

                        $graph_array['type'] = 'sensor_' . $sensor['sensor_class'];
                        $graph_array['id']   = $sensor['sensor_id'];
                        break;

                    case 'processor':
                        $proc_id = $_GET['processor_id'] ?? null;
                        if (!$proc_id) throw new Exception("Missing processor_id for processor graph");
                        $processor = $db->query("SELECT * FROM processors WHERE device_id=? AND processor_id=?", [$device_id, $proc_id])->fetch();
                        if (!$processor) throw new Exception("Processor not found");

                        $graph_array['type'] = 'processor_usage';
                        $graph_array['id']   = $processor['processor_id'];
                        break;

                    case 'mempool':
                        $mempool_id = $_GET['mempool_id'] ?? null;
                        if (!$mempool_id) throw new Exception("Missing mempool_id for mempool graph");
                        $mempool = $db->query("SELECT * FROM mempools WHERE device_id=? AND mempool_id=?", [$device_id, $mempool_id])->fetch();
                        if (!$mempool) throw new Exception("Mempool not found");

                        $graph_array['type'] = 'mempool_usage';
                        $graph_array['id']   = $mempool['mempool_id'];
                        break;

                    default:
                        throw new Exception("Unsupported graph_type: $graph_type");
                }

                $graph_tag = generate_graph_tag($graph_array);
                preg_match('/src="([^"]+)"/', $graph_tag, $matches);
                $graph_url = $matches[1] ?? null;
                $graph_full_url = $config['base_url'].'/graph.php?' . http_build_query($graph_array);
                $graph_full_tag = '<img id="graph-'.$device_id.'-'.$graph_array['type'].'-'.$graph_array['id'].'" src="'.$graph_full_url.'">';
                $config['allow_unauth_graphs'] = 1;

                echo json_encode([
                    'status'         => 'ok',
                    'graph' => [
                        'graph_type'     => $graph_type,
                        'graph_url'      => $graph_url,
                        'graph_full_url' => $graph_full_url,
                        'img_tag'        => $graph_tag,
                        'img_full_tag'   => $graph_full_tag,
                        'params'         => $graph_array
                    ]
                ]);
                break;

            default:
                throw new Exception("Unknown action: $action");
        }
    } catch (Exception $e) {
        // $logger->error("API error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 
                        'message' => 'No action set.', 
                        'usage1' => '/api.php?action=devices',
                        'usage2' => '/api.php?action=device&device_id=1',
                        'usage3' => '/api.php?action=ports&device_id=1',
                        'usage4' => '/api.php?action=port&device_id=1&port_id=1',
                        'usage5' => '/api.php?action=port&device_id=1&index=1',
                        'usage6' => '/api.php?action=sensors&device_id=1',
                        'usage7' => '/api.php?action=sensor&device_id=1&sensor_id=1',
                        'usage8' => '/api.php?action=sensor&device_id=1&sensor_type=temperature',
                        'usage9' => '/api.php?action=sensor&device_id=1&index=1',
                        'usage10' => '/api.php?action=graph&device_id=1&graph_type=port&port_id=1'
                     ]);
}
