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
$deliveryDate					= $_POST["deliveryDate"];
$locationId						= $_POST["locationId"];
$meridiemType					= $_POST["meridiemType"];
$meridiemFlag					= $_POST["meridiemFlag"];
$vehicleEndStatus				= $_POST["vehicleEndStatus"];
$vo_guestId						=	$_SESSION["OMember_id"];

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$json = new Json();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

//1. 이미 배차가 완료되었는지 여부, 상태 확인
$db->que = "SELECT * FROM vehicleAllocateStatus 
				WHERE 1=1
					AND vs_locationId='".$locationId."' 
					AND vs_deliveryDate='".$deliveryDate."' 
					AND vs_meridiemType='".$meridiemType."'
					AND vs_meridiemFlag='".$meridiemFlag."'"
					;
$db->query();
$row = $db->getRow();

//2. 배차상태 테이블 배차 완료 update or insert
if($row['vs_vehicleEndStatus']){

	// update

	$DATA["vs_vehicleEndStatus"]= $vehicleEndStatus;

	$WHERE = " WHERE 1=1
					AND vs_locationId='".$locationId."' 
					AND vs_deliveryDate='".$deliveryDate."' 
					AND vs_meridiemType='".$meridiemType."'
					AND vs_meridiemFlag='".$meridiemFlag."'" ;
	$db->Update("vehicleAllocateStatus", $DATA, $WHERE, " vehicleAllocateStatus Update Error ");

} else {

	// insert
	$DATA["vs_locationId"]		= $locationId;
	$DATA["vs_deliveryDate"]	= $deliveryDate;
	$DATA["vs_meridiemType"]	= $meridiemType;
	$DATA["vs_meridiemFlag"]	= $meridiemFlag;
	//$DATA["vs_vehicleEndStatus"]= "Y";
	$DATA["vs_vehicleEndStatus"]= $vehicleEndStatus;

	$db->Insert("vehicleAllocateStatus", $DATA, $WHERE, " vehicleAllocateStatus Insert Error ");
}

echo $json->getResult();
$db->close();
exit;

?>