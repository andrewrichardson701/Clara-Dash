<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#ffffff">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" id="google-font">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="https://adobe-fonts.github.io/source-code-pro/source-code-pro.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css">
</head>
<body>

    <?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

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
        ?>
        <br><strong>Unable to find map <?php if($map_dir && $map_file) { echo('at: '.getcwd().DIRECTORY_SEPARATOR.$map_dir.$map_file); } ?></strong><br><br>
        <p>Available maps:</p>
        <ul>
        <?php
            foreach (scandir(getcwd().DIRECTORY_SEPARATOR.'maps') as $file) {
                if (!is_dir(getcwd().DIRECTORY_SEPARATOR.'maps'.DIRECTORY_SEPARATOR.$file) && pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                    $file = pathinfo($file, PATHINFO_FILENAME);
                    echo('<li><a href="weathermap.php?map='.$file.'">'.$file.'</a></li>');
                }
            }
        ?>
        </ul>
        <?php
        exit();
    }
    ?>
    <div id="canvas-content">
    <?php
        // add in the canvas to load the weathermap
        include 'canvas.php';

        // Temporarily include the README
        ?>
        <div id="readme" class="container well-nopad bg-dark" style="font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc; margin-top:10px">
            README file: 'maps/map_README.md' <br><br>
            <?php
            include 'maps/map_README.md';
            ?>
        </div>
    </div>
</body>
