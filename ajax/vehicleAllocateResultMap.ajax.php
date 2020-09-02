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




//$initLon = $companyInfo['ci_lon'];
//$initLat = $companyInfo['ci_lat'];

//$strArrayNo			= $_GET['strArrayNo'];
$deliveryDate			= $_GET['deliveryDate'];
$meridiemType			= $_GET['meridiemType'];
$meridiemFlag			= $_GET['meridiemFlag'];
$locationId				= $_GET['locationId'];

/*
$noWhere				= " AND vr_vehicleNo IN(".$strArrayNo.")";
if($strArrayNo==''){
	$json->result["resultMessage"] = "NULL";
	echo $json->getResult();
	$db->close();
	exit;
}
*/
$noWhere = "";

$db->que = " SET @num:=0 ";
$db->query();

$db->que = " SELECT @num:=@num+1		AS no
					,vr_deguestName		AS deguestName
					,vr_deguestTel		AS deguestTel
					,vr_deguestPay		AS deguestPay 
					,vr_guestId			AS guestId
					,vr_deguestId		AS deguestId
					,vr_Juso			AS Juso
					,vr_guestLon		AS guestLon
					,vr_guestLat		AS guestLat
					,vr_deguestLon		AS deguestLon
					,vr_deguestLat		AS deguestLat
					,vr_vehicleNo		AS vehicleNo
					,vr_vehicleNoIndex	AS vehicleNoIndex
					,vr_jsonData		AS jsonData
					,vr_errorJusoFlag	AS errorJusoFlag
						FROM vehicleAllocateResult 
						WHERE 1=1
						AND vr_deliveryDate='".$deliveryDate."' 
						AND vr_meridiemType='".$meridiemType."' 
						AND vr_meridiemFlag='".$meridiemFlag."' 
						AND vr_locationId='".$locationId."'".$noWhere. " 
							GROUP BY vr_vehicleNo, vr_vehicleNoIndex 
							ORDER BY vr_vehicleNo*1 ASC, vr_vehicleNoIndex*1 ASC";
$db->query();
$vehicleAllocateResultList = $db->getRows();

for($i=0;$i<count($vehicleAllocateResultList);$i++){
	
	$jsonData = stripslashes($vehicleAllocateResultList[$i]['jsonData']);
	$jsonData = json_decode($jsonData);

	$vehicleAllocateResultList[$i]['jsonData'] = $jsonData;
}
$json->add("vehicleAllocateResultList", $vehicleAllocateResultList);
$json->result["resultMessage"] = "고객정보데이터";
echo $json->getResult();
$db->close();

exit;

?>