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
$guestId						= $_POST["guestId"];
$deguestId						= $_POST["deguestId"];
$guestLon						= $_POST["guestLon"];
$guestLat						= $_POST["guestLat"];
$deguestLon						= $_POST["deguestLon"];
$deguestLat						= $_POST["deguestLat"];
$guestJusoSubId					= $_POST["guestJusoSubId"];
$deguestJusoSubId				= $_POST["deguestJusoSubId"];
$guestIsShop					= $_POST["guestIsShop"];
$deguestIsShop					= $_POST["deguestIsShop"];
$distance						= $_POST["distance"];
$guestDate						= $_POST["guestDate"];
$deguestDate					= $_POST["deguestDate"];
$updateDate						= $_POST["updateDate"];
$jsonData						= $_POST["jsonData"];
$status							= $_POST["status"];

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$json = new Json();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
unset($DATA);

if($status=='INSERT'){
	
	$DATA["vd_guestId"]				= $guestId;
	$DATA["vd_guestJusoSubId"]		= $guestJusoSubId;
	$DATA["vd_deguestId"]				= $deguestId;
	$DATA["vd_deguestJusoSubId"]		= $deguestJusoSubId;
	$DATA["vd_guestLat"]			= $guestLat;
	$DATA["vd_guestLon"]			= $guestLon;
	$DATA["vd_deguestLat"]			= $deguestLat;
	$DATA["vd_deguestLon"]			= $deguestLon;
	$DATA["vd_guestIsShop"]			= $guestIsShop;
	$DATA["vd_deguestIsShop"]		= $deguestIsShop;
	$DATA["vd_distanceValue"]		= $distance;
	$DATA["vd_guestDate"]			= $guestDate;
	$DATA["vd_deguestDate"]			= $deguestDate;
	$DATA["vd_jsonData"]			= $jsonData;
	$DATA["vd_createDate"]			= date(("Y-m-d H:i:s"), time());
	$DATA["vd_updateDate"]			= $updateDate;
	//$DATA["vd_updateDate"]			= date(("Y-m-d H:i:s"), time());
	$db->Insert("vehicleGuestMutualDistance", $DATA, "insert vehicleGuestMutualDistance");
	
	
} else {

	/*
	
	$DATA["vd_guestLat"]			= $guestLat;
	$DATA["vd_guestLon"]			= $guestLon;
	$DATA["vd_deguestLat"]			= $deguestLat;
	$DATA["vd_deguestLon"]			= $deguestLon;
	$DATA["vd_distanceValue"]		= $distance;
	$DATA["vd_guestDate"]			= $guestDate;
	$DATA["vd_deguestDate"]			= $deguestDate;
	$DATA["vd_jsonData"]			= $jsonData;
	$DATA["vd_updateDate"]			= $updateDate;
	//$DATA["vd_updateDate"]			= date(("Y-m-d H:i:s"), time());
	$WHERE = " WHERE 
				vd_guestId='".$guestId."' AND 
				vd_guestJusoSubId='".$guestJusoSubId."' AND 
				vd_guestIsShop='".$guestIsShop."' AND 
				vd_deguestId='".$deguestId."' AND 
				vd_deguestJusoSubId='".$deguestJusoSubId."' AND 
				vd_deguestIsShop='".$deguestIsShop."'";

	$db->Update("vehicleGuestMutualDistance", $DATA, $WHERE, "insert vehicleGuestMutualDistance");

	*/
}



//$json->add("listCount", $listCount);
//$json->add("index", $index);

echo $json->getResult();
$db->close();
exit;

?>