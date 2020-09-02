<?
header("Content-Type:text/html;charset=UTF-8");
//###################################################
// 메인화면 그래프
// 2017
//###################################################


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/json.inc.php";
include "../inc/mysql.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$companySeq					= $COMPANY_SEQ;


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db = new Mysql();

//Json Class
$json = new Json();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

/*
$purpose["-oil-"] = "유류비";
$db->que = "SELECT * FROM purpose WHERE companySeq=". $companySeq. " AND purposeState='Y' ORDER BY sort";
$db->query();
while($row = $db->getRow()){
	$purpose[$row["purposeType"]] = $row["purposeName"];
}

$begin = date("Y-m-01", strtotime('-5 months'));
$end = date("Y-m-t");
*/

/*
$db->que = "SELECT SUM(distance) AS distance, purpose, DATE_FORMAT(startDate, '%m') AS month FROM drivingLog ";
$db->que .= " WHERE companySeq=". $companySeq. " AND startDate >= '". $begin. "' AND startDate <= '". $end. "' GROUP BY purpose, month";
*/

/*
$db->que = " SELECT SUM(vr_distanceValue) AS distance FROM vehicleAllocateResult 
					WHERE 1=1
						AND vr_deliveryDate >= '".$toYear."-01-01'
						AND vr_deliveryDate <= '".$toYear."-12-31'
						GROUP BY vr_deliveryDate, vr_meridiemFlag ";
*/

/*
$db->que = " SELECT SUM(vr_distanceValue) AS distance FROM vehicleAllocateResult 
					WHERE 1=1
						AND vr_deliveryDate >= '".$begin."'
						AND vr_deliveryDate <= '".$end."'
						GROUP BY vr_deliveryDate, vr_meridiemFlag ";
*/

// 월별 배차건수 구하긔
$nIndex = 0;
$db->que = " SELECT COUNT(*) AS vehicleCntRoute, DATE_FORMAT(vs_deliveryDate, '%m') AS month FROM vehicleAllocateStatus
					WHERE 1=1
						AND vs_deliveryDate >= '2017-01-01'
						AND vs_deliveryDate <= '2017-12-31'
						GROUP BY DATE_FORMAT(vs_deliveryDate, '%y %m') ";


$db->query();
for($i=0;$row = $db->getRow();$i++){
	$DATA[$nIndex][($row['month']-1)] = $row['vehicleCntRoute'];
}
for($i=0;$i<12;$i++){

	if($DATA[$nIndex][$i] <= 0){
		$DATA[$nIndex][$i] = 0;
	}else{
		$DATA[$nIndex][$i] = 111;
	}
	//echo $DATA[$nIndex][$i]." ";
}

// 월별 배송건수 구하긔
$nIndex++;
$db->que = " SELECT COUNT(*) AS vehicleCntDelivery, DATE_FORMAT(vr_deliveryDate, '%m') AS month FROM vehicleAllocateResult 
					WHERE 1=1
						AND vr_deliveryDate >= '2017-01-01'
						AND vr_deliveryDate <= '2017-12-31'
						AND vr_deguestId != 'admin'
						GROUP BY DATE_FORMAT(vr_deliveryDate, '%y %m')";

$db->query();
for($i=0;$row = $db->getRow();$i++){
	$DATA[$nIndex][($row['month']-1)] = $row['vehicleCntDelivery'];
}
for($i=0;$i<12;$i++){
	if($DATA[$nIndex][$i] <= 0){
		$DATA[$nIndex][$i] = 0;
	}else{
		$DATA[$nIndex][$i] = 222;
	}
}

// 월별 배송거리 구하긔
$nIndex++;
$db->que = " select sum(vr_distanceValue) as vehicleCntDistance, DATE_FORMAT(vr_deliveryDate, '%m') as month from vehicleAllocateResult 
					where 1=1
						and vr_deliveryDate >= '2017-01-01'
						and vr_deliveryDate <= '2017-12-31'
						GROUP BY DATE_FORMAT(vr_deliveryDate, '%y %m')";

$db->query();
for($i=0;$row = $db->getRow();$i++){
	$DATA[$nIndex][($row['month']-1)] = $row['vehicleCntDistance'];
}
for($i=0;$i<12;$i++){
	if($DATA[$nIndex][$i] <= 0){
		$DATA[$nIndex][$i] = 0;
	}else{
		$DATA[$nIndex][$i] = 333;
	}
}

$json->add("vehicleData", $DATA);
$json->result["resultMessage"] = "고객배송중복제거데이터";
echo $json->getResult();
$db->Close();
exit;

?>
