<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Step01
// 고객 업데이트정보 API
// ERP Server <===== local Server (call)
// ERP Server로부터 고객정보가 변경된 데이터만 가져와서 거점서버에 업데이트합니다.
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/mysql.inc.php";
include "../inc/user.inc.php";
include "../inc/json.inc.php";
include "../inc/lib.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

//1. 넘겨받을 변수 확인
$vo_deliveryDate	= $_POST["deliveryDate"];
$vo_locationId		= $_POST["locationId"];
$vo_meridiemType	= $_POST["meridiemType"];
$result = "N";

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$json = new Json();
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -



//2. 이미 배차가 완료되었는지 여부, 상태 확인
$db->que = "SELECT * FROM vehicleAllocateStatus 
			WHERE vs_locationId='".$vo_locationId."' 
			AND vs_deliveryDate='".$vo_deliveryDate."' AND vs_meridiemType='".$vo_meridiemType."'";
$db->query();

if($db->affected_rows() > 0){
	$row = $db->getRow();
	
	if($row["vs_vehicleEndStatus"]=='Y'){
		$result = "Y";
	}

} else {

	$DATA["vs_locationId"]		= $vo_locationId;
	$DATA["vs_deliveryDate"]	= $vo_deliveryDate;
	$DATA["vs_meridiemType"]	= $vo_meridiemType;
	$DATA["vs_vehicleEndStatus"]= "N";

	$db->Insert("vehicleAllocateStatus", $DATA, " vehicleAllocateStatus Insert Error ");
}


$json->add("code", $result);
$json->add("message", "완료");

echo $json->getResult();
$db->close();
exit;

?>