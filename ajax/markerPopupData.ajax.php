<?
header("Content-Type:text/html;charset=UTF-8");
//###################################################
// 유류비 정산
// 2016
//###################################################


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/json.inc.php";
include "../inc/mysql.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$deliveryDate			= $_GET['deliveryDate'];
$meridiemType			= $_GET['meridiemType'];
$meridiemFlag			= $_GET['meridiemFlag'];
$locationId				= $_GET['locationId'];
$lat					= $_GET['lat'];
$lon					= $_GET['lon'];

$ci_guestId			=	$_SESSION["OMember_id"];
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db = new Mysql();

//Json Class
$json = new Json();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
//LIB::PLog($deliveryDate.",".$meridiemType.",".$locationId.",".$lat.",".$lon);
/*
$db->que = "SELECT 
						 ve_guestName		AS guestName
						,ve_guestTel		AS guestTel
						,ve_guestJuso		AS guestJuso
						,ve_pay				AS pay  
							FROM vehicleGuestOrderData  
							WHERE ve_deliveryDate='".$deliveryDate."' AND 
							ve_meridiemType='".$meridiemType."' AND 
							ve_locationId='".$locationId."' AND 
							ve_guestLat='".$lat."' AND 
							ve_guestLon='".$lon."'";
*/
/*
$db->que = "SELECT 
						 vr_deguestName		AS deguestName 
						,vr_guestId			AS guestId 
						,vr_deguestTel		AS deguestTel
						,vr_Juso			AS Juso
						,sum(vr_deguestPay)	AS deguestPay  
						,vr_deguestAccno	AS deguestAccno 
						,vr_vehicleNo		AS vehicleNo
						,vr_vehicleNoIndex	AS vehicleNoIndex 
							FROM vehicleAllocateResult  
							WHERE vr_deliveryDate='".$deliveryDate."' AND 
							vr_meridiemType='".$meridiemType."' AND 
							vr_locationId='".$locationId."' AND 
							vr_deguestLat='".$lat."' AND 
							vr_deguestLon='".$lon."' 
							GROUP BY vr_deguestId,vr_deguestJusoSubId,vr_deguestIsShop 
							ORDER BY vr_vehicleNoIndex";
*/

// 쿼리수정, 중첩지점데이터 반영을 위해 - group by 필요없음, result테이블데이터에 입력된 그대로 가져옴

$db->que = " SET @num:=0 ";
$db->query();

$db->que = "SELECT		@num:=@num+1		AS no
						,vr_deguestName		AS deguestName 
						,vr_guestId			AS guestId 
						,vr_deguestId		AS deguestId 
						,vr_deguestTel		AS deguestTel
						,vr_Juso			AS Juso
						,vr_deguestPay		AS deguestPay  
						,vr_deguestAccno	AS deguestAccno 
						,vr_vehicleNo		AS vehicleNo
						,vr_vehicleNoIndex	AS vehicleNoIndex 
						,vr_accnoDupleJuso	AS accnoDupleJuso
						,vr_deguestLat		AS deguestLat
						,vr_deguestLon		AS deguestLon
						,vr_seq				AS seq
						,vr_deguestJusoSubId as deguestJusoSubId
						,vr_deguestIsShop	AS deguestIsShop
						,vr_errorJusoFlag	AS errorJusoFlag
							FROM vehicleAllocateResult  
								WHERE 1=1
									AND vr_deliveryDate='".$deliveryDate."' 
									AND vr_meridiemType='".$meridiemType."' 
									AND vr_meridiemFlag='".$meridiemFlag."' 
									AND vr_locationId='".$locationId."' 
									AND vr_deguestLat='".$lat."' 
									AND vr_deguestLon='".$lon."' 
										ORDER BY cast(vr_vehicleNo AS UNSIGNED) ";

$db->query();
$orderUserList = $db->getRows();


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
							ORDER BY A.vr_vehicleNo*1 ASC ";
$db->query();
$vehicleAllocateResultList = $db->getRows();

$json->add("orderUserList", $orderUserList);
$json->add("vehicleAllocateResultList", $vehicleAllocateResultList);
$json->result["resultMessage"] = "고객정보데이터";
echo $json->getResult();
$db->close();

exit;

?>
