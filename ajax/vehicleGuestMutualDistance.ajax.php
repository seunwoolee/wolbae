<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// 거섬서버 배송주소 중복제거된 데이터 가져오기
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

//$today = date("Y-m-d");
$deliveryDate   = $_POST["deliveryDate"];
$locationId		= $_POST["locationId"];
$meridiemType   = $_POST["meridiemType"];
$meridiemFlag   = $_POST["meridiemFlag"];
//$deliveryDate  = "2017-09-28 00:00:00";

// 모든 고객정보 테이블 조회
$db->que = " SELECT ve_seq AS seq
					,ve_guestId				AS guestId
					,ve_guestJusoSubId		AS guestJusoSubId
					,ve_guestLat			AS guestLat
					,ve_guestLon			AS guestLon
					,ve_isShop				AS isShop 
					,vg_updateDate			AS updateDate
						FROM vehicleGuestOrderData 
						JOIN vehicleGuestInfo 
						ON ve_guestId=vg_guestId AND ve_guestJusoSubId=vg_guestJusoSubId AND ve_isShop = vg_isShop  
							WHERE 1=1
								AND ve_isJuso='Y' 
								AND ve_deliveryDate='".$deliveryDate."' 
								AND ve_locationId='".$locationId."' 
								AND ve_meridiemType='".$meridiemType."'
								AND ve_meridiemFlag='".$meridiemFlag."'
									GROUP BY ve_guestId,ve_guestJusoSubId,ve_isShop 
									ORDER BY ve_guestId ASC";
$db->query();
$vehicleGuestInfoData = $db->getRows();
// 고객정보 테이블 조회하여 오늘 업데이트된 주소데이터를 가져온다.

/*
$db->que = " SELECT vg_seq AS seq
					,vg_guestId				AS guestId
					,vg_guestJusoSubId		AS guestJusoSubId
					,vg_guestLat			AS guestLat
					,vg_guestLon			AS guestLon
						FROM vehicleGuestInfo
						WHERE vg_updateDate > '".$deliveryDate."' AND vg_guestJuso!='' AND vg_guestLat!='' AND vg_guestLon!='' ORDER BY vg_guestId ASC";
*/



//LIB::PLog("cococococ:".$db->affected_rows());

$json->add("vehicleGuestInfoData", $vehicleGuestInfoData);

$json->result["resultMessage"] = "고객배송중복제거데이터";
echo $json->getResult();
$db->close();
exit;

?>