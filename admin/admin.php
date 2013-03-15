<?php
ini_set('use_only_cookies', 'On');
ini_set('session.use_trans_sid', 'Off');
session_start();
if ($_SESSION['admin'] != 'true') {
    header('Location:index.php?error=1');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Panel sterowania Administratora</title>
        <link rel="icon" type="image/x-icon" href="../images/icon32.png" />
        <link rel="stylesheet" href="../css/simplex.css" />
        <link rel="stylesheet" href="css/admin.css" />
        <link rel="stylesheet"
              href="../js/development-bundle/themes/smoothness/jquery-ui-1.8.16.custom.css" />
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script src="../js/js/jquery-ui-1.8.16.custom.min.js"
        type="text/javascript"></script>
        <script type="text/javascript" src="../js/jquery.blockui.js"></script>
        <script type="text/javascript" src="js/adminsimplex.js"></script>
    </head>
    <body>
        <img id="bg" alt="Background image" src="../images/back.jpg" />
        <div id="content">
            <div id="wrapper"></div>
            <div id="header">
                <form id="logoutform" action="logout.php?logout=1">
                    <button id="logout">Wyloguj</button>
                </form>
            </div>
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1">Włącz/Wyłącz</a></li>
                </ul>
                <div id="tabs-1">
                    <div style="width: 116px; height: 200px; margin: 0px auto; margin-top: 100px;">
                        <button id="standby">W</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
