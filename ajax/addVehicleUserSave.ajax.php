<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Step01
// �� ������Ʈ���� API
// ERP Server <===== local Server (call)
// ERP Server�κ��� �������� ����� �����͸� �����ͼ� ���������� ������Ʈ�մϴ�.
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/mysql.inc.php";
include "../inc/user.inc.php";
include "../inc/json.inc.php";
include "../inc/lib.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

// ERP���� ����� PARAM ���� ���� ������ ������, ERP���������� �Ѿ���� ������ Ȯ�� �ʿ�!!!
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

	if($userInfo['vg_guestId']){	// �������� ������, ���⼭ update

		// ������ update
		if($userInfo['vg_guestLat']!=$vehicleGuestOrderDataList[$i]['guestLat'] || 
			$userInfo['vg_guestLon']!=$vehicleGuestOrderDataList[$i]['guestLon']){

			$DATA["vg_guestName"]		= $vehicleGuestOrderDataList[$i]['guestName'];		// �̸�
			$DATA["vg_guestJuso"]		= $vehicleGuestOrderDataList[$i]['guestJuso'];		// �ּ�
			$DATA["vg_guestLat"]		= $vehicleGuestOrderDataList[$i]['guestLat'];		// ����(lat)
			$DATA["vg_guestLon"]		= $vehicleGuestOrderDataList[$i]['guestLon'];		// �浵(lon)
			$DATA["vg_modifyStatus"]	= "Y";												// �������
			$DATA["vg_updateDate"]		= date(("Y-m-d H:i:s"), time());					// ���� ��¥

			if((empty($vehicleGuestOrderDataList[$i]['guestJuso'])) || ($vehicleGuestOrderDataList[$i]['guestLat']==0) || ($vehicleGuestOrderDataList[$i]['guestLon']==0)){
				$DATA["vg_isJuso"]			= "N";
			}

			$WHERE  = " WHERE vg_guestId='".$userInfo['vg_guestId']."'";
			$WHERE .= " AND vg_guestJusoSubId='".$userInfo['vg_guestJusoSubId']."'";
			$WHERE .= " AND vg_isShop='".$userInfo['vg_isShop']."'";

			$db->Update("vehicleGuestInfo", $DATA, $WHERE, " vehicleGuestInfo Update Error ");
		
		}

	} else {					// �������� ������, ���⼭ insert

		// ������ insert
		$DATA["vg_guestId"]			= $vehicleGuestOrderDataList[$i]['guestId'];		// ��ID
		$DATA["vg_guestName"]		= $vehicleGuestOrderDataList[$i]['guestName'];		// �̸�
		$DATA["vg_guestJuso"]		= $vehicleGuestOrderDataList[$i]['guestJuso'];		// �ּ�
		$DATA["vg_guestJusoSubId"]	= $vehicleGuestOrderDataList[$i]['guestJusoSubId'];	// �ּ��ε���
		$DATA["vg_guestLat"]		= $vehicleGuestOrderDataList[$i]['guestLat'];		// ����(lat)
		$DATA["vg_guestLon"]		= $vehicleGuestOrderDataList[$i]['guestLon'];		// �浵(lon)
		$DATA["vg_isShop"]			= $vehicleGuestOrderDataList[$i]['isShop'];			// �浵(lon)
		$DATA["vg_modifyStatus"]	= "Y";												// �������
		$DATA["vg_createDate"]		= date(("Y-m-d H:i:s"), time());					// ������¥
		$DATA["vg_updateDate"]		= $vehicleGuestOrderDataList[$i]['updateDate'];		// ������¥

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