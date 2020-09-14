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


$db->que = " SELECT DISTINCT(o.ve_guestId), o.ve_guestName, r.vr_deguestId, r.vr_deguestName
					FROM vehicleGuestOrderData AS o
					LEFT JOIN  vehicleAllocateResult AS r ON vr_deguestAccno = o.ve_accno
                    WHERE 1=1
                    AND ve_deliveryDate='" . $deliveryDate . "' 
                    AND ve_meridiemType='" . $meridiemType . "' 
                    AND ve_locationId='" . $locationId . "'
                    AND ve_guestName NOT IN ('guestName', 'admin')
                    AND r.vr_deguestId IS null";
$db->query();
$missingOrders = $db->getRows();

$db->que = " SELECT MAX(vr_vehicleNo) +1 AS maxRouteNumber
					FROM vehicleAllocateResult
                    WHERE 1=1
                    AND vr_deliveryDate='" . $deliveryDate . "' 
                    AND vr_meridiemType='" . $meridiemType . "' 
                    AND vr_locationId='" . $locationId . "'";
$db->query();
$maxRouteNumber = $db->getOne();


$json->add("missingOrders", $missingOrders);
$json->add("maxRouteNumber", $maxRouteNumber);
echo $json->getResult();
$db->close();

exit;

?>