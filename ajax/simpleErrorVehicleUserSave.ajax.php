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

// ERP서버 적용시 PARAM 값이 맞지 않을수 있으니, ERP서버측에서 넘어오는 데이터 확인 필요!!!
$PARAM = json_decode(file_get_contents("php://input"), JSON_UNESCAPED_UNICODE);


$deliveryDate			= $PARAM['deliveryDate'];
$meridiemType			= $PARAM['meridiemType'];
$meridiemFlag			= $PARAM['meridiemFlag'];
$locationId				= $PARAM['locationId'];
$ci_guestId				= $_SESSION["OMember_id"];
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$json = new Json();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$vehicleErrorUserList = $PARAM['vehicleErrorUserList'];
//유저 정보 수정
for($i=0;$i<count($vehicleErrorUserList);$i++){
	unset($DATA);
	$WHERE = " WHERE 1=1
					AND vg_guestId='".$vehicleErrorUserList[$i]['guestId']."'
					AND vg_guestJusoSubId='".$vehicleErrorUserList[$i]['guestJusoSubId']."'
					AND vg_isShop='".$vehicleErrorUserList[$i]['isShop']."'"; 
					
	$DATA["vg_guestJuso"]		= $vehicleErrorUserList[$i]['guestJuso'];		// 주소
	$DATA["vg_guestLat"]		= $vehicleErrorUserList[$i]['guestLat'];		// 위도(lat)
	$DATA["vg_guestLon"]		= $vehicleErrorUserList[$i]['guestLon'];		// 경도(lon)
	$DATA["vg_isJuso"]			= "Y";											// 주소 체크
	$DATA["vg_updateDate"]		= date(("Y-m-d H:i:s"), time());				// 현재 시각

	$db->Update("vehicleGuestInfo", $DATA, $WHERE, " vehicleGuestInfo Insert Error ");
}
//주문 정보 수정
for($i=0;$i<count($vehicleErrorUserList);$i++){
	unset($DATA);
	$WHERE = " WHERE 1=1
					AND ve_deliveryDate='".$deliveryDate."'	
					AND ve_meridiemType='".$meridiemType."'
					AND ve_meridiemFlag='".$meridiemFlag."'
					AND ve_locationId='".$locationId."'
					AND ve_guestId='".$vehicleErrorUserList[$i]['guestId']."'
					AND ve_guestJusoSubId='".$vehicleErrorUserList[$i]['guestJusoSubId']."'
					AND ve_isShop='".$vehicleErrorUserList[$i]['isShop']."'";  
					
	$DATA["ve_guestJuso"]		= $vehicleErrorUserList[$i]['guestJuso'];		// 주소
	$DATA["ve_guestLat"]		= $vehicleErrorUserList[$i]['guestLat'];		// 위도(lat)
	$DATA["ve_guestLon"]		= $vehicleErrorUserList[$i]['guestLon'];		// 경도(lon)
	$DATA["ve_isJuso"]			= "Y";											// 주소 체크
	$db->Update("vehicleGuestOrderData", $DATA, $WHERE, " vehicleGuestInfo Insert Error ");
}

//vehicleAllocateResult 배차완료 테이블 데이터 추가
for($i=0;$i<count($vehicleErrorUserList);$i++){
	$db->que = "SELECT 
						max(vr_vehicleNoIndex) AS maxVehicleNoIndex 
							FROM vehicleAllocateResult 
								WHERE 1=1
									AND vr_deliveryDate = '".$deliveryDate."'
									AND vr_meridiemType='".$meridiemType."'
									AND vr_meridiemFlag='".$meridiemFlag."'
									AND vr_locationId='".$locationId."' 
									AND vr_vehicleNo='".$vehicleErrorUserList[$i]['vehicleNo']."'";

	$db->query();
	$maxVehicleNoIndex = $db->getOne();
	
	unset($DATA);
	$DATA["vr_vehicleNoIndex"]			= $maxVehicleNoIndex+1;

	$WHERE = "WHERE 1=1 
				AND vr_deliveryDate='".$deliveryDate."'
				AND vr_meridiemType='".$meridiemType."'
				AND vr_meridiemFlag='".$meridiemFlag."'
				AND vr_locationId='".$locationId."'
				AND vr_vehicleNo='".$vehicleErrorUserList[$i]['vehicleNo']."'
				AND vr_deguestId='".$ci_guestId."'";

	$db->Update("vehicleAllocateResult", $DATA, $WHERE, "update vehicleAllocateResult");
	
	unset($DATA);
	$DATA["vr_deguestAccno"]			= $vehicleErrorUserList[$i]['accno'];
	$DATA["vr_deguestName"]				= $vehicleErrorUserList[$i]['guestName'];
	$DATA["vr_deguestTel"]				= $vehicleErrorUserList[$i]['guestTel'];
	$DATA["vr_deguestPay"]				= $vehicleErrorUserList[$i]['guestPay'];
	$DATA["vr_deguestId"]				= $vehicleErrorUserList[$i]['guestId'];
	$DATA["vr_deguestJusoSubId"]		= $vehicleErrorUserList[$i]['guestJusoSubId'];
	$DATA["vr_vehicleNo"]				= $vehicleErrorUserList[$i]['vehicleNo'];
	$DATA["vr_vehicleNoIndex"]			= $maxVehicleNoIndex;
	$DATA["vr_deguestLat"]				= $vehicleErrorUserList[$i]["guestLat"];
	$DATA["vr_deguestLon"]				= $vehicleErrorUserList[$i]["guestLon"];
	$DATA["vr_Juso"]					= $vehicleErrorUserList[$i]["guestJuso"];
	$DATA["vr_deliveryDate"]			= $deliveryDate;
	$DATA["vr_locationId"]				= $locationId;
	$DATA["vr_meridiemType"]			= $meridiemType;
	$DATA["vr_meridiemFlag"]			= $meridiemFlag;
	$DATA["vr_deguestIsShop"]			= $vehicleErrorUserList[$i]["isShop"];
	$DATA["vr_createDate"]				= date(("Y-m-d H:i:s"), time());
	$DATA["vr_updateDate"]				= date(("Y-m-d H:i:s"), time());


	$db->Insert("vehicleAllocateResult", $DATA, "insert vehicleAllocateResult");
		
}
//대구광역시 동구 용계동 433 서도산업
//대구광역시 서구 평리동 438-5 와이디텍스타일


echo "ok";
$db->close();
exit;

?>