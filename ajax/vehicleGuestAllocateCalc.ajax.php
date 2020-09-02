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
$deliveryDate		= $_POST["deliveryDate"];	// 배송날짜
$locationId			= $_POST["locationId"];		// 거점ID
$meridiemType		= $_POST["meridiemType"];	// 오전,오후
$meridiemFlag		= $_POST["meridiemFlag"];	// 오전,오후 분할 플래그

$deliveryDate	= "2018-05-14";
$locationId		= "1";
$meridiemType	= "1";
$meridiemFlag	= "1";

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//Json Class
$json = new Json();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -\


//1. 우선 현재날짜 기준으로 데이터를 삭제 해야합니다.
$WHERE = "WHERE 1=1
			AND ve_locationId='".$locationId."' 
			AND ve_deliveryDate='".$deliveryDate."' 
			AND ve_meridiemType='".$meridiemType."'
			AND ve_meridiemFlag='".$meridiemFlag."'";

$db->que = "SELECT SUM(ve_pay) AS sumPay FROM vehicleGuestOrderData ".$WHERE;
$db->query();
$vehicleGuestAllocateCalc = $db->getRow();

$json->add("vehicleGuestAllocateCalc", $vehicleGuestAllocateCalc);

echo $json->getResult();
$db->close();
exit;

?>