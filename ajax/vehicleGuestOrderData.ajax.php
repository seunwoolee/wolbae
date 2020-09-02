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
$deliveryDate	= $_POST["deliveryDate"];	// 배송날짜
$locationId		= $_POST["locationId"];		// 거점ID
$meridiemType	= $_POST["meridiemType"];	// 오전,오후

$meridiemFlag	= $_POST["meridiemFlag"];	// 오전,오후 배차 분할 플래그

/*
$db->que = " SELECT ve_seq AS seq
					,ve_accno			AS accno
					,ve_locationId		AS locationId
					,ve_deliveryDate	AS deliveryDate
					,ve_meridiemType	AS meridiemType
					,ve_guestId			AS guestId
					,ve_guestName		AS name
					,ve_guestTel		AS guestTel
					,ve_guestJusoSubId	AS jusoSubId
					,ve_guestJuso		AS juso
					,ve_pay				AS pay
					,ve_guestLat		AS lat
					,ve_guestLon		AS lon
					,ve_isNew			AS isNew
					,ve_isShop			AS isShop
					,ve_isRoad			AS isRoad 
						FROM vehicleGuestOrderData 
						WHERE ve_locationId='".$locationId."' AND 
						ve_deliveryDate='".$deliveryDate."' AND 
						ve_meridiemType='".$meridiemType."'
						 ORDER BY ve_seq asc";
*/
$db->que = " SELECT ve_seq AS seq
					,ve_accno			AS accno
					,ve_locationId		AS locationId
					,ve_deliveryDate	AS deliveryDate
					,ve_meridiemType	AS meridiemType
					,ve_guestId			AS guestId
					,ve_guestName		AS name
					,ve_guestTel		AS guestTel
					,ve_guestJusoSubId	AS jusoSubId
					,ve_guestJuso		AS juso
					,ve_pay				AS pay
					,ve_guestLat		AS lat
					,ve_guestLon		AS lon
					,ve_isNew			AS isNew
					,ve_isShop			AS isShop
					,ve_isRoad			AS isRoad 
						FROM vehicleGuestOrderData 
						WHERE 1=1
						AND ve_locationId='".$locationId."' 
						AND ve_deliveryDate='".$deliveryDate."'
						AND ve_meridiemType='".$meridiemType."'
						AND ve_meridiemFlag='".$meridiemFlag."'
						 ORDER BY ve_seq asc";
$db->query();

$json->add("vehicleGuestOrderData", $db->getRows());
$json->result["resultMessage"] = "고객배송정보데이터";
echo $json->getResult();
$db->close();
exit;

?>