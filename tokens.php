<?php
function storeTokensInDatabase($accessToken, $refreshToken, $expiresIn) {
    $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=angieBD', 'root', '');
    $stmt = $pdo->prepare("INSERT INTO oauth_tokens (access_token, refresh_token, expires_at) VALUES (:access_token, :refresh_token, :expires_at)");
    $stmt->execute([
        ':access_token' => $accessToken,
        ':refresh_token' => $refreshToken,
        ':expires_at' => $expiresAt,
    ]);
}
function checkAndRefreshToken($clientId, $clientSecret) {
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=angieBD', 'root', '');
    $stmt = $pdo->query("SELECT * FROM oauth_tokens ORDER BY id DESC LIMIT 1");
    $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tokenData) {
        // No tokens found, you should call getInitialTokens() first
        return null;
    }

    $expiresAt = new DateTime($tokenData['expires_at']);
    $now = new DateTime();

    if ($now >= $expiresAt) {
        // Token has expired, refresh it
        $url = 'https://id.twitch.tv/oauth2/token';
        $data = array(
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $tokenData['refresh_token'],
            'grant_type' => 'refresh_token',
        );

        $options = array(
            'http' => array(
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) {
            // Handle error
            return null;
        }

        $newTokens = json_decode($result, true);

        // Update tokens in the database
        updateTokensInDatabase($newTokens['access_token'], $newTokens['refresh_token'], $newTokens['expires_in']);

        return $newTokens['access_token'];
    } else {
        // Token is still valid
        return $tokenData['access_token'];
    }
}
function updateTokensInDatabase($accessToken, $refreshToken, $expiresIn) {
    $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=angieBD', 'root', '');
    $stmt = $pdo->prepare("UPDATE oauth_tokens SET access_token = :access_token, refresh_token = :refresh_token, expires_at = :expires_at ORDER BY id DESC LIMIT 1");
    $stmt->execute([
        ':access_token' => $accessToken,
        ':refresh_token' => $refreshToken,
        ':expires_at' => $expiresAt,
    ]);
}
function getSubscribers($accessToken, $broadcasterId, $clientId) {
    $url = "https://api.twitch.tv/helix/subscriptions?broadcaster_id=" . $broadcasterId;
    $subscribers = [];
    $cursor = null;

    do {
        $fullUrl = $url;
        if ($cursor) {
            $fullUrl .= "&after=" . $cursor;
        }

        $options = array(
            'http' => array(
                'header' => "Authorization: Bearer " . $accessToken . "\r\n" .
                    "Client-Id: " . $clientId . "\r\n",
                'method' => 'GET',
            ),
        );

        $context = stream_context_create($options);
        $result = file_get_contents($fullUrl, false, $context);
        if ($result === FALSE) {
            // Handle error
            return null;
        }

        $response = json_decode($result, true);
        if (isset($response['data'])) {
            $subscribers = array_merge($subscribers, $response['data']);
        }

        $cursor = $response['pagination']['cursor'] ?? null;

    } while ($cursor);

    return $subscribers;
}

function displaySubscribersTable($subscribers) {
    if (!$subscribers) {
        echo "No subscribers found or error fetching subscribers.";
        return;
    }

    echo "<h1>La Gente Inteligente (subs)</h1>";
    echo "<table border='1'>";
    echo "<tr><th>User ID</th><th>User Name</th><th>¿Sub Regalada? 1->Sí</th></tr>";
    foreach ($subscribers as $subscriber) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($subscriber['user_id']) . "</td>";
        echo "<td>" . htmlspecialchars($subscriber['user_name']) . "</td>";
        echo "<td>" . htmlspecialchars($subscriber['is_gift']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Función para almacenar suscriptores en la base de datos
function storeSubscribersInDatabase($accessToken, $broadcasterId, $clientId, $subscribers) {
    if (!$subscribers) {
        echo "No subscribers found or error fetching subscribers.";
        return;
    }

    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=angieBD', 'root', '');

    foreach ($subscribers as $subscriber) {
        $userId = $subscriber['user_id'];
        $userName = $subscriber['user_name'];
        $subscribedAt = $subscriber['is_gift'];

        // Verificar si el suscriptor ya existe en la base de datos
        $stmt = $pdo->prepare("SELECT * FROM suscriptores WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        $existingSubscriber = $stmt->fetch();

        if (!$existingSubscriber) {
            // El suscriptor no existe, se puede insertar en la base de datos
            $stmt = $pdo->prepare("INSERT INTO suscriptores (user_id, user_name, is_gift) VALUES (:user_id, :user_name, :is_gift)");
            // Ejecutar la consulta con los datos del suscriptor actual
            $stmt->execute([
                ':user_id' => $userId,
                ':user_name' => $userName,
                ':is_gift' => $subscribedAt,
            ]);
        }
    }

    echo "Subscribers stored successfully.";
}


//---------------------------------------------------------------------------------------------------------------------------------

function subs($channelId, $accessToken, $clientId) {
    $subs = [];
    $cursor = null;

    do {
        // Construir la URL con el cursor si está presente
        $url = 'https://api.twitch.tv/helix/subscriptions?broadcaster_id=' . $channelId . '&first=100';
        if ($cursor) {
            $url .= '&after=' . $cursor;
        }

        // Configurar cURL
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Client-ID: ' . $clientId,
                'Authorization: Bearer ' . $accessToken
            ),
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            die('Error: ' . curl_error($curl));
        }
        curl_close($curl);

        $data = json_decode($response, true);

        if (isset($data['data']) && !empty($data['data'])) {
            $subs = array_merge($subs, $data['data']);
        }

        // Obtener el cursor para la siguiente página, si existe
        $cursor = isset($data['pagination']['cursor']) ? $data['pagination']['cursor'] : null;
    } while ($cursor);

    echo "Lista de suscriptores:<br>";
    foreach ($subs as $sub) {
        echo $sub['user_name'] . "<br>";
    }
}





