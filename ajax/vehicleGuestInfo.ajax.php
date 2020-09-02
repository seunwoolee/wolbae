<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// ERP서버 변경된 고객정보 받아오는부분 ( JSON )
// ERP서버가 준비가 안된상태라, 로컬서버에서 데이터 발생시켰습니다.
// ERP서버가 준비가 완료되면 이페이지는 삭제처리합니다.
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/mysql.inc.php";
include "../inc/user.inc.php";
include "../inc/json.inc.php";
include "../inc/lib.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//Json Class
$json = new Json();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db->que = " SELECT 
					 ve_guestId			AS guestId
					,ve_guestName		AS name
					,ve_guestJusoSubId	AS jusoSubId
					,ve_guestJuso		AS juso
					,ve_guestLat		AS lat
					,ve_guestLon		AS lon
					,ve_updateDate		AS updateDate 
						FROM Erp_vehicleGuestInfo ";
$db->query();

/*
$url = 'http://app.mylawyer.pe.kr/admin/test.php';
$content = file_get_contents($url);
$content = utf8_encode($content);
$content = stripslashes($content);
*/

LIB::PLog($content);
//$jsonContent = json_decode($content, true);

$json->add("Erp_vehicleGuestInfo", $db->getRows());
$json->result["resultMessage"] = "고객정보데이터";
echo $json->getResult();
$db->close();
exit;

?>