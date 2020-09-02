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

$vehicleGuestOrderDataList = $PARAM['vehicleGuestOrderData'];

$deliveryDate	= $PARAM["deliveryDate"];	// 배송날짜
$locationId		= $PARAM["locationId"];		// 거점ID
$meridiemType	= $PARAM["meridiemType"];	// 오전,오후 
$meridiemFlag	= $PARAM["meridiemFlag"];	// 오전,오후 분할 플래그 ex) 1,2,3,4....


//고객 정보 테이블 INSERT, UPDATE
for($i=0;$i<count($vehicleGuestOrderDataList);$i++){
	unset($DATA);
	$paramUserGuestId = trim($vehicleGuestOrderDataList[$i]['guestId']);
	$paramUserJusoSubId = trim($vehicleGuestOrderDataList[$i]['jusoSubId']);
	$paramUserIsShop	= trim($vehicleGuestOrderDataList[$i]['isShop']);
	$db->que  = " SELECT vg_guestId,vg_guestJusoSubId,vg_guestLat,vg_guestLon,vg_isShop
						FROM vehicleGuestInfo WHERE 1=1
							AND vg_guestId='".$paramUserGuestId."' 
							AND vg_guestJusoSubId='".$paramUserJusoSubId."' 
							AND vg_isShop='".$paramUserIsShop."'";

	$db->query();
	$userInfo = $db->getRow();
	
	if($userInfo['vg_guestId']){
		
		// 고객정보 update
		if($userInfo['vg_guestLat']!=$vehicleGuestOrderDataList[$i]['lat'] || 
			$userInfo['vg_guestLon']!=$vehicleGuestOrderDataList[$i]['lon']){

			$DATA["vg_guestName"]		= $vehicleGuestOrderDataList[$i]['name'];		// 이름
			$DATA["vg_guestJuso"]		= $vehicleGuestOrderDataList[$i]['juso'];		// 주소
			$DATA["vg_guestLat"]		= $vehicleGuestOrderDataList[$i]['lat'];		// 위도(lat)
			$DATA["vg_guestLon"]		= $vehicleGuestOrderDataList[$i]['lon'];		// 경도(lon)
			$DATA["vg_modifyStatus"]	= "Y";											// 변경상태
			$DATA["vg_updateDate"]		= date(("Y-m-d H:i:s"), time());				// 수정 날짜

			if((empty($vehicleGuestOrderDataList[$i]['juso'])) || ($vehicleGuestOrderDataList[$i]['lat']==0) || ($vehicleGuestOrderDataList[$i]['lon']==0)){
				$DATA["vg_isJuso"]			= "N";
			}

			$WHERE  = " WHERE vg_guestId='".$userInfo['vg_guestId']."'";
			$WHERE .= " AND vg_guestJusoSubId='".$userInfo['vg_guestJusoSubId']."'";
			$WHERE .= " AND vg_isShop='".$userInfo['vg_isShop']."'";

			$db->Update("vehicleGuestInfo", $DATA, $WHERE, " vehicleGuestInfo Update Error ");
		
		}

	} else {
		
		// 고객정보 insert
		$DATA["vg_guestId"]			= $vehicleGuestOrderDataList[$i]['guestId'];	// 고객ID
		$DATA["vg_guestName"]		= $vehicleGuestOrderDataList[$i]['name'];		// 이름
		$DATA["vg_guestJuso"]		= $vehicleGuestOrderDataList[$i]['juso'];		// 주소
		$DATA["vg_guestJusoSubId"]	= $vehicleGuestOrderDataList[$i]['jusoSubId'];	// 주소인덱스
		$DATA["vg_guestLat"]		= $vehicleGuestOrderDataList[$i]['lat'];		// 위도(lat)
		$DATA["vg_guestLon"]		= $vehicleGuestOrderDataList[$i]['lon'];		// 경도(lon)
		$DATA["vg_isShop"]			= $vehicleGuestOrderDataList[$i]['isShop'];		// ERP,SHOP
		$DATA["vg_modifyStatus"]	= "Y";											// 변경상태
		$DATA["vg_createDate"]		= date(("Y-m-d H:i:s"), time());				// 생성날짜
		$DATA["vg_updateDate"]		= date(("Y-m-d H:i:s"), time());				// 수정날짜

		if((empty($vehicleGuestOrderDataList[$i]['juso'])) || ($vehicleGuestOrderDataList[$i]['lat']==0) || ($vehicleGuestOrderDataList[$i]['lon']==0)){
			$DATA["vg_isJuso"]			= "N";
		}
		$db->Insert("vehicleGuestInfo", $DATA, " vehicleGuestInfo Insert Error ");

	}
}

unset($DATA);
//거점 주소 INSERT
$ci_guestId = $_SESSION["OMember_id"];
$db->que = "SELECT * FROM companyInfo WHERE ci_guestId='".$ci_guestId."'";
$db->query();
$companyRow = $db->getRow();

$db->que = "SELECT * FROM vehicleGuestInfo WHERE vg_guestId='".$ci_guestId."'";
$db->query();

if($db->affected_rows() < 1){
	// 거점주소 insert
	$DATA["vg_guestId"]			= $companyRow['ci_guestId'];
	$DATA["vg_guestName"]		= $companyRow['ci_guestId'];
	$DATA["vg_guestJuso"]		= $companyRow['ci_juso'];		
	$DATA["vg_guestJusoSubId"]	= '1';
	$DATA["vg_guestLat"]		= $companyRow['ci_lat'];
	$DATA["vg_guestLon"]		= $companyRow['ci_lon'];
	$DATA["vg_isShop"]			= "0";
	$DATA["vg_modifyStatus"]	= "Y";												
	$DATA["vg_createDate"]		= date(("Y-m-d H:i:s"), time());					
	$DATA["vg_updateDate"]		= date(("Y-m-d H:i:s"), time());
	$db->Insert("vehicleGuestInfo", $DATA, " vehicleGuestInfo Insert Error ");
}


//주문 데이터 조회
$db->que = " SELECT ve_seq AS seq
					,ve_guestId			AS guestId
					,ve_guestJusoSubId	AS guestJusoSubId
					,ve_guestLat		AS guestLat
					,ve_guestLon		AS guestLon
					,ve_isShop			AS isShop
						FROM vehicleGuestOrderData 
						WHERE 1=1
						AND ve_locationId='".$locationId."' 
						AND ve_deliveryDate='".$deliveryDate."' 
						AND ve_meridiemType='".$meridiemType."' 
						AND ve_meridiemFlag='".$meridiemFlag."' 
						AND ve_guestId!='".$ci_guestId."' 
						AND ve_isJuso='Y' 
						GROUP BY ve_guestId,ve_guestJusoSubId,ve_isShop 
						 ORDER BY ve_seq asc";
$db->query();

$vehicleOrderData	= $db->getRows();

$json->add("vehicleOrderData", $vehicleOrderData);
$json->result["resultMessage"] = "고객배송중복제거데이터";
echo $json->getResult();
$db->close();
exit;

?>