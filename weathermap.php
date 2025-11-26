<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'includes/functions.php';

$continue = $map_dir = $map_file = false;

if (!isset($map)) {
    $map = $_GET['map'] ?? null;
}

if ($map) {
    $map_name = $map;
    $map_file = $map.'.json';
    $map_dir  = './maps';
    if (search_file(getcwd().DIRECTORY_SEPARATOR.$map_dir, $map_file)) {
        $continue = true;
    } 
    $map_file = $map_dir.DIRECTORY_SEPARATOR.$map_file;
    $map_data = json_decode(file_get_contents($map_file), true);
    $canvas_id = $map_data['Config']['canvas_id'];
}

// error output.
if (!$continue) {
    ?><strong>Unable to find map <?php if($map_dir && $map_file) { echo('at: '.getcwd().DIRECTORY_SEPARATOR.$map_dir.$map_file); } ?></strong>
    <?php
    exit();
}

// add in the canvas to load the weathermap
include 'canvas.php';

// Temporarily include the README
?>
<div id="readme" style="font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc; margin-top:10px">
<?php
include 'maps/map_README.md';
?>
</div>
