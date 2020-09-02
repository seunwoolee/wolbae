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

$vehicleGuestOrderDataList = $PARAM['vehicleGuestOrderDataList'];

for($i=0;$i<count($vehicleGuestOrderDataList);$i++){

	$db->que  = " SELECT	vg_guestId
							,vg_guestJusoSubId
							,vg_guestLat
							,vg_guestLon
							,vg_isShop 
						FROM vehicleGuestInfo 
							WHERE 1=1
								AND vg_guestId='".$vehicleGuestOrderDataList[$i]['guestId']."'
								AND vg_guestJusoSubId='".$vehicleGuestOrderDataList[$i]['guestJusoSubId']."' 
								AND vg_isShop='".$vehicleGuestOrderDataList[$i]['isShop']."'";

	$db->query();
	$userInfo = $db->getRow();

	unset($DATA);

	if($userInfo['vg_guestId']){	// 고객정보가 있으면, 여기서 update

		// 고객정보 update
		if($userInfo['vg_guestLat']!=$vehicleGuestOrderDataList[$i]['guestLat'] || 
			$userInfo['vg_guestLon']!=$vehicleGuestOrderDataList[$i]['guestLon']){

			$DATA["vg_guestName"]		= $vehicleGuestOrderDataList[$i]['guestName'];		// 이름
			$DATA["vg_guestJuso"]		= $vehicleGuestOrderDataList[$i]['guestJuso'];		// 주소
			$DATA["vg_guestLat"]		= $vehicleGuestOrderDataList[$i]['guestLat'];		// 위도(lat)
			$DATA["vg_guestLon"]		= $vehicleGuestOrderDataList[$i]['guestLon'];		// 경도(lon)
			$DATA["vg_modifyStatus"]	= "Y";												// 변경상태
			$DATA["vg_updateDate"]		= date(("Y-m-d H:i:s"), time());					// 수정 날짜

			if((empty($vehicleGuestOrderDataList[$i]['guestJuso'])) || ($vehicleGuestOrderDataList[$i]['guestLat']==0) || ($vehicleGuestOrderDataList[$i]['guestLon']==0)){
				$DATA["vg_isJuso"]			= "N";
			}

			$WHERE  = " WHERE vg_guestId='".$userInfo['vg_guestId']."'";
			$WHERE .= " AND vg_guestJusoSubId='".$userInfo['vg_guestJusoSubId']."'";
			$WHERE .= " AND vg_isShop='".$userInfo['vg_isShop']."'";

			$db->Update("vehicleGuestInfo", $DATA, $WHERE, " vehicleGuestInfo Update Error ");
		
		}

	} else {					// 고객정보가 없으면, 여기서 insert

		// 고객정보 insert
		$DATA["vg_guestId"]			= $vehicleGuestOrderDataList[$i]['guestId'];		// 고객ID
		$DATA["vg_guestName"]		= $vehicleGuestOrderDataList[$i]['guestName'];		// 이름
		$DATA["vg_guestJuso"]		= $vehicleGuestOrderDataList[$i]['guestJuso'];		// 주소
		$DATA["vg_guestJusoSubId"]	= $vehicleGuestOrderDataList[$i]['guestJusoSubId'];	// 주소인덱스
		$DATA["vg_guestLat"]		= $vehicleGuestOrderDataList[$i]['guestLat'];		// 위도(lat)
		$DATA["vg_guestLon"]		= $vehicleGuestOrderDataList[$i]['guestLon'];		// 경도(lon)
		$DATA["vg_isShop"]			= $vehicleGuestOrderDataList[$i]['isShop'];			// 경도(lon)
		$DATA["vg_modifyStatus"]	= "Y";												// 변경상태
		$DATA["vg_createDate"]		= date(("Y-m-d H:i:s"), time());					// 생성날짜
		$DATA["vg_updateDate"]		= $vehicleGuestOrderDataList[$i]['updateDate'];		// 수정날짜

		if((empty($vehicleGuestOrderDataList[$i]['guestJuso'])) || ($vehicleGuestOrderDataList[$i]['guestLat']==0) || ($vehicleGuestOrderDataList[$i]['guestLon']==0)){
			$DATA["vg_isJuso"]			= "N";
		}

		$db->Insert("vehicleGuestInfo", $DATA, " vehicleGuestInfo Insert Error ");
	}

}

echo "ok";
$db->close();
exit;

?>