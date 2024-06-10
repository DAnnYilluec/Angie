<!DOCTYPE html>
<html lang="">
<head>
    <title>Ruleta</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="estilos.css" rel="stylesheet" type="text/css" media="all">
    <link rel="icon" href="angie.png">
    <script src="https://kit.fontawesome.com/801222a0d1.js" crossorigin="anonymous"></script>
</head>
<body id="top">
<!-- MENU -->
<header id="header" class="hoc clear">
    <div id="logo" class="fl_left" style="margin-right: 60px;">
        <h1><a href="index.php">RULETA DE ANGIE404_</a></h1>
    </div>
    <nav id="mainav" class="fl_right">
        <ul class="clear">
            <li><a href="RuletaInfantil/index.html">Ruleta infantil</a></li>
            <li><a href="Aleatorio.php">Ruleta Chat</a></li>
            <li><a href="Subs.php">Ruleta Clasista</a></li>
            <li class="active"><a href="Seguidores.php">Ver Seguidores</a></li>
        </ul>
    </nav>
</header>
<?php
include "tokens.php";
// Uso del código
$clientId = 'tism6qcmpbhwlguncwxkf2x4roubvs';
$clientSecret = '6zivgrn1paktk0er5xbxm6gsv3zt5q';
$broadcasterId = '1029356313';

$accessToken = checkAndRefreshToken($clientId, $clientSecret);
if ($accessToken) {
    $followers = getSubscribers($accessToken, $broadcasterId,$clientId);
    displaySubscribersTable($followers);
    storeSubscribersInDatabase($accessToken,$broadcasterId,$clientId,$followers);
} else {
    echo "Error obtaining access token.";
}
?>
</body>
</html>
