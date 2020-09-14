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
			AND ve_guestId ='" . $guestId . "' 
			AND ve_deliveryDate='" . $deliveryDate . "' 
			AND ve_meridiemType='" . $meridiemType . "' 
			AND ve_locationId='" . $locationId . "'
		";
			
$db->que = " SELECT * FROM vehicleGuestOrderData $WHERE";

$db->query();
$orders = $db->getRows();
LIB::PLog($orders);

for($i=0; $i<count($orders); $i++)
{
	$DATA["vr_deliveryDate"]			= $orders[$i]['ve_deliveryDate'];
	$DATA["vr_meridiemType"]			= $orders[$i]['ve_meridiemType'];
	$DATA["vr_deguestAccno"]			= $orders[$i]['ve_accno'];
	$DATA["vr_deguestName"]			= $orders[$i]['ve_guestName'];
	$DATA["vr_deguestPay"]			= $orders[$i]['ve_pay'];
	$DATA["vr_deguestId"]			= $orders[$i]['ve_guestId'];
	$DATA["vr_deguestLat"]			= $orders[$i]['ve_guestLat'];
	$DATA["vr_deguestLon"]			= $orders[$i]['ve_guestLon'];
	$DATA["vr_Juso"]			= $orders[$i]['ve_guestJuso'];
	$DATA["vr_locationId"]			= $orders[$i]['ve_locationId'];
	$DATA["vr_distanceValue"]			= 0;
	$DATA["vr_meridiemFlag"]			= '1';
	$DATA["vr_vehicleNo"]			= intval($routeNumber) - 1;
	$DATA["vr_vehicleNoIndex"]			= 100;
	
	$DATA["vr_deguestJusoSubId"]			= '1';
	$DATA["vr_deguestIsShop"]			= '0';

	$db->Insert("vehicleAllocateResult", $DATA, " vehicleGuestInfo Insert Error ");		
}




$json->result["resultMessage"] = "고객정보데이터";
$json->result["code"] = "ok";
echo $json->getResult();
$db->close();

exit;

?>

