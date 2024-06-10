<?php
include 'tokens.php';
include 'chat.php';
$clientId = 'tism6qcmpbhwlguncwxkf2x4roubvs';
$clientSecret = '6zivgrn1paktk0er5xbxm6gsv3zt5q';
$broadcasterId = '473076837';
$nickname = 'dannyilluec';
$channel = 'ItsNa7e';

$accessToken = checkAndRefreshToken($clientId, $clientSecret);
if ($accessToken) {
connectToTwitchChat($accessToken, $nickname, $channel);
} else {
    echo "Error obtaining access token.";
}
?>
