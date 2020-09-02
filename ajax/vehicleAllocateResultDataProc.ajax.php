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

//1. 우선 현재날짜 기준으로 데이터를 삭제 해야합니다.
/*
$WHERE = "WHERE vr_locationId='".$locationId."' 
		  AND vr_deliveryDate='".$deliveryDate."' AND vr_meridiemType='".$meridiemType."'";

$db->que = "DELETE FROM vehicleAllocateResult ".$WHERE;
$db->query();

//2. 정해진 날짜에 대한 주문 테이블 조회
$db->que = "SELECT * FROM vehicleGuestOrderData WHERE vo_deliveryDate='".$deliveryDate.
			"' AND vo_guestId!='".$vo_guestId."' ORDER BY vo_seq ASC";
$db->query();


//3. 주문 테이블 조회에 대한것을 토데로 배차 테이블 INSERT
$vehicleGuestOrderDataList = $db->getRows();
$count=0;
for($i=0;$i<count($vehicleGuestOrderDataList);$i++){
	
	$vehicleNo				=0;
	$vehicleNoIndex			=0;

	if($count==0){
		$vehicleNo				=1;
		$vehicleNoIndex			=1;
	}
	else if($count==1){
		$vehicleNo				=1;
		$vehicleNoIndex			=2;
	}
	else if($count==2){
		$vehicleNo				=1;
		$vehicleNoIndex			=3;
	}
	else if($count==3){
		$vehicleNo				=2;
		$vehicleNoIndex			=1;
	}
	else if($count==4){
		$vehicleNo				=2;
		$vehicleNoIndex			=2;
	}
	else if($count==5){
		$vehicleNo				=2;
		$vehicleNoIndex			=3;
	}
	else if($count==6){
		$vehicleNo				=3;
		$vehicleNoIndex			=3;
	}
	else if($count==7){
		$vehicleNo				=3;
		$vehicleNoIndex			=2;
	}
	else if($count==8){
		$vehicleNo				=3;
		$vehicleNoIndex			=1;
	}
	$DATA["vr_vehicleNo"]			= $vehicleNo;
	$DATA["vr_vehicleNoIndex"]		= $vehicleNoIndex;
	//$DATA["vr_accno"]				= $vehicleGuestOrderDataList[$i]["vo_accno"];
	$DATA["vr_guestId"]				= $vehicleGuestOrderDataList[$i]["vo_guestId"];
	$DATA["vr_locationId"]			= $locationId;
	$DATA["vr_deliveryDate"]		= $deliveryDate;
	$DATA["vr_meridiemType"]		= $meridiemType;
	$DATA["vr_createDate"]			= date(("Y-m-d H:i:s"), time());
	$DATA["vr_updateDate"]			= date(("Y-m-d H:i:s"), time());

	$db->Insert("vehicleAllocateResult", $DATA, "insert vehicleAllocateResult");
	$count++;
	unset($DATA);
}
*/
//4. 배차상태 테이블 배차 완료 UPDATE
$DATA["vs_vehicleEndStatus"]= $vehicleEndStatus;
$WHERE = "	WHERE 1=1
				AND vs_locationId='".$locationId."' 
				AND vs_deliveryDate='".$deliveryDate."' 
				AND vs_meridiemType='".$meridiemType."' ";

$db->Update("vehicleAllocateStatus", $DATA, $WHERE, " vehicleAllocateStatus Insert Error ");
/*
$WHERE = "WHERE vr_locationId='".$locationId."' 
		  AND vr_deliveryDate='".$deliveryDate."' AND vr_meridiemType='".$meridiemType."'";
$db->que = "DELETE FROM vehicleAllocateResult ".$WHERE;
$db->query();
*/

echo $json->getResult();
$db->close();
exit;

?>