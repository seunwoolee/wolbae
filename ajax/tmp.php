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

$db->que = " SELECT * FROM tmpData where 1=1 and IDATE='20170501' and LOCAL='0' and LAT != '0' and LON != '0' ";
$db->query();

$order = $db->getRows();

for($i=0;$i<count($order);$i++){

	$dataOrder[$i]['vo_accno']			= $order[$i]['ACCNO'];
	$dataOrder[$i]['vo_locationId']		= "0";
	$dataOrder[$i]['vo_deliveryDate']	= "2017-08-01";				// 테스트 데이터이므로, 2017년 08월 01일 배송건으로 처리합니다.
	$dataOrder[$i]['vo_meridiemType']	= "0";						// 오전배송으로 처리합니다.
	$dataOrder[$i]['vo_guestId']		= $order[$i]['GUESTID'];
	$dataOrder[$i]['vo_guestName']		= $order[$i]['NAME'];
	$dataOrder[$i]['vo_guestTel']		= "010-2801-5348";
	$dataOrder[$i]['vo_guestJusoSubId']	= "1";
	$dataOrder[$i]['vo_guestJuso']		= $order[$i]['JUSO'];
	$dataOrder[$i]['vo_pay']			= $order[$i]['PAY'];
	$dataOrder[$i]['vo_gsu']			= $order[$i]['GSU'];
	$dataOrder[$i]['vo_guestLat']		= $order[$i]['LAT'];
	$dataOrder[$i]['vo_guestLon']		= $order[$i]['LON'];

	$dataOrder[$i]['vo_createDate']		= date(("Y-m-d H:i:s"), time());
	$dataOrder[$i]['vo_updateDate']		= date(("Y-m-d H:i:s"), time());

}

$db->Inserts("Erp_vehicleGuestOrderData", $dataOrder, " Erp_vehicleGuestOrderData Insert Error ");



$db->close();
exit;

?>