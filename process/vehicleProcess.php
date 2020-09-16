<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// ERP서버 변경된 고객정보 받아오는부분 ( JSON )
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
$deliveryDate		= $_POST["deliveryDate"];	// 배송날짜
$locationId			= $_POST["locationId"];		// 거점ID
$meridiemType		= $_POST["meridiemType"];	// 오전,오후
$meridiemFlag		= $_POST["meridiemFlag"];	// 오전,오후 분할 플래그
$vehicleCount		= $_POST["vehicleCount"];

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//Json Class
$json = new Json();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -\


//1. 우선 현재날짜 기준으로 데이터를 삭제 해야합니다.
$WHERE = "WHERE 1=1
			AND vr_locationId='".$locationId."' 
			AND vr_deliveryDate='".$deliveryDate."' 
			AND vr_meridiemType='".$meridiemType."'
			AND vr_meridiemFlag='".$meridiemFlag."'";

$db->que = "DELETE FROM vehicleAllocateResult ".$WHERE;
$db->query();


//2. 리눅스를 통한 TCP 배차 실행
$output;
$return_var;
$exePath = "../tsptest/TSP_wolbae ".$deliveryDate." ".$meridiemType." ".$meridiemFlag." ".$locationId." ".$vehicleCount;

exec($exePath." > /dev/null 2>/dev/null &", $output, $return_var);


if($return_var=='0'){
	$json->add("code", "Y");
} else {
	$json->add("code", "N");
}

$json->add("message", "완료");

echo $json->getResult();
$db->close();
exit;

?>