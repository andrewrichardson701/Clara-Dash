<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/functions.php';

$continue = $map_dir = $map_file = false;
if (isset($_GET['map'])) {
    $map_name = $_GET['map'];
    $map_file = $_GET['map'].'.json';
    $map_dir  = './maps';
    if (search_file(getcwd().DIRECTORY_SEPARATOR.$map_dir, $map_file)) {
        $continue = true;
    } 
}

// error output.
if (!$continue) {
    ?><strong>Unable to find map <?php if($map_dir && $map_file) { echo('at: '.getcwd().DIRECTORY_SEPARATOR.$map_dir.$map_file); } ?></strong>
    <?php
    exit();
}

?>

<script>
    var map_file = '<?php echo($map_dir.DIRECTORY_SEPARATOR.$map_file) ?>';
</script>


<?php

// add in the canvas to load the weathermap
include 'canvas.php';

// Temporarily include the README
?>
<div id="readme" style="font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc; margin-top:10px">
<?php
include 'maps/map_README.md';
?>
</div>
