<?php

// Conexión a la base de datos
$pdo = new PDO('mysql:host=localhost;dbname=angieBD', 'root', '');

// Preparar la consulta SQL para obtener los nombres de los seguidores
$stmt = $pdo->query("SELECT user_name FROM suscriptores");

// Obtener los nombres de los seguidores
$seguidores = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $seguidores[] = $row['user_name'];
}

// Devolver los nombres de los seguidores como JSON
echo json_encode($seguidores);


