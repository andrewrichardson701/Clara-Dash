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

include 'canvas_test.php';

?>
