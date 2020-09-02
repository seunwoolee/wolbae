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
$vehicleNo			= $_POST["vehicleNo"];

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//Json Class
$json = new Json();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -\

//리눅스를 통한 TCP 배차 실행
$output;
$return_var;
//echo $deliveryDate."<br>".$meridiemType."<br> ".$meridiemFlag."<br> ".$locationId." <br>".$vehicleNo;exit;
$exePath = "../tsptest/TSPSIMPLE ".$deliveryDate." ".$meridiemType." ".$meridiemFlag." ".$locationId." ".$vehicleNo." 1";
//echo $exePath;exit;
exec($exePath, $output, $return_var);

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