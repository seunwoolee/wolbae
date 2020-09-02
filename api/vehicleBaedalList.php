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
$locationId		= $_GET["locationId"];		// 거점ID
$deliveryDate	= $_GET["deliveryDate"];	// 배송날짜
$meridiemType	= $_GET["meridiemType"];	// 오전,오후 구분
$meridiemFlag	= $_GET["meridiemFlag"];	// 오전,오후 순서

$gbn			= $_GET["gbn"];		// gbn

/*
if($locationId == ""){
	$locationId = "0";
}
if(!$deliveryDate == ""){
	$deliveryDate = "2017-12-13";
}
if(!$meridiemType == ""){
	$meridiemType = "1";
}
if(!$meridiemFlag == ""){
	$meridiemFlag = "1";
}
*/

//$db->que = " set @num:=0";
//$db->query();

  $db->que  = " SELECT ";
//$db->que .= "								AS accno ";
  $db->que .= "		vg_locationId			AS locationId ";
//$db->que .= "								AS deliveryDate";
//$db->que .= "								AS flag";
  $db->que .= "		,vg_guestId				AS guestId";
//$db->que .= "								AS guestTel";
//$db->que .= "								AS isNew";
//$db->que .= "								AS isRoad";
  $db->que .= "		,vg_isShop				AS isShop";
  $db->que .= "		,vg_guestJuso			AS juso";
  $db->que .= "		,vg_guestJusoSubId		AS jusoSubId";
  $db->que .= "		,vg_guestLat			AS lat";
  $db->que .= "		,vg_guestLon			AS lon";
//$db->que .= "								AS meridiemType";
  $db->que .= "		,vg_guestName			AS name";
//$db->que .= "								AS no";
//$db->que .= "								AS pay";
					
$db->que .= "			FROM vehicleGuestInfoAdd
							WHERE 1=1
								AND vg_guestLat != ''
								AND vg_guestLat != 0
									ORDER BY RAND() LIMIT 80";



$db->query();
$vehicleBaedalList = $db->getRows();

if($gbn == "a"){
	$meridiemType = "0";
} else {
	$meridiemType = "1";
}


for($i=0;$i<count($vehicleBaedalList);$i++){

	$strDupleJusoFlg	= '';
	$strErrorJusoFlg	= '';

	$payM = (rand(1, 9) * 10000);
	$payS = (rand(1, 9) * 1000);
	$payH = (rand(1, 9) * 100);
	$payT = (rand(1, 9) * 100);
	$payO = (rand(1, 9) * 10);
	$pay = ($payM + $payS  + $payH + $payT + $payO);

	$DATA[$i]['accno']			= ($i+1);										// x accno
	$DATA[$i]['locationId']		= $vehicleBaedalList[$i]['locationId'];			// o locationId
	//$DATA[$i]['locationId']	= "0";											// o locationId
	$DATA[$i]['deliveryDate']	= date("Ymd");									// x deliveryDate
	$DATA[$i]['flag']			= "1";											// x flag
	$DATA[$i]['guestId']		= $vehicleBaedalList[$i]['guestId'];			// o guestId
	$DATA[$i]['guestTel']		= "123-456-7890";								// x guestTel
	$DATA[$i]['isNew']			= "1";											// x isNew
	$DATA[$i]['isRoad']			= "n";											// x isRoad
	$DATA[$i]['isShop']			= $vehicleBaedalList[$i]['isShop'];				// o isShop
	$DATA[$i]['juso']			= $vehicleBaedalList[$i]['juso'];				// o juso
	$DATA[$i]['jusoSubid']		= $vehicleBaedalList[$i]['jusoSubId'];			// o jusoSubId
	$DATA[$i]['lat']			= $vehicleBaedalList[$i]['lat'];				// o lat
	$DATA[$i]['lon']			= $vehicleBaedalList[$i]['lon'];				// o lon
	$DATA[$i]['meridiemType']	= $meridiemType;								// x meridiemType
	$DATA[$i]['name']			= $vehicleBaedalList[$i]['name'];				// o name
	$DATA[$i]['no']				= ($i+1);										// x no
	$DATA[$i]['pay']			= $pay;											// x pay

}

$json->add("item", $DATA);
//$json->result["resultMessage"] = "주문데이터";

echo $json->getResult();
$db->close();
exit;

?>