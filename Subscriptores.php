<?php
include "tokens.php";
$clientId = 'tism6qcmpbhwlguncwxkf2x4roubvs';
$clientSecret = '6zivgrn1paktk0er5xbxm6gsv3zt5q';
$channelId = '1029356313';

$accessToken = checkAndRefreshToken($clientId, $clientSecret);
if ($accessToken) {
        subs($clientId,$channelId,$accessToken);
} else {
    echo "Error obtaining access token.";
}

?>

