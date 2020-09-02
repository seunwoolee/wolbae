<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// ERP서버 변경된 고객배송정보 받아오는부분 ( JSON )
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
$deliveryDate = $_POST["deliveryDate"];
//$vo_deliveryDate = "2017-05-01 00:00:00";
$locationId = $_POST["locationId"];
$meridiemType = $_POST["meridiemType"];

$db->que = " SELECT vo_seq AS seq
					,vo_accno			AS accno
					,vo_locationId		AS locationId
					,vo_deliveryDate	AS deliveryDate
					,vo_meridiemType	AS meridiemType
					,vo_guestId			AS guestId
					,vo_guestName		AS name
					,vo_guestTel		AS guestTel
					,vo_guestJusoSubId	AS jusoSubId
					,vo_guestJuso		AS juso
					,vo_pay				AS pay
					,vo_gsu				AS gsu
					,vo_guestLat		AS lat
					,vo_guestLon		AS lon
						FROM Erp_vehicleGuestOrderData WHERE vo_locationId='0' 
						AND vo_deliveryDate='2017-05-01' AND vo_meridiemType='0' 
						AND vo_locationId='0'".
						" ORDER BY vo_seq asc";
$db->query();

$json->add("Erp_vehicleGuestOrderData", $db->getRows());
$json->result["resultMessage"] = "고객배송정보데이터";
echo $json->getResult();
$db->close();
exit;

?>