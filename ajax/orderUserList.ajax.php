<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// 배차결과 테이블을 토데로 지도에 출력시킬 데이터를 가져온다.
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
$ci_guestId			=	$_SESSION["OMember_id"];
$vehicleNo				= $_GET['vehicleNo'];
$deliveryDate			= $_GET['deliveryDate'];
$meridiemType			= $_GET['meridiemType'];
$meridiemFlag			= $_GET['meridiemFlag'];
$locationId				= $_GET['locationId'];

//LIB::PLog("wwww:".$ci_guestId);
/*
$subQuery = " (SELECT 
					 vr_deguestId 
					,vr_deguestJusoSubId 
					,vr_deguestIsShop 
					,vr_vehicleNoIndex 
					,vr_deguestLat
					,vr_deguestLon
						FROM vehicleAllocateResult 
						WHERE vr_deliveryDate='".$deliveryDate."' AND vr_meridiemType='".$meridiemType."' 
						AND vr_locationId='".$locationId."' AND vr_vehicleNo='".$vehicleNo."'  
						ORDER BY vr_vehicleNoIndex ASC) AS vr ";
$db->que = "SELECT 
						 ve_guestName		AS guestName
						,ve_guestTel		AS guestTel
						,ve_guestJuso		AS guestJuso
						,ve_pay				AS pay
						,vr_vehicleNoIndex	AS vehicleNoIndex
						,count(ve_seq)		AS deliveryCount 
							FROM vehicleGuestOrderData AS ve JOIN".$subQuery." 
							ON ve_guestLat = vr_deguestLat AND ve_guestLon = vr_deguestLon  
							WHERE ve_deliveryDate='".$deliveryDate."' AND ve_meridiemType='".$meridiemType."' 
							AND ve_locationId='".$locationId."' 
							GROUP BY ve_accno";
							//GROUP BY ve_guestId,ve_guestJusoSubId";
*/

/*
$db->que = "SELECT		vr_deguestId		AS deguestId  
						,vr_guestId			AS guestId 
						,vr_deguestLat		AS deguestLat 
						,vr_deguestLon		AS deguestLon 
						,vr_deguestName		AS deguestName
						,vr_deguestTel		AS deguestTel
						,vr_Juso			AS Juso
						,sum(vr_deguestPay)		AS deguestPay
						,vr_vehicleNoIndex	AS vehicleNoIndex
							FROM vehicleAllocateResult   
							WHERE vr_deliveryDate='".$deliveryDate."' AND vr_meridiemType='".$meridiemType."' 
							AND vr_locationId='".$locationId."' AND 
							vr_vehicleNo='".$vehicleNo."' AND 
							vr_deguestId!='".$ci_guestId."' 
							GROUP BY vr_deguestId,vr_deguestJusoSubId,vr_deguestIsShop 
							ORDER BY vr_vehicleNoIndex";
							//GROUP BY ve_guestId,ve_guestJusoSubId";
*/

// 쿼리수정, 중첩지점데이터 반영을 위해 - group by 필요없음, result테이블데이터에 입력된 그대로 가져옴
$db->que = " SET @num:= 0";
$db->query();

$db->que = "SELECT		@num:=@num+1		AS no
						,vr_deguestAccno	AS deguestAccno
						,vr_deguestId		AS deguestId  
						,vr_guestId			AS guestId 
						,vr_deguestLat		AS deguestLat 
						,vr_deguestLon		AS deguestLon 
						,vr_deguestName		AS deguestName
						,vr_deguestTel		AS deguestTel
						,vr_Juso			AS Juso
						,vr_deguestPay		AS deguestPay
						,vr_vehicleNoIndex	AS vehicleNoIndex
						,vr_accnoDupleJuso	AS accnoDupleJuso
						,vr_errorJusoFlag	AS errorJusoFlag
							FROM vehicleAllocateResult   
							WHERE 1=1
							AND vr_deliveryDate='".$deliveryDate."' 
							AND vr_meridiemType='".$meridiemType."' 
							AND vr_meridiemFlag='".$meridiemFlag."' 
							AND vr_locationId='".$locationId."' 
							AND vr_vehicleNo='".$vehicleNo."' 
							AND vr_deguestId!='".$ci_guestId."' 
							order by vr_vehicleNoIndex ";
//echo $db->que;exit;
$db->query();

$orderUserList = $db->getRows();



$json->add("orderUserList", $orderUserList);
$json->result["resultMessage"] = "고객정보데이터";
echo $json->getResult();
$db->close();

exit;

?>