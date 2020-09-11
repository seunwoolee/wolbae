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
$deliveryDate = $_POST["deliveryDate"];    // 배송날짜
$locationId = $_POST["locationId"];        // 거점ID
$meridiemType = $_POST["meridiemType"];    // 오전,오후
$meridiemFlag = $_POST["meridiemFlag"];    // 오전,오후 분할 플래그
$vehicleNo = $_POST["vehicleNo"];

//$db->query();

$db->que = " SELECT DISTINCT(vehicleAllocateResult.vr_deguestId) AS viaPointId,
					vr_deguestName		AS viaPointName,
					vehicleAllocateResult.vr_deguestLon AS viaX, 
					vehicleAllocateResult.vr_deguestLat AS viaY
					FROM vehicleAllocateResult
                    WHERE 1=1
                    AND vr_deliveryDate='" . $deliveryDate . "' 
                    AND vr_meridiemType='" . $meridiemType . "' 
                    AND vr_meridiemFlag='" . $meridiemFlag . "' 
                    AND vr_locationId='" . $locationId . "'
                    AND vr_vehicleNo='" . $vehicleNo . "'
                    AND vr_deguestName NOT IN ('guestName', 'admin')
                        GROUP BY vr_vehicleNo, vr_vehicleNoIndex 
                        ORDER BY vr_vehicleNo*1 ASC, vr_vehicleNoIndex*1 ASC";
$db->query();
$viaPoints = $db->getRows();

$json->add("viaPoints", $viaPoints);
$json->result["resultMessage"] = "고객정보데이터";
echo $json->getResult();
$db->close();

exit;

?>