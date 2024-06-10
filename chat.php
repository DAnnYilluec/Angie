<?php
function connectToTwitchChat($token, $nickname, $channel) {
    set_time_limit(0);  // Establecer tiempo de ejecución ilimitado

    $token2='oauth:'.$token;
    $server = 'irc.chat.twitch.tv';
    $port = 6667;

    $socket = fsockopen($server, $port, $errno, $errstr, 30);
    if (!$socket) {
        die("No se puede conectar a Twitch IRC server: $errstr ($errno)");
    }
    echo "Conectado a Twitch IRC server.<br>";

    // Enviar el mensaje PASS
    fwrite($socket, "PASS $token2\r\n");
    echo "Enviado PASS.<br>";

    // Enviar el mensaje NICK
    fwrite($socket, "NICK $nickname\r\n");
    echo "Enviado NICK.<br>";

    // Unirse al canal
    fwrite($socket, "JOIN #$channel\r\n");
    echo "Intentando unirse al canal #$channel.<br>";

    $users = [];
    $startTime = microtime(true);  // Tiempo inicial en segundos y microsegundos

    // Escuchar el mensaje de bienvenida del servidor y la confirmación de unión
    while (!feof($socket)) {
        $response = fgets($socket);
        echo "Recibido: $response<br>";

        // Ping-Pong para mantener la conexión viva
        if (strpos($response, 'PING') === 0) {
            fwrite($socket, 'PONG ' . substr($response, 5) . "\r\n");
            echo "Enviado PONG.<br>";
        }

        // Verificar la confirmación de unión al canal
        if (strpos($response, "376") !== false || strpos($response, "422") !== false) {
            echo "Unido al servidor IRC.<br>";
        }

        // Capturar la lista inicial de usuarios
        if (strpos($response, "353") !== false) {
            preg_match('/:.* 353 .* = #.* :(.+)/', $response, $matches);
            if (isset($matches[1])) {
                $initial_users = explode(' ', $matches[1]);
                foreach ($initial_users as $user) {
                    if (!in_array($user, $users)) {
                        $users[] = $user;
                    }
                }
            }
        }

        // Capturar usuarios que se unan al canal después
        if (strpos($response, "JOIN #$channel") !== false) {
            preg_match('/:(\w+)!/', $response, $matches);
            if (isset($matches[1])) {
                $user = $matches[1];
                if (!in_array($user, $users)) {
                    $users[] = $user;
                    echo "Nuevo usuario en el chat: $user<br>";
                }
            }
        }

        // Capturar nombres de usuarios que envían mensajes en el chat
        if (strpos($response, "PRIVMSG #$channel") !== false) {
            preg_match('/:(\w+)!.* PRIVMSG/', $response, $matches);
            if (isset($matches[1])) {
                $user = $matches[1];
                if (!in_array($user, $users)) {
                    $users[] = $user;
                    echo "Usuario que envió un mensaje: $user<br>";
                }
            }
        }

        // Verificar si se ha superado un minuto de ejecución
        $elapsedTime = microtime(true) - $startTime;
        if ($elapsedTime >= 60) {
            break;
        }
    }

    // Mostrar la lista completa de usuarios
    echo "Usuarios en el chat:<br>";
    foreach ($users as $user) {
        echo "$user<br>";
    }

    // Cerrar la conexión
    fclose($socket);
    echo "Conexión cerrada.<br>";
}
?>
