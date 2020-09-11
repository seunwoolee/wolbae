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
//Json Class
$json = new Json();
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


//1. 넘겨받을 변수 확인
$deliveryDate	= $PARAM["deliveryDate"];	// 배송날짜		
$locationId		= $PARAM["locationId"];		// 거점ID
$meridiemType	= $PARAM["meridiemType"];	// 오전,오후 값
$meridiemFlag	= $PARAM["meridiemFlag"];	// 오전,오후 분할 플래그 


//echo $deliveryDate;
//echo $locationId;
//echo $meridiemType;
//echo $meridiemFlag;



//2. 현재날짜 기준으로 vehicleGuestOrderData 테이블 내용을 삭제 해야합니다.
//$WHERE = "WHERE ve_locationId='".$locationId."' 
//		  AND ve_deliveryDate='".$deliveryDate."' AND ve_meridiemType='".$meridiemType."'";
$WHERE = "WHERE 1=1
			AND ve_locationId='".$locationId."' 
			AND ve_deliveryDate='".$deliveryDate."' 
			AND ve_meridiemType='".$meridiemType."' 
			AND ve_meridiemFlag='".$meridiemFlag."' ";

$db->que = "DELETE FROM vehicleGuestOrderData ".$WHERE;
$db->query();

// 2.1 현재날짜 기준으로 vehicleAllocateStatus 테이블 내용을 삭제 해야합니다.
$WHERE = "WHERE 1=1
			AND vs_locationId='".$locationId."' 
			AND vs_deliveryDate='".$deliveryDate."' 
			AND vs_meridiemType='".$meridiemType."' 
			AND vs_meridiemFlag='".$meridiemFlag."' ";

$db->que = "DELETE FROM vehicleAllocateStatus ".$WHERE;
$db->query();


//LIB::PLog("count:".count($PARAM['item']));


//3. 거점서버DB에 ERP데이터 등록
for($i=0;$i<count($PARAM['item']);$i++){		// 실서버 적용시 RAPAM 배열ID 변경해야됩니다.
	unset($DATA);
	
	// 셈플코드임 (고객배송정보 insert )
	$DATA["ve_accno"]			= $PARAM['item'][$i]['accno'];			// 주문번호
	$DATA["ve_locationId"]		= $PARAM['item'][$i]['locationId'];		// 거점ID
	$strDelivery				= $PARAM['item'][$i]['deliveryDate'];

	$DATA["ve_deliveryDate"]	= substr($strDelivery,0,4)."-".substr($strDelivery,5,2)."-".substr($strDelivery,8,2);// 배송날짜
	$DATA["ve_meridiemType"]	= $PARAM['item'][$i]['meridiemType'];	// 오전오후배송

	$DATA["ve_meridiemFlag"]	= $PARAM['item'][$i]['flag'];			// 오전오후배송 분할 인덱스 -  추가된 데이터임

	$DATA["ve_guestId"]			= $PARAM['item'][$i]['guestId'];		// 고객ID
	$DATA["ve_guestName"]		= $PARAM['item'][$i]['name'];			// 이름
	$DATA["ve_guestTel"]		= $PARAM['item'][$i]['guestTel'];		// 고객 전화번호
	$DATA["ve_guestJusoSubId"]	= $PARAM['item'][$i]['jusoSubid'];		// 주소인덱스
	$DATA["ve_guestJuso"]		= $PARAM['item'][$i]['juso'];			// 주소
	$DATA["ve_pay"]				= $PARAM['item'][$i]['pay'];			// 금액
	
	if($PARAM['item'][$i]['pay'] < 0){
		$DATA["ve_pay"]			= 0;
	}

	$DATA["ve_guestLat"]		= $PARAM['item'][$i]['lat'];			// 위도(lat)
	$DATA["ve_guestLon"]		= $PARAM['item'][$i]['lon'];			// 경도(lon)
	if($PARAM['item'][$i]['lat']==null){
		$DATA["ve_guestLat"] = 0;
	}
	if($PARAM['item'][$i]['lon']==null){
		$DATA["ve_guestLon"] = 0;
	}
	$DATA["ve_isNew"]			= $PARAM['item'][$i]['isNew'];			// 최신 주소 수정날짜
	$DATA["ve_isShop"]			= $PARAM['item'][$i]['isShop'];			// ERP,SHOP 구별
	$DATA["ve_isRoad"]			= $PARAM['item'][$i]['isRoad'];			// 신주소,구주소
	$DATA["ve_createDate"]		= date(("Y-m-d H:i:s"), time());		// 생성날짜
	
	if((empty($PARAM['item'][$i]['juso'])) || ($PARAM['item'][$i]['lat']==null) || ($PARAM['item'][$i]['lon']==null)){
		$DATA["ve_isJuso"]			= "N";
	}

	$db->Insert("vehicleGuestOrderData", $DATA, " vehicleGuestOrderData Insert Error ");
	//주소 및 위,경도 공백 및 0값 체크
	

}
//4. 출발지 주소도 등록
unset($DATA);

$ci_guestId = $_SESSION["OMember_id"];
$db->que = "SELECT * FROM companyInfo WHERE ci_guestId='".$ci_guestId."'";
$db->query();
$companyRow = $db->getRow();

// 셈플코드임 (고객배송정보 insert )
$DATA["ve_accno"]			= $companyRow['ci_no'];
$DATA["ve_locationId"]		= $companyRow['ci_no'];
$DATA["ve_deliveryDate"]	= $deliveryDate;
$DATA["ve_meridiemType"]	= $meridiemType;
$DATA["ve_meridiemFlag"]	= $meridiemFlag;
$DATA["ve_guestId"]			= $companyRow['ci_guestId'];
$DATA["ve_guestName"]		= $companyRow['ci_guestId'];
$DATA["ve_guestTel"]		= "";
$DATA["ve_guestJuso"]		= $companyRow['ci_juso'];
$DATA["ve_guestJusoSubId"]	= "1";
$DATA["ve_pay"]				= 0;
$DATA["ve_guestLat"]		= $companyRow['ci_lat'];
$DATA["ve_guestLon"]		= $companyRow['ci_lon'];
$DATA["ve_isNew"]			= 0;
$DATA["ve_isShop"]			= 0;
$DATA["ve_isRoad"]			= 'n';
$DATA["ve_createDate"]		= date(("Y-m-d H:i:s"), time());	// 생성날짜

$db->Insert("vehicleGuestOrderData", $DATA, " vehicleGuestOrderData Insert Error ");


//중복된 주소를 업데이트
$db->que = "SELECT count(A.ve_seq) AS dupleCount,A.ve_accno,A.ve_guestJuso,ve_guestLat,ve_guestLon 
					FROM (SELECT * FROM vehicleGuestOrderData 
											WHERE 1=1
												AND ve_deliveryDate ='".$deliveryDate."' 
												AND ve_meridiemType='".$meridiemType."' 
												AND ve_meridiemFlag='".$meridiemFlag."' 
												AND ve_locationId='".$locationId."' 
												AND ve_isJuso = 'Y'  
													GROUP by ve_accno
					) AS A 
						GROUP BY A.ve_guestLat,A.ve_guestLon 
							HAVING count(dupleCount) > 1 
							ORDER BY A.ve_accno;";
$db->query();
$item = $db->getRows();

for($i=0;$i<count($item);$i++){
	unset($DATA);

	$DATA["ve_accnoDupleJuso"] = $item[$i]["ve_accno"];
	$WHERE = "WHERE 1=1
					AND ve_guestLat='".$item[$i]["ve_guestLat"]."' 
					AND ve_guestLon='".$item[$i]["ve_guestLon"]."' 
					AND ve_deliveryDate='".$deliveryDate."' 
					AND ve_locationId='".$locationId. "' 
					AND ve_meridiemType='".$meridiemType."'
					AND ve_meridiemFlag='".$meridiemFlag."'" ;
	$db->Update("vehicleGuestOrderData", $DATA, $WHERE, " vehicleGuestOrderData Insert Error ");
}

echo "ok";
$db->close();
exit;

?>