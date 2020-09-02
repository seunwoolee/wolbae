<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// ERP서버 변경된 고객배송정보 받아오는부분 ( JSON )
// ERP서버가 준비가 안된상태라, 로컬서버에서 데이터 발생시켰습니다.
// ERP서버가 준비가 완료되면 이페이지는 삭제처리합니다.
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/mysql.inc.php";
include "../inc/user.inc.php";
include "../inc/json.inc.php";
include "../inc/lib.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//Json Class
$json = new Json();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$locationId		= $_GET["locationId"];		// 거점ID
$deliveryDate	= $_GET["deliveryDate"];	// 배송날짜
$meridiemType	= $_GET["meridiemType"];	// 오전,오후 구분
$meridiemFlag	= $_GET["meridiemFlag"];	// 오전,오후 순서

$db->que = " SELECT		vs_locationId			AS locationId
						,vs_deliveryDate		AS deliveryDate
						,vs_meridiemType		AS meridiemType
						,vs_meridiemFlag		AS flag
						,vs_vehicleEndStatus	AS status 
							FROM vehicleAllocateStatus WHERE 1=1
								AND vs_locationId='".$locationId."' 
								AND vs_deliveryDate='".$deliveryDate."' 
								AND vs_meridiemType='".$meridiemType."' 
								AND vs_meridiemFlag='".$meridiemFlag."' ";

$db->query();

$json->add("Erp_vehicleAllocateResultStatus", $db->getRows());

$json->result["resultMessage"] = "배차완료상태데이터";
echo $json->getResult();
$db->close();
exit;

?>