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



//$strArrayNo				= $_GET['strArrayNo'];
$deliveryDate			= $_GET['deliveryDate'];
$meridiemType			= $_GET['meridiemType'];
$locationId				= $_GET['locationId'];

$noWhere = "";

$db->que = " SELECT 
				 A.vr_vehicleNo									AS vehicleNo
				,COUNT(distinct vr_deguestLat,vr_deguestLon)	AS count
				,SUM(A.vr_distanceValue)						AS sum 
				,SUM(A.vr_deguestPay)							AS deguestPay
					FROM (SELECT * FROM vehicleAllocateResult 
							WHERE vr_deliveryDate='".$deliveryDate."' AND 
							vr_meridiemType='".$meridiemType."' AND 
							vr_locationId='".$locationId."'  
							GROUP BY vr_vehicleNo,vr_vehicleNoIndex) AS A 
					GROUP BY A.vr_vehicleNo 
					ORDER BY A.vr_vehicleNo*1 ASC, A.vr_vehicleNoIndex ASC";
$db->query();
$vehicleMapGroupListData = $db->getRows();

$json->add("vehicleMapGroupListData", $vehicleMapGroupListData);
$json->result["resultMessage"] = "맵그룹데이터";
echo $json->getResult();
$db->close();

exit;

?>