<html>
    <head>
        <title>PDU Monitoring Dashboard</title>
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
        <div class="content container">
            <div id="title" class="container" style="padding-top:20px">
                <h1>PDU Monitoring Dashboard</h1>
            </div>
            
            <hr style="border-color: lightgrey; background-color: lightgrey">

            <div id="sites" class="container">     
                <h3>Sites</h3>
                <div class="row">
                    <div class="col well-nopad bg-dark clickable" style="margin: 5px" onclick="(window.location.href='sites.php?site=hoddesdon')">
                        <h4><strong>Hoddesdon - nLighten</strong></h4>
                        <p>Hoddesdon site is an <strong>nLighten</strong> data center, covered by the <strong>London DC team</strong>.
                            <br><br>
                            Address:<br>
                            nLighten, Unit 7, Geddings Road, Hoddesdon, EN11 0NT
                        </p>
                    </div>
                    <div class="col well-nopad bg-dark clickable" style="margin: 5px" onclick="(window.location.href='sites.php?site=telehouse')">
                        <h4><strong>Telehouse - London</strong></h4>
                        <p>Telehouse London is covered by the <strong>London DC team</strong>. 
                            <br><br>
                            Address:<br>
                            Telehouse London, Coriander Avenue, London, E14 2AA
                        </p>
                    </div>
                    <div class="col well-nopad bg-dark clickable" style="margin: 5px" onclick="(window.location.href='sites.php?site=gloucester')">
                        <h4><strong>Gloucester - Indectron</strong></h4>
                        <p>Indectron is the primary data centre in Gloucester, covered by the <strong>Gloucester DC team</strong>.
                        <br><br>
                        Address: <br>
                        Indectron, Shield House, 2 Crest Way, Barnwood, Gloucester, GL4 3DH
                        </p>
                    </div>
                    <div class="col well-nopad bg-dark clickable" style="margin: 5px" onclick="(window.location.href='sites.php?site=equnix')">
                        <h4><strong>Equinix - LD5</strong></h4>
                        <p>Equinix in Slough, LD5, is covered primarily by the Gloucester team.</p>
                            <br><br>
                            Address: <br>
                            Equinix LD5, Slough Trading Estate, 8 Buckingham Ave, Slough SL1 4AX
                        </p>
                    </div>
                </div>
            </div>

            <hr style="border-color: lightgrey; background-color: lightgrey">

            <div id="weathermaps" class="container">     
                <h3>Weathermaps</h3>
                <table class="table table-dark centertable" style="margin-top:10px">
                    <thead style="text-align: center; white-space: nowrap;">
                        <tr>
                            <th>Site</th>
                            <th>Suite</th>
                            <th>Link</th>
                        </tr>
                    </thead>
                    <tbody style="text-align: center; white-space: nowrap;">
                        <tr>
                            <td>Hoddesdon</td>
                            <td>Suite 52</td>
                            <td><a href="weathermap.php?map=hod_52">View Weathermap</a></td>
                        </tr>
                        <tr>
                            <td>Hoddesdon</td>
                            <td>Suite 42</td>
                            <td><a href="weathermap.php?map=hod_42">View Weathermap</a></td>
                        </tr>
                        <tr>
                            <td>Hoddesdon</td>
                            <td>Suite 21</td>
                            <td><a href="weathermap.php?map=hod_21">View Weathermap</a></td>
                        </tr>
                        <tr>
                            <td>Hoddesdon</td>
                            <td>Suite 61</td>
                            <td><a href="weathermap.php?map=hod_61">View Weathermap</a></td>
                        </tr>
                        <tr>
                            <td>Hoddesdon</td>
                            <td>All</td>
                            <td><a href="weathermap.php?map=hod_all">View Weathermap</a></td>
                        </tr>
                        <tr>
                            <td>Hoddesdon</td>
                            <td>Redstor</td>
                            <td><a href="weathermap.php?map=hod_redstor">View Weathermap</a></td>
                        </tr>
                        <tr>
                            <td>Hoddesdon</td>
                            <td>Colocation</td>
                            <td><a href="weathermap.php?map=hod_colocation">View Weathermap</a></td>
                        </tr>
                        <tr>
                            <td>Telehouse</td>
                            <td>DFM6</td>
                            <td><a href="weathermap.php?map=th_dfm6">View Weathermap</a></td>
                        </tr>
                        <tr>
                            <td>Telehouse</td>
                            <td>Other</td>
                            <td><a href="weathermap.php?map=th_other">View Weathermap</a></td>
                        </tr>
                        <tr>
                            <td colspan=100% style="background:black"></td>
                        </tr>
                        <tr>
                            <td>Gloucester</td>
                            <td>DH1</td>
                            <td><a href="weathermap.php?map=glo_dh1">View Weathermap</a></td>
                        </tr>
                        <tr>
                            <td>LD5</td>
                            <td>LD5:01:1MC130</td>
                            <td><a href="weathermap.php?map=ld5_01-1mc130">View Weathermap</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>  
            
    </body>
</html>