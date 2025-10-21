<?php

$config = [];

include '../../config.php';

$config['db'] = [
        'host'   => $config['db_host'],
        'dbname' => $config['db_name'],
        'user'   => $config['db_user'],
        'pass'   => $config['db_pass'] 
];

// make sure to set $config['allow_unauth_graphs'] = 1; in the observium config.php

return $config;

?>
