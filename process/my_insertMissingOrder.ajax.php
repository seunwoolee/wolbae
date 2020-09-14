<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// 배차결과 테이블을 토데로 지도에 출력시킬 데이터를 가져온다.
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


$deliveryDate = $_POST["deliveryDate"];    // 배송날짜
$locationId = $_POST["locationId"];        // 거점ID
$meridiemType = $_POST["meridiemType"];    // 오전,오후
$guestId = $_POST["guestId"];
$routeNumber = $_POST["routeNumber"];


$WHERE = "
			WHERE 1=1
			AND ve_deguestId ='" . $guestId . "' 
			AND ve_deliveryDate='" . $deliveryDate . "' 
			AND ve_meridiemType='" . $meridiemType . "' 
			AND ve_locationId='" . $locationId . "'
		";
			
$db->que = " SELECT * FROM vehicleGuestOrderData " + $WHERE;
$db->query();
$order = $db->getRow();


$DATA["vg_guestId"]			= $order['guestId'];
$DATA["vg_guestId"]			= $order['guestId'];
$DATA["vg_guestId"]			= $order['guestId'];
$DATA["vg_guestId"]			= $order['guestId'];
$DATA["vg_guestId"]			= $order['guestId'];

if((empty($vehicleGuestOrderDataList[$i]['guestJuso'])) || ($vehicleGuestOrderDataList[$i]['guestLat']==0) || ($vehicleGuestOrderDataList[$i]['guestLon']==0)){
	$DATA["vg_isJuso"]			= "N";
}

$db->Insert("vehicleGuestInfo", $DATA, " vehicleGuestInfo Insert Error ");



$json->result["resultMessage"] = "고객정보데이터";
$json->result["code"] = "ok";
echo $json->getResult();
$db->close();

exit;

?>

