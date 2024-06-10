<?php
$clientId = 'tism6qcmpbhwlguncwxkf2x4roubvs';
$broadcasterId = '1029356313';


$url = 'https://api.twitch.tv/helix/subscriptions/user';
$params = [
    'broadcaster_id' => $broadcasterId,
    'user_id' => '473076837' // El ID del seguidor que deseas verificar
];

$queryString = http_build_query($params);
$requestUrl = $url . '?' . $queryString;

$options = [
    CURLOPT_URL => $requestUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Client-ID: ' . $clientId,
        'Authorization: Bearer ' . $accessToken,
    ],
];

$ch = curl_init();
curl_setopt_array($ch, $options);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['data']) && !empty($data['data'])) {
    // El seguidor es también un suscriptor
    echo "El usuario es un suscriptor.";
} else {
    // El seguidor no es un suscriptor
    echo "El usuario no es un suscriptor.";
}
?>
