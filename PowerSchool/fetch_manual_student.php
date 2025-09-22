<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
include('functions.php');
include_once('dbClass.php');
$type = 'enrolled_state_id_students';
$url = 'https://jeffersonco.powerschool.com';
$clientID = 'd97a2fbf-2f7a-4aad-9030-1587312bb885';
$clientSecret = '72aa4c4a-b877-43c4-801b-48c2fc2cf123';
$accessTokenKey = "YzFiMjIyMmItOWEzYS00YzdkLWFmOTItMWMyNjA5OWMzODQ0OmE3ZTBmZGQ0LWQ4OWMtNDUzNi1iZjE5LWIyZDU3YzYyYWY0Yg==";

$accessToken = getAccessToken($url, $clientID, $clientSecret);
$accessTokenArray = json_decode($accessToken);


$objDB = new MySQLCN; 

if (!empty($accessTokenArray)) {
    $accessTokenKey = $accessTokenArray->access_token;
    $accessTokenType = $accessTokenArray->token_type;
    $accessTokenExpiresIn = $accessTokenArray->expires_in;
    
    if (isset($accessTokenKey) && !empty($accessTokenKey)) {
        $powerSchoolRecords = getPowerSchoolRecords($type, $accessTokenKey, $url, []);
    }
} 




?>