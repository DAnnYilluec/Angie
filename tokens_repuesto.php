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

function getFollowers($accessToken, $broadcasterId,$clientId)
{
    $url = "https://api.twitch.tv/helix/channels/followers?broadcaster_id=" . $broadcasterId;

    $options = array(
        'http' => array(
            'header' => "Authorization: Bearer " . $accessToken . "\r\n" .
                "Client-Id: " . $clientId . "\r\n",
            'method' => 'GET',
        ),
    );

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        // Handle error
        return null;
    }

    $followers = json_decode($result, true);
    return $followers['data'];
}

function displayFollowersTable($followers) {
    if (!$followers) {
        echo "No followers found or error fetching followers.";
        return;
    }

    echo "<h1>Followers</h1>";
    echo "<table border='1'>";
    echo "<tr><th>User ID</th><th>User Name</th><th>Followed At</th></tr>";
    foreach ($followers as $follower) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($follower['user_id']) . "</td>";
        echo "<td>" . htmlspecialchars($follower['user_name']) . "</td>";
        echo "<td>" . htmlspecialchars($follower['followed_at']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Función para almacenar seguidores en la base de datos
// Función para almacenar seguidores en la base de datos
// Función para almacenar seguidores en la base de datos
function storeFollowersInDatabase($accessToken, $broadcasterId, $clientId, $followers) {
    if (!$followers) {
        echo "No followers found or error fetching followers.";
        return;
    }

    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=angieBD', 'root', '');

    foreach ($followers as $follower) {
        $userId = $follower['user_id'];
        $userName = $follower['user_name'];
        $followedAt = $follower['followed_at'];

        // Verificar si el seguidor ya existe en la base de datos
        $stmt = $pdo->prepare("SELECT * FROM seguidores WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        $existingFollower = $stmt->fetch();

        if (!$existingFollower) {
            // El seguidor no existe, se puede insertar en la base de datos
            $stmt = $pdo->prepare("INSERT INTO seguidores (user_id, user_name, followed_at) VALUES (:user_id, :user_name, :followed_at)");
            // Ejecutar la consulta con los datos del seguidor actual
            $stmt->execute([
                ':user_id' => $userId,
                ':user_name' => $userName,
                ':followed_at' => $followedAt,
            ]);
        } else {
            // El seguidor ya existe, puedes ignorar o manejar el caso según sea necesario
            echo "El seguidor con ID $userId ya existe en la base de datos.";
        }
    }

    echo "Followers stored successfully.";
}





