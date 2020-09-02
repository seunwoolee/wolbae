<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Step01
// 고객 업데이트정보 API
// ERP Server <===== local Server (call)
// ERP Server로부터 고객정보가 변경된 데이터만 가져와서 거점서버에 업데이트합니다.
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/mysql.inc.php";
include "../inc/user.inc.php";
include "../inc/json.inc.php";
include "../inc/lib.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

// ERP서버 적용시 PARAM 값이 맞지 않을수 있으니, ERP서버측에서 넘어오는 데이터 확인 필요!!!
$PARAM = json_decode(file_get_contents("php://input"), JSON_UNESCAPED_UNICODE);

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$json = new Json();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$deliveryDate			= $_POST["deliveryDate"];		// 배송날짜
$locationId				= $_POST["locationId"];			// 거점ID
$meridiemType			= $_POST["meridiemType"];		// 오전,오후
$meridiemFlag			= $_POST["meridiemFlag"];		// 오전,오후 분할 플래그 
$guestId				= $_POST["guestId"];
$guestJusoSubId			= $_POST["guestJusoSubId"];
$isShop					= $_POST["isShop"];
$isAddVehicle			= $_POST["isAddVehicle"];

$DATA["ve_isJuso"]		= "N";
$DATA["ve_guestLat"]	= 0;
$DATA["ve_guestLon"]	= 0;

$WHERE  = " WHERE 1=1 
					AND ve_deliveryDate='".$deliveryDate."' 
					AND ve_meridiemType='".$meridiemType."' 
					AND ve_meridiemFlag='".$meridiemFlag."' 
					AND ve_locationId='".$locationId."' 
					AND ve_guestId='".$guestId."' 
					AND ve_guestJusoSubId='".$guestJusoSubId."' 
					AND ve_isShop='".$isShop."'";

$db->Update("vehicleGuestOrderData", $DATA, $WHERE, " vehicleGuestOrderData Update Error ");
unset($DATA);


$DATA["vg_isJuso"]		= "N";
$DATA["vg_guestLat"]	= 0;
$DATA["vg_guestLon"]	= 0;
$WHERE  = " WHERE  vg_guestId='".$guestId."' AND 
					vg_guestJusoSubId='".$guestJusoSubId."' AND 
					vg_isShop='".$isShop."'";
$db->Update("vehicleGuestInfo", $DATA, $WHERE, " vehicleGuestOrderData Update Error ");

$unset($DATA);

if($isAddVehicle=='Y'){
	$WHERE = " WHERE 1=1 
						AND vr_deliveryDate='".$deliveryDate."' 
						AND vr_locationId='".$locationId."' 
						AND vr_meridiemType='".$meridiemType."'
						AND vr_meridiemFlag='".$meridiemFlag."'
						AND vr_deguestId='".$guestId."' 
						AND vr_deguestJusoSubId='".$guestJusoSubId."' 
						AND vr_deguestIsShop='".$isShop."'";

	$db->que = "DELETE FROM vehicleAllocateResult ".$WHERE;
	$db->query();
}

echo "ok";
$db->close();
exit;

?>