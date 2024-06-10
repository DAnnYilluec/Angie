<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ruleta</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="estilos.css" rel="stylesheet" type="text/css" media="all">
    <link href="styleSu.css" rel="stylesheet" type="text/css" media="all">
    <link rel="icon" href="angie.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/801222a0d1.js" crossorigin="anonymous"></script>
</head>
<body id="top">
<!-- MENU -->
<header id="header" class="hoc clear" style="
    background-color: darkolivegreen;
    padding-left: 20px;
    padding-right: 20px;">
    <div id="logo" class="fl_left" style="margin-right: 60px;">
        <h1><a href="index.php">RULETA DE ANGIE404_</a></h1>
    </div>
    <nav id="mainav" class="fl_right">
        <ul class="clear">
            <li><a href="RuletaInfantil/index.html">Ruleta infantil</a></li>
            <li><a href="Aleatorio.php">Ruleta Chat</a></li>
            <li class="active"><a href="Subs.php">Ruleta Clasista</a></li>
            <li><a href="Seguidores.php">Ver Seguidores</a></li>
        </ul>
    </nav>
</header>
<button onclick="girarRuleta()" id="spin">Girar Ruleta</button>
<div id="ruletilla"><img src="rulette33.png" id="ruleta"></div>
<div class="tarjetilla">
<h2 class="fl_left" style="margin-right: 60px;">SE LLEVAN PREMIO</h2>
    <div id="resultado"></div>
    <div id="resultadom"></div>
</div>
<audio id="sonidoGirar" src="sonido_girar.mp3"></audio>
<audio id="sonidoDetener" src="sonido_detener.mp3"></audio>
<audio id="sonidoSorpresa" src="Y2meta.app%20-%20PARTE%203.%20Mejores%20FRASES%20y%20Momentos%20de%20La%20Que%20Se%20Avecina%20(LQSA)%20(128%20kbps)%20(1).mp3"></audio>
<div class="tarjetilla">
    <h2 class="fl_left" style="margin-right: 60px;">YA SE LA HAN LLEVADO</h2>
    <div id="resultado2"></div>
</div>
<script src="scriptSu.js"></script>
</body>
</html>
