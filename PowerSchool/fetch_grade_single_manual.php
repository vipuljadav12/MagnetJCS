<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
include('functions.php');
include_once('dbClass.php');

$url = 'https://jeffersonco.powerschool.com';
$clientID = 'd97a2fbf-2f7a-4aad-9030-1587312bb885';
$clientSecret = '72aa4c4a-b877-43c4-801b-48c2fc2cf123';

$type = 'student_storedgrades';

$objDB = new MySQLCN; 


$accessToken = getAccessToken($url, $clientID, $clientSecret);
$accessTokenArray = json_decode($accessToken);

if (!empty($accessTokenArray)) {
    $accessTokenKey = $accessTokenArray->access_token;
    $accessTokenType = $accessTokenArray->token_type;
    $accessTokenExpiresIn = $accessTokenArray->expires_in;
    
    if (isset($accessTokenKey) && !empty($accessTokenKey)) {
        $SQL = "SELECT dcid FROM student WHERE stateID = '1945702023'";
            $rsS = $objDB->select($SQL);
            $powerSchoolRecords = getPowerSchoolRecords($type, $accessTokenKey, $url, array("submission_id"=>123, "student_id"=>$rsS[0]['dcid']));
            exit;
            $SQL = "UPDATE submissions SET  grade_exists = 'Y' WHERE id = '".$rs[$i]['id']."'";
            $rs = $objDB->sql_query($SQL);

        $SQL = "SELECT id, student_id FROM submissions WHERE id = ".$_REQUEST['id'];
        $rs = $objDB->select($SQL);

        for($i=0; $i < count($rs); $i++)
        {

            $SQL = "SELECT dcid FROM student WHERE stateID = '".$rs[$i]['student_id']."'";
            $rsS = $objDB->select($SQL);
            $powerSchoolRecords = getPowerSchoolRecords($type, $accessTokenKey, $url, array("submission_id"=>$rs[$i]['id'], "student_id"=>$rsS[0]['dcid']));

            $SQL = "UPDATE submissions SET  grade_exists = 'Y' WHERE id = '".$rs[$i]['id']."'";
            $rs = $objDB->sql_query($SQL);
        }
    }
} else {
    echo "Invalid Token";
}
