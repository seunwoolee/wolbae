<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// 배차결과중에 주소,위,경도 값에 문제가 있는 유저들
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/mysql.inc.php";
include "../inc/user.inc.php";
include "../inc/json.inc.php";
include "../inc/lib.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$deliveryDate			= $_GET['deliveryDate'];
$meridiemType			= $_GET['meridiemType'];
$meridiemFlag			= $_GET['meridiemFlag'];
$locationId				= $_GET['locationId'];

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//Json Class
$json = new Json();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
/*
$db->que = "SELECT	ve_accno				AS accno
					,ve_guestName			AS guestName
					,ve_guestTel			AS guestTel
					,sum(ve_pay)			AS guestPay 
					,ve_guestId				AS guestId 
					,ve_guestJusoSubId		AS guestJusoSubId 
					,ve_isShop				AS isShop 
					,ve_guestJuso			AS guestJuso
					,ve_guestLat			AS guestLat
					,ve_guestLon			AS guestLon
					,ve_guestName			AS guestName 
						FROM vehicleGuestOrderData 
							WHERE 1=1
								AND ve_deliveryDate = '".$deliveryDate."'
								AND ve_meridiemType='".$meridiemType."'
								AND ve_meridiemFlag='".$meridiemFlag."'
								AND ve_locationId='".$locationId."'
								AND ve_guestId!='admin'
								AND ve_isJuso = 'N' 
								AND ve_errorJusoFlag != 'Y'
									GROUP BY ve_guestId,ve_guestJusoSubId,ve_isShop";
*/

// 오류건들은 경로에 추가되더라도, 중복건으로 보지 않습니다.
$db->que = "SELECT	ve_accno				AS accno
					,ve_guestName			AS guestName
					,ve_guestTel			AS guestTel
					,ve_guestId				AS guestId 
					,ve_guestJusoSubId		AS guestJusoSubId 
					,ve_isShop				AS isShop 
					,ve_guestJuso			AS guestJuso
					,ve_guestLat			AS guestLat
					,ve_guestLon			AS guestLon
					,ve_guestName			AS guestName 
					,ve_pay					AS guestPay
						FROM vehicleGuestOrderData 
							WHERE 1=1
								AND ve_deliveryDate		= '".$deliveryDate."'
								AND ve_meridiemType		= '".$meridiemType."'
								AND ve_meridiemFlag		= '".$meridiemFlag."'
								AND ve_locationId		= '".$locationId."'
								AND ve_guestId			!= 'admin'
								AND ve_isJuso			= 'N' 
								AND ve_errorJusoFlag	!= 'Y' ";
$db->query();
$errorUserList = $db->getRows();

$db->que = "SELECT 
					A.vr_vehicleNo				AS vehicleNo
					,sum(A.vr_distanceValue)	AS distanceValue
						FROM (SELECT * FROM vehicleAllocateResult 
								WHERE 1=1
									AND vr_deliveryDate='".$deliveryDate."'
									AND vr_meridiemType='".$meridiemType."'
									AND vr_meridiemFlag='".$meridiemFlag."'
									AND vr_locationId='".$locationId."'  
										GROUP BY vr_vehicleNo,vr_vehicleNoIndex) AS A 
										GROUP BY A.vr_vehicleNo 
											ORDER BY A.vr_vehicleNo*1 ASC";
$db->query();
$vehicleAllocateResultList = $db->getRows();

$json->add("errorUserList", $errorUserList);
$json->add("vehicleAllocateResultList", $vehicleAllocateResultList);
$json->result["resultMessage"] = "고객정보데이터";
echo $json->getResult();
$db->close();

exit;

?>