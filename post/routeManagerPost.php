<?
header("Content-Type:text/html;charset=UTF-8");

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";



//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$guestId				= trim($_POST["guestId"]);

if($guestId == ""){
	LIB::Alert("고객ID정보가 없습니다.", "../routeManager.html");
	exit;
}

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//-----------------------------------------------------------------------------------------------
// 데이터 삭제

// vehicleGuestInfo 
$db->que = "DELETE FROM vehicleGuestInfo WHERE vg_guestId='".$guestId."'";
$db->query();

// vehicleGuestMutualDistance
$db->que = "DELETE FROM vehicleGuestMutualDistance WHERE vd_guestId='".$guestId."'";
$db->query();

// vehicleGuestMutualDistance
$db->que = "DELETE FROM vehicleGuestMutualDistance WHERE vd_deguestId='".$guestId."'";
$db->query();

$db->close();

LIB::Alert("[".$guestId."]와 관련된 경로데이터가 삭제 되었습니다.", "../routeManager.html");

?>
