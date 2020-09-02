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
$ci_guestId			= $_SESSION["OMember_id"];

//$vo_deliveryTime	= $PARAM["deliveryDate"]." 00:00:00";
$vo_deliveryDate	= $PARAM["deliveryDate"];
$vo_meridiemType	= $PARAM["meridiemType"];
$vo_meridiemFlag	= $PARAM["meridiemFlag"];	// 추가
$vo_locationId		= $PARAM["locationId"];

$db->que = "DELETE FROM vehicleGuestOrderData WHERE ve_deliveryDate='1000-01-01'";
$db->query();

$Erp_vehicleGuestOrderDataList = $PARAM["item"];
//$vehicleGuestOrderDataList;
$count=0;
//Erp_vehicleGuestOrderData날짜와 vehicleGuestInfo의 날짜를 비교하여 다를시 주소값 변경



for($i=0;$i<count($Erp_vehicleGuestOrderDataList);$i++){
//for($i=0;$i<2;$i++){
	unset($DATA);



	// 중복건 채크하는부분인데, 이거 함더 봐야함...!!!!
	$paramGuestId	= trim($Erp_vehicleGuestOrderDataList[$i]['guestId']);
	$paramJusoSubId	= trim($Erp_vehicleGuestOrderDataList[$i]['jusoSubid']);
	$paramIsShop	= trim($Erp_vehicleGuestOrderDataList[$i]['isShop']);
	
	$db->que  = " SELECT count(*) AS rowCount 
					FROM vehicleGuestOrderData 
						WHERE 1=1 
							AND ve_guestId			= '".$paramGuestId."'
							AND ve_guestJusoSubId	= '".$paramJusoSubId."' 
							AND ve_isShop			= '".$paramIsShop."'

							AND ve_deliveryDate		= '".$vo_deliveryDate."' 
							AND ve_meridiemType		= '".$vo_meridiemType."'
							AND ve_meridiemFlag		= '".$vo_meridiemFlag."'
							AND ve_locationId		= '".$vo_locationId."'";
	$db->query();
	$rowCount = $db->getOne();

	//echo " SELECT count(*) AS rowCount FROM vehicleGuestOrderData WHERE 1=1  AND ve_guestId			= '".$paramGuestId."' AND ve_guestJusoSubId	= '".$paramJusoSubId."' AND ve_isShop			= '".$paramIsShop."' AND ve_deliveryDate		= '".$vo_deliveryDate."' AND ve_meridiemType		= '".$vo_meridiemType."' AND ve_meridiemFlag		= '".$vo_meridiemFlag."' AND ve_locationId		= '".$vo_locationId."'";
	//echo "<br>";
	//echo "[".$rowCount."]";
	//echo "<br>";

	// 당일배송건 vehicleGuestOrderData 테이블 비교해서, 한건도 없으면 새로 추가된건임
	if($rowCount == 0){
		unset($DATA);

		$DATA["ve_accno"]			= $Erp_vehicleGuestOrderDataList[$i]['accno'];			// 주문번호
		$DATA["ve_locationId"]		= $Erp_vehicleGuestOrderDataList[$i]['locationId'];		// 거점ID
		$DATA["ve_deliveryDate"]	= "1000-01-01";											// 추가된놈임을 구분하기위해 표시용으로 날짜를 이따위로 해 놓음
		$DATA["ve_meridiemType"]	= $Erp_vehicleGuestOrderDataList[$i]['meridiemType'];	// 오전오후배송
		$DATA["ve_guestId"]			= $Erp_vehicleGuestOrderDataList[$i]['guestId'];		// 고객ID
		$DATA["ve_guestName"]		= $Erp_vehicleGuestOrderDataList[$i]['name'];			// 이름
		$DATA["ve_guestTel"]		= $Erp_vehicleGuestOrderDataList[$i]['guestTel'];		// 고객 전화번호
		$DATA["ve_guestJuso"]		= $Erp_vehicleGuestOrderDataList[$i]['juso'];			// 주소
		$DATA["ve_guestJusoSubId"]	= $Erp_vehicleGuestOrderDataList[$i]['jusoSubid'];		// 주소인덱스
		$DATA["ve_pay"]				= $Erp_vehicleGuestOrderDataList[$i]['pay'];			// 금액
		$DATA["ve_guestLat"]		= $Erp_vehicleGuestOrderDataList[$i]['lat'];			// 위도(lat)
		$DATA["ve_guestLon"]		= $Erp_vehicleGuestOrderDataList[$i]['lon'];			// 경도(lon)

		$DATA["ve_meridiemFlag"]	= $Erp_vehicleGuestOrderDataList[$i]['flag'];			// 오전,오후 플래그 추가됨

		if($Erp_vehicleGuestOrderDataList[$i]['lat']==null){
			$DATA["ve_guestLat"]	= 0;
		}

		if($Erp_vehicleGuestOrderDataList[$i]['lon']==null){
			$DATA["ve_guestLon"]	= 0;
		}

		$DATA["ve_isNew"]			= $Erp_vehicleGuestOrderDataList[$i]['isNew'];			// 
		$DATA["ve_isShop"]			= $Erp_vehicleGuestOrderDataList[$i]['isShop'];			// 
		$DATA["ve_isRoad"]			= $Erp_vehicleGuestOrderDataList[$i]['isRoad'];			// 
		$DATA["ve_createDate"]		= date(("Y-m-d H:i:s"), time());						// 생성날짜

		$DATA["ve_isJuso"]			= "N";

		$DATA["ve_errorJusoFlag"]	= "Y";

		//$json->add("dfdfdfdf".$i, $DATA);


		/*
		// 주소, 위도, 경도중 하나라도 값이 없으면, ve_isJuso 상태값 'N' 으로 처리
		if((empty($Erp_vehicleGuestOrderDataList[$i]['juso'])) || ($Erp_vehicleGuestOrderDataList[$i]['lat']==null) ||($Erp_vehicleGuestOrderDataList[$i]['lon']==null)){
			$DATA["ve_isJuso"]			= "N";
		} else {
			// ve_isJuso 컬럼타입이 default 'Y' 이므로, 처리안함
		}
		*/
		

		$db->Insert("vehicleGuestOrderData", $DATA, " vehicleGuestOrderData Insert Error ");		
	}
}

/*
exit;
$json->result["resultMessage"] = "dfdfdf";
echo $json->getResult();
$db->close();
exit;
*/

/*
$db->que = " SELECT ve_accno				AS accno 
					,ve_deliveryDate		AS deliveryDate
					,ve_guestId				AS guestId
					,ve_guestTel			AS guestTel 
					,ve_guestName			AS name 
					,ve_guestJusoSubId		AS jusoSubid 
					,ve_guestJuso			AS juso 
					,ve_guestLat			AS lat 
					,ve_guestLon			AS lon
					,ve_isShop				AS isShop 
					,ve_isNew				AS isNew 
					,ve_isRoad				AS isRoad 
					,ve_locationId			AS locationId 
					,ve_meridiemType		AS meridiemType 
					,ve_meridiemFlag		AS meridiemFlag
					,sum(ve_pay)			AS pay 
					,ve_createDate			AS updateDate 
						FROM vehicleGuestOrderData 
							WHERE 1=1
								AND ve_deliveryDate	= '1000-01-01' 
								AND ve_meridiemType	= '".$vo_meridiemType."' 
								AND ve_meridiemFlag	= '".$vo_meridiemFlag."' 
								AND ve_locationId	= '".$vo_locationId."'
									GROUP BY ve_guestId, ve_guestJusoSubId, ve_isShop ";
*/

$db->que = " SELECT ve_accno				AS accno 
					,ve_deliveryDate		AS deliveryDate
					,ve_guestId				AS guestId
					,ve_guestTel			AS guestTel 
					,ve_guestName			AS name 
					,ve_guestJusoSubId		AS jusoSubid 
					,ve_guestJuso			AS juso 
					,ve_guestLat			AS lat 
					,ve_guestLon			AS lon
					,ve_isShop				AS isShop 
					,ve_isNew				AS isNew 
					,ve_isRoad				AS isRoad 
					,ve_locationId			AS locationId 
					,ve_meridiemType		AS meridiemType 
					,ve_meridiemFlag		AS meridiemFlag
					,ve_pay					AS pay 
					,ve_createDate			AS updateDate 
						FROM vehicleGuestOrderData 
							WHERE 1=1
								AND ve_deliveryDate	= '1000-01-01' 
								AND ve_meridiemType	= '".$vo_meridiemType."' 
								AND ve_meridiemFlag	= '".$vo_meridiemFlag."' 
								AND ve_locationId	= '".$vo_locationId."' ";
$db->query();
$row = $db->getRows();

for($i=0;$i<count($row);$i++){

	$vehicleGuestOrderDataList[$count]['accno']				= $row[$i]['accno'];
	$vehicleGuestOrderDataList[$count]['deliveryDate']		= date("Y-m-d");
	$vehicleGuestOrderDataList[$count]['guestId']			= $row[$i]['guestId'];
	$vehicleGuestOrderDataList[$count]['guestName']			= $row[$i]['name'];
	$vehicleGuestOrderDataList[$count]['guestTel']			= $row[$i]['guestTel'];
	$vehicleGuestOrderDataList[$count]['guestJusoSubId']	= $row[$i]['jusoSubid'];
	$vehicleGuestOrderDataList[$count]['guestJuso']			= $row[$i]['juso'];
	$vehicleGuestOrderDataList[$count]['guestLat']			= $row[$i]['lat'];
	$vehicleGuestOrderDataList[$count]['guestLon']			= $row[$i]['lon'];
	$vehicleGuestOrderDataList[$count]['isShop']			= $row[$i]['isShop'];
	$vehicleGuestOrderDataList[$count]['isNew']				= $row[$i]['isNew'];
	$vehicleGuestOrderDataList[$count]['isRoad']			= $row[$i]['isRoad'];
	$vehicleGuestOrderDataList[$count]['locationId']		= $row[$i]['locationId'];
	$vehicleGuestOrderDataList[$count]['meridiemType']		= $row[$i]['meridiemType'];
	$vehicleGuestOrderDataList[$count]['meridiemFlag']		= $row[$i]['meridiemFlag'];
	$vehicleGuestOrderDataList[$count]['pay']				= $row[$i]['pay'];
	$vehicleGuestOrderDataList[$count]['updateDate']		= $row[$i]['updateDate'];
	$count++;

}
//LIB::PLog("wewfwfcsefssexexesx1");

// 이건 경로 뽑을라고??		
$db->que = "SELECT   A.vr_vehicleNo				AS vehicleNo
					,sum(A.vr_distanceValue)	AS distanceValue
						FROM (
								SELECT * FROM vehicleAllocateResult 
									WHERE 1=1 
										AND vr_deliveryDate='".$vo_deliveryDate."' 
										AND vr_meridiemType='".$vo_meridiemType."' 
										AND vr_meridiemFlag='".$vo_meridiemFlag."' 
										AND vr_locationId='".$vo_locationId."'  
											GROUP BY vr_vehicleNo,vr_vehicleNoIndex
						) AS A 
								GROUP BY A.vr_vehicleNo 
								ORDER BY A.vr_vehicleNo*1 ASC";

$db->query();
$vehicleAllocateResultList = $db->getRows();
//LIB::PLog("wewfwfcsefssexexesx");
$json->add("vehicleAllocateResultList", $vehicleAllocateResultList);
$json->add("vehicleGuestOrderDataList", $vehicleGuestOrderDataList);
$json->result["resultMessage"] = "고객정보데이터";
echo $json->getResult();
$db->close();
exit;

?>