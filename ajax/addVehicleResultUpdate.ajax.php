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

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

$json = new Json();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$vo_deliveryTime	= $PARAM["deliveryDate"]." 00:00:00";
$vo_deliveryDate	= $PARAM["deliveryDate"];
$vo_locationId		= $PARAM["locationId"];
$vo_meridiemType	= $PARAM["meridiemType"];
$vo_meridiemFlag	= $PARAM["meridiemFlag"];

$vehicleGuestOrderDataList = $PARAM["vehicleGuestOrderDataList"];

$DATA["ve_deliveryDate"]			= date("Y-m-d");
$WHERE =	" WHERE ve_deliveryDate = '1000-01-01'";
$db->Update("vehicleGuestOrderData", $DATA, $WHERE, " vehicleGuestOrderData Update Error ");	

//vehicleAllocateResult 배차완료 테이블 데이터 추가

for($i=0;$i<count($vehicleGuestOrderDataList);$i++){

	unset($DATA);

	// 해당경로의 
	$db->que = "SELECT 
						max(vr_vehicleNoIndex) AS maxVehicleNoIndex 
							FROM vehicleAllocateResult 
								WHERE 1=1
									AND vr_deliveryDate		= '".$vo_deliveryDate."' 
									AND vr_meridiemType		= '".$vo_meridiemType."' 
									AND vr_locationId		= '".$vo_locationId."' 
									AND vr_meridiemFlag		= '".$vo_meridiemFlag."'
									AND vr_vehicleNo		= '".$vehicleGuestOrderDataList[$i]['vehicleNo']."'";

	$db->query();
	$maxVehicleNoIndex = $db->getOne();
	$maxVehicleNoIndex++;
	
	/*
	unset($DATA);
	$DATA["vr_vehicleNoIndex"]			= $maxVehicleNoIndex+1;

	$WHERE = "WHERE vr_deliveryDate='".$deliveryDate."' AND 
							  vr_meridiemType='".$meridiemType."' AND 
							  vr_locationId='".$locationId."' AND 
							  vr_vehicleNo='".$vehicleGuestOrderDataList[$i]['vehicleNo']."' AND 
							  vr_deguestId='".$ci_guestId."'";

	$db->Update("vehicleAllocateResult", $DATA, $WHERE, "update vehicleAllocateResult");
	*/
	
	unset($DATA);
	$DATA['vr_deguestAccno']			= $vehicleGuestOrderDataList[$i]['accno'];
	$DATA['vr_deguestName']				= $vehicleGuestOrderDataList[$i]['guestName'];
	$DATA['vr_deguestTel']				= $vehicleGuestOrderDataList[$i]['guestTel'];
	$DATA['vr_deguestPay']				= $vehicleGuestOrderDataList[$i]['pay'];
	$DATA['vr_deguestId']				= $vehicleGuestOrderDataList[$i]['guestId'];
	$DATA['vr_deguestJusoSubId']		= $vehicleGuestOrderDataList[$i]['guestJusoSubId'];
	$DATA['vr_vehicleNo']				= $vehicleGuestOrderDataList[$i]['vehicleNo'];
	$DATA['vr_vehicleNoIndex']			= $maxVehicleNoIndex;
	$DATA['vr_deguestLat']				= $vehicleGuestOrderDataList[$i]['guestLat'];
	$DATA['vr_deguestLon']				= $vehicleGuestOrderDataList[$i]['guestLon'];
	$DATA['vr_Juso']					= $vehicleGuestOrderDataList[$i]['guestJuso'];
	$DATA['vr_deliveryDate']			= $vo_deliveryDate;
	$DATA['vr_locationId']				= $vo_locationId;
	$DATA['vr_meridiemType']			= $vo_meridiemType;
	$DATA['vr_meridiemFlag']			= $vehicleGuestOrderDataList[$i]['meridiemFlag'];
	$DATA['vr_deguestIsShop']			= $vehicleGuestOrderDataList[$i]['isShop'];
	$DATA['vr_createDate']				= date(('Y-m-d H:i:s'), time());
	$DATA['vr_updateDate']				= date(('Y-m-d H:i:s'), time());
	$DATA['vr_errorJusoFlag']			= 'Y';												// 에러 주소 플래그

	//if($vehicleGuestOrderDataList[$i]["guestLat"]!=0 && $vehicleGuestOrderDataList[$i]["guestLon"]!=0 && $vehicleGuestOrderDataList[$i]["guestJuso"]!=''){
	//	$db->Insert("vehicleAllocateResult", $DATA, "vehicleAllocateResult Insert Error");
	//}	
	$db->Insert("vehicleAllocateResult", $DATA, "vehicleAllocateResult Insert Error");

/*
	echo "vr_deguestAccno[".$DATA["vr_deguestAccno"]."]";	
	echo "vr_deguestName[".$DATA["vr_deguestName"]		."]";	
	echo "vr_deguestTel[".$DATA["vr_deguestTel"]		."]";	
	echo "vr_deguestPay[".$DATA["vr_deguestPay"]		."]";	
	echo "vr_deguestId[".$DATA["vr_deguestId"]		."]";	
	echo "vr_deguestJusoSubId[".$DATA["vr_deguestJusoSubId"]."]";	
	echo "vr_vehicleNo[".$DATA["vr_vehicleNo"]		."]";	
	echo "vr_vehicleNoIndex[".$DATA["vr_vehicleNoIndex"]	."]";	
	echo "vr_deguestLat[".$DATA["vr_deguestLat"]		."]";	
	echo "vr_deguestLon[".$DATA["vr_deguestLon"]		."]";	
	echo "vr_Juso[".$DATA["vr_Juso"]			."]";	
	echo "vr_deliveryDate[".$DATA["vr_deliveryDate"]	."]";	
	echo "vr_locationId[".$DATA["vr_locationId"]		."]";	
	echo "vr_meridiemType[".$DATA["vr_meridiemType"]	."]";	
	echo "vr_meridiemFlag[".$DATA["vr_meridiemType"]	."]";	
	echo "vr_deguestIsShop[".$DATA["vr_deguestIsShop"]	."]";	
	echo "vr_createDate[".$DATA["vr_createDate"]		."]";	
	echo "vr_updateDate[".$DATA["vr_updateDate"]		."]";	
	echo "vr_errorJusoFlag[".$DATA["vr_errorJusoFlag"]	."]";	
*/


}

//exit;

$json->add("vehicleGuestOrderDataList", $vehicleGuestOrderDataList);
$json->result["resultMessage"] = "배송경로추가데이터";
echo $json->getResult();
$db->close();
exit;

?>