<html>
    <head>
        <title>PDU Mon - Sites</title>
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
        if (isset($_GET['site'])) {
            $site = $_GET['site'];
            if (in_array($site, ['hoddesdon', 'telehouse', 'gloucester', 'equnix'])) {
                include('includes/' . $site . '.php');
            } else {
                echo '<div class="container"><h2>Invalid site specified.</h2></div>';
            }
        } else {
            header("Location: index.php");
            exit();
        }        
        ?>   
    </body>
</html>