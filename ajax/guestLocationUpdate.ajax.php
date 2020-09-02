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

$guestId			= $_POST['guestId'];
$guestLat			= $_POST['guestLat'];
$guestLon			= $_POST['guestLon'];


$meridiemType		= $_POST['meridiemType'];
$meridiemFlag		= $_POST['meridiemFlag'];
$deliveryDate		= $_POST['deliveryDate'];

//echo "guestId[".$guestId."]<br>";
//echo "guestLat[".$guestLat."]<br>";
//echo "guestLon[".$guestLon."]<br>";
//echo "meridiemType[".$meridiemType."]<br>";
//echo "meridiemFlag[".$meridiemFlag."]<br>";
//echo "deliveryDate[".$deliveryDate."]<br>";





//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

$json = new Json();

unset($DATA);

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

/*
$DATA["vg_guestLat"]		= $strLat;
$DATA["vg_guestLon"]		= $strLon;
//$DATA["vg_guestJuso"]		= $strJuso;

$WHERE =	" WHERE vg_guestId = '".$guestId."'";
$db->Update("vehicleGuestInfo", $DATA, $WHERE, " vehicleGuestInfo Update Error ");

*/


//-----------------------------------------------------------------------------------------------
// 주문데이터에서 좌표정보 초기화
// 당일 주문데이터에서 해당 고객의 좌표정보를 초기화 합니다.
//-----------------------------------------------------------------------------------------------
unset($DATA);
$DATA["ve_guestLat"]		= "0";
$DATA["ve_guestLon"]		= "0";
//$DATA["ve_guestJuso"]		= $guestJuso;
$DATA["ve_isJuso"]			= "N";
$DATA["ve_errorJusoFlag"]	= "Y";



//$DATA["ve_guestJuso"]		= $strJuso;
$WHERE  = " WHERE 1=1 ";
$WHERE .= " AND ve_guestId = '".$guestId."'";
$WHERE .= " AND ve_deliveryDate = '".$deliveryDate."'";
$WHERE .= " AND ve_meridiemType = '".$meridiemType."'";
$WHERE .= " AND ve_meridiemFlag = '".$meridiemFlag."'";

$db->Update("vehicleGuestOrderData", $DATA, $WHERE, " vehicleGuestOrderData Update Error ");

//-----------------------------------------------------------------------------------------------
// vehicleAllocateResult
// 주문데이터에서 좌표정보 초기화
// 당일 주문데이터에서 해당 고객의 좌표정보를 초기화 합니다.
//-----------------------------------------------------------------------------------------------
unset($DATA);
$DATA["vr_guestLat"]		= "0";
$DATA["vr_guestLon"]		= "0";
$DATA["vr_deguestLat"]		= $guestLat;
$DATA["vr_deguestLon"]		= $guestLon;
$DATA["vr_errorJusoFlag"]	= "Y";

//$DATA["ve_guestJuso"]		= $strJuso;
$WHERE  = " WHERE 1=1 ";
$WHERE .= " AND vr_deguestId = '".$guestId."'";
$WHERE .= " AND vr_deliveryDate = '".$deliveryDate."'";
$WHERE .= " AND vr_meridiemType = '".$meridiemType."'";
$WHERE .= " AND vr_meridiemFlag = '".$meridiemFlag."'";

$db->Update("vehicleAllocateResult", $DATA, $WHERE, " vehicleAllocateResult Update Error ");


//-----------------------------------------------------------------------------------------------
// vehicleGuestInfo update
//-----------------------------------------------------------------------------------------------
unset($DATA);
$DATA["vg_guestLat"]		= "0";
$DATA["vg_guestLon"]		= "0";
$DATA["vg_isJuso"]			= "N";

$WHERE  = " WHERE 1=1 ";
$WHERE .= " AND vg_guestId = '".$guestId."'";

$db->Update("vehicleGuestInfo", $DATA, $WHERE, " vehicleGuestInfo Update Error ");


//-----------------------------------------------------------------------------------------------
// 데이터 삭제
//-----------------------------------------------------------------------------------------------

// vehicleGuestInfo 
//$db->que = "DELETE FROM vehicleGuestInfo WHERE vg_guestId='".$guestId."'";
//$db->query();

// vehicleGuestMutualDistance
$db->que = "DELETE FROM vehicleGuestMutualDistance WHERE vd_guestId='".$guestId."'";
$db->query();

// vehicleGuestMutualDistance
$db->que = "DELETE FROM vehicleGuestMutualDistance WHERE vd_deguestId='".$guestId."'";
$db->query();

echo $json->getResult();
$db->close();
exit;

?>