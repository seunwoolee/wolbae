<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// 배차결과 테이블을 토데로 지도에 출력시킬 데이터를 가져온다.
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
$jsonData = $_POST["jsonData"];    // 배송날짜
$indexs = $_POST["indexs"];    // 배송날짜
$deliveryDate = $_POST["deliveryDate"];    // 배송날짜
$locationId = $_POST["locationId"];        // 거점ID
$meridiemType = $_POST["meridiemType"];    // 오전,오후
$meridiemFlag = $_POST["meridiemFlag"];    // 오전,오후 분할 플래그
$vehicleNo = $_POST["vehicleNo"];
$firstIndexFalg = true;

$WHERE = "
                WHERE 1=1
                AND vr_deguestName IN ('admin', 'guestName') 
                AND vr_deliveryDate='" . $deliveryDate . "' 
                AND vr_meridiemType='" . $meridiemType . "' 
                AND vr_meridiemFlag='" . $meridiemFlag . "' 
                AND vr_locationId='" . $locationId . "'
                AND vr_vehicleNo='" . $vehicleNo . "' 
            ";
$db->que = "DELETE FROM vehicleAllocateResult ".$WHERE;
$db->query();

foreach ($indexs as $index) {
    $WHERE = "
                WHERE 1=1
                AND vr_deguestId ='" . $index['id'] . "' 
                AND vr_deliveryDate='" . $deliveryDate . "' 
                AND vr_meridiemType='" . $meridiemType . "' 
                AND vr_meridiemFlag='" . $meridiemFlag . "' 
                AND vr_locationId='" . $locationId . "'
                AND vr_vehicleNo='" . $vehicleNo . "' 
            ";

    $db->que = " SELECT 
                    vehicleAllocateResult.vr_vehicleNoIndex,
					vehicleAllocateResult.vr_deguestId
					FROM vehicleAllocateResult
                    $WHERE";
    $db->query();

    if ($db->affected_rows() > 0) {

        $DATA["vr_vehicleNoIndex"] = intval($index['index']) - 1;
        $DATA["vr_jsonData"] = $firstIndexFalg ? $jsonData : null;

        $db->Update("vehicleAllocateResult", $DATA, $WHERE, " vehicleAllocateResult UPDATE Error ");
        $firstIndexFalg = false;
    }
}



$json->result["resultMessage"] = "고객정보데이터";
$json->result["code"] = "ok";
echo $json->getResult();
$db->close();

exit;

?>

