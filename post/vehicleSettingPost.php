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

$guestId				=	$_SESSION["OMember_id"];
$speed					= $_POST["speed"];
$time					= $_POST["time"];

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//-----------------------------------------------------------------------------------------------
// DB 저장


$DATA["vt_speed"]				= $speed;
$DATA["vt_time"]				= $time;


$db->Update("vehicleSetting", $DATA, "where vt_guestId='". $guestId. "'", "update error");
LIB::Alert("수정 되었습니다.", "../vehicleSetting.html");

?>
