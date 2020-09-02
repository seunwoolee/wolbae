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

$deliveryDate			= $_POST['deliveryDate'];
$meridiemType			= $_POST['meridiemType'];
$meridiemFlag			= $_POST['meridiemFlag'];		// 
$locationId				= $_POST['locationId'];			//
$vehicleNo				= $_POST['vehicleNo'];			//
$vehicleNoIndex			= $_POST['vehicleNoIndex'];
$changeVehicleNo		= $_POST['changeVehicleNo'];
$ci_guestId				= $_SESSION["OMember_id"];

/*
$deliveryDate		= "2017-11-03";
$meridiemType		= "0";
$locationId			= "0";
$vehicleNo			= "0";
$vehicleNoIndex		= "0";
$changeVehicleNo	= "12";

echo "deliveryDate[".$deliveryDate."]<br>";
echo "meridiemType[".$meridiemType."]<br>";
echo "locationId[".$locationId."]<br>";
echo "vehicleNo[".$vehicleNo."]<br>";
echo "vehicleNoIndex[".$vehicleNoIndex."]<br>";	
echo "changeVehicleNo[".$changeVehicleNo."]<br>";
echo "ci_guestId[".$ci_guestId."]<br>";
*/


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$json = new Json();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -




// 1. 변경건 조회 - accNo
$db->que = "SELECT * FROM vehicleAllocateResult 
					WHERE 1=1
						AND vr_deliveryDate		= '".$deliveryDate."' 
						AND vr_meridiemType		= '".$meridiemType."'
						AND vr_meridiemFlag		= '".$meridiemFlag."' 
						AND vr_locationId		= '".$locationId."' 
						AND vr_vehicleNo		= '".$vehicleNo."' 
						AND vr_vehicleNoIndex	= '".$vehicleNoIndex."'";
$db->query();
$vehicleRows = $db->getRows();

// 1.1 고객정보 수정 및 주문정보 수정 해야


// 2. 변경건 삭제
$WHERE = " WHERE 1=1
				AND vr_deliveryDate		= '".$deliveryDate."'
				AND vr_meridiemType		= '".$meridiemType."'
				AND vr_meridiemFlag		= '".$meridiemFlag."'
				AND vr_locationId		= '".$locationId."'
				AND vr_vehicleNo		= '".$vehicleNo."'
				AND vr_vehicleNoIndex	= '".$vehicleNoIndex."'";

$db->que = "DELETE FROM vehicleAllocateResult ".$WHERE;
$db->query();

// 3. 변경건 vr_vehicleNoIndex 재정렬하기
$db->que = " SELECT * FROM vehicleAllocateResult 
					WHERE 1=1
						AND vr_deliveryDate='".$deliveryDate."' 
						AND vr_meridiemType='".$meridiemType."' 
						AND vr_meridiemFlag='".$meridiemFlag."' 
						AND vr_locationId='".$locationId."' 
						AND vr_vehicleNo='".$vehicleNo."' 
						  ORDER BY CAST(vr_vehicleNo AS UNSIGNED) ASC";

$db->query();
$resortVehicleRows = $db->getRows();

$nVehicleNoIndex = 0;		// vr_vehicleNoIndex 재정렬을 위해 초기화
$vr_vehicleNoIndexTmp = '';	// vr_vehicleNoIndex 재정렬을 위해 초기화
for($i=0;$i<count($resortVehicleRows);$i++){

	// 중첩지점이 아니면 nVehicleNoIndex증가, 아니면 유지
	// 중첩지점에 대한 nVehicleNoIndex 동일한 값을 부여하기 위해
	if($vr_vehicleNoIndexTmp != $resortVehicleRows[i]['vr_vehicleNoIndex']){
		$nVehicleNoIndex++;
	}
	$vr_vehicleNoIndexTmp = $resortVehicleRows[i]['vr_vehicleNoIndex'];

	$DATA["vr_vehicleNoIndex"] = $nVehicleNoIndex;

	$WHERE = " WHERE vr_seq='".$resortVehicleRows[i]['vr_seq']."' ";

	$db->Update("vehicleAllocateResult", $DATA, $WHERE, "update vehicleAllocateResult");
}


// 4. 

//index 최대치 구하기

$db->que = "SELECT 
					max(vr_vehicleNoIndex) AS maxVehicleNoIndex 
						FROM vehicleAllocateResult 
							WHERE 1=1
								AND vr_deliveryDate = '".$deliveryDate."' 
								AND vr_meridiemType='".$meridiemType."' 
								AND vr_meridiemFlag='".$meridiemFlag."' 
								AND vr_locationId='".$locationId."' 
								AND vr_vehicleNo='".$changeVehicleNo."'";

$db->query();
$maxVehicleNoIndex = $db->getOne();


/*
unset($DATA);
$DATA["vr_vehicleNoIndex"]			= $maxVehicleNoIndex+1;

$WHERE = "WHERE vr_deliveryDate='".$deliveryDate."' AND 
						  vr_meridiemType='".$meridiemType."' AND 
						  vr_locationId='".$locationId."' AND 
						  vr_vehicleNo='".$changeVehicleNo."' AND 
						  vr_deguestId='".$ci_guestId."'";

$db->Update("vehicleAllocateResult", $DATA, $WHERE, "update vehicleAllocateResult");
*/


//데이터 이동 INSERT
$maxVehicleNoIndex++;
for($i=0;$i<count($vehicleRows);$i++){
	unset($DATA);
	$DATA["vr_deguestAccno"]			= $vehicleRows[$i]['vr_deguestAccno'];
	$DATA["vr_deguestName"]				= $vehicleRows[$i]['vr_deguestName'];
	$DATA["vr_deguestTel"]				= $vehicleRows[$i]['vr_deguestTel'];
	$DATA["vr_deguestPay"]				= $vehicleRows[$i]['vr_deguestPay'];
	if($vehicleRows[$i]['vr_deguestPay']==null){
		$DATA["vr_deguestPay"] = 0;
	}
	$DATA["vr_deguestId"]				= $vehicleRows[$i]['vr_deguestId'];
	$DATA["vr_deguestJusoSubId"]		= $vehicleRows[$i]['vr_deguestJusoSubId'];
	$DATA["vr_vehicleNo"]				= $changeVehicleNo;
	$DATA["vr_vehicleNoIndex"]			= $maxVehicleNoIndex;
	$DATA["vr_deguestLat"]				= $vehicleRows[$i]["vr_deguestLat"];
	$DATA["vr_deguestLon"]				= $vehicleRows[$i]["vr_deguestLon"];
	$DATA["vr_Juso"]					= $vehicleRows[$i]["vr_Juso"];
	$DATA["vr_deliveryDate"]			= $deliveryDate;
	$DATA["vr_locationId"]				= $locationId;
	$DATA["vr_meridiemType"]			= $meridiemType;
	$DATA["vr_meridiemFlag"]			= $meridiemFlag;
	$DATA["vr_deguestIsShop"]			= $vehicleRows[$i]["vr_deguestIsShop"];
	$DATA["vr_accnoDupleJuso"]			= $vehicleRows[$i]["vr_accnoDupleJuso"];
	$DATA["vr_createDate"]				= date(("Y-m-d H:i:s"), time());
	$DATA["vr_updateDate"]				= date(("Y-m-d H:i:s"), time());
	$DATA["vr_errorJusoFlag"]			= $vehicleRows[$i]["vr_errorJusoFlag"];

	$db->Insert("vehicleAllocateResult", $DATA, "insert vehicleAllocateResult");
}


/*
//지도 라인 데이터를 없애기 위한 작업(위치 선)
$db->que = "SELECT * FROM vehicleAllocateResult 
					WHERE vr_deliveryDate='".$deliveryDate."' AND 
						  vr_meridiemType='".$meridiemType."' AND 
						  vr_locationId='".$locationId."' AND 
						  vr_guestId='".$vehicleRows[0]['vr_deguestId']."' AND 
						  vr_vehicleNo='".$vehicleRows[0]['vr_vehicleNo']."' 
						  LIMIT 1";

$db->query();
$vehicleRow = $db->getRow();

unset($DATA);
$DATA["vr_guestId"]					= "";
$DATA["vr_guestJusoSubId"]			= "";
$DATA["vr_guestLat"]				= 0;
$DATA["vr_guestLon"]				= 0;
$DATA["vr_distanceValue"]			= 0;
$DATA["vr_jsonData"]				= "";
$DATA["vr_guestIsShop"]				= "";

$WHERE = "WHERE vr_deliveryDate='".$deliveryDate."' AND 
						  vr_meridiemType='".$meridiemType."' AND 
						  vr_locationId='".$locationId."' AND 
						  vr_vehicleNo='".$vehicleRow['vr_vehicleNo']."' AND 
						  vr_vehicleNoIndex='".$vehicleRow['vr_vehicleNoIndex']."'";

$db->Update("vehicleAllocateResult", $DATA, $WHERE, "update vehicleAllocateResult");
*/

$json->add("code", "Y");
$json->result["resultMessage"] = "경로변경";
echo $json->getResult();
$db->close();
exit;

?>