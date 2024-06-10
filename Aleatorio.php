<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ruleta</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="estilos.css" rel="stylesheet" type="text/css" media="all">
    <link href="styleAl.css" rel="stylesheet" type="text/css" media="all">
    <link rel="icon" href="angie.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/801222a0d1.js" crossorigin="anonymous"></script>
</head>
<body id="top">
<!-- MENU -->
<header id="header" class="hoc clear" style="
    background-color: darkslategray;
    padding-left: 20px;
    padding-right: 20px;">
    <div id="logo" class="fl_left" style="margin-right: 60px;">
        <h1><a href="index.php">RULETA DE ANGIE404_</a></h1>
    </div>
    <nav id="mainav" class="fl_right">
        <ul class="clear">
            <li><a href="RuletaInfantil/index.html">Ruleta infantil</a></li>
            <li class="active"><a href="Aleatorio.php">Ruleta Chat</a></li>
            <li><a href="Subs.php">Ruleta Clasista</a></li>
            <li><a href="Seguidores.php">Ver Seguidores</a></li>
        </ul>
    </nav>
</header>
<?php
include "tokens.php";
include "chat.php";
$clientId = 'tism6qcmpbhwlguncwxkf2x4roubvs';
$clientSecret = '6zivgrn1paktk0er5xbxm6gsv3zt5q';
$channelId = '1029356313';
$nickname ="DAnnYilluec";
$channel = "angie404_";
$accessToken = checkAndRefreshToken($clientId, $clientSecret);
if ($accessToken) {
    connectToTwitchChat($accessToken,$nickname,$channel);
} else {
    echo "Error obtaining access token.";
}
?>
<button onclick="girarRuleta()" id="spin">Girar Ruleta</button>
<div id="ruletilla"><img src="rulette2.png" id="ruleta"></div>
<div class="tarjetilla">
    <h2 class="fl_left" style="margin-right: 60px;">SE LLEVAN PREMIO</h2>
    <div id="resultado"></div>
    <div id="resultadom"></div>
</div>
<audio id="sonidoGirar" src="sonido_girar.mp3"></audio>
<audio id="sonidoDetener" src="sonido_detener.mp3"></audio>
<div class="tarjetilla">
    <h2 class="fl_left" style="margin-right: 60px;">YA SE LA HAN LLEVADO</h2>
    <div id="resultado2"></div>
</div>
<script src="scriptA.js"></script>
</body>
</html>
