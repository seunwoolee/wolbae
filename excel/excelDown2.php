<?
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";

$toDay = date("Ymd-His");

header( "Content-type: application/vnd.ms-excel" );   
header( "Content-type: application/vnd.ms-excel; charset=utf-8");    
header( "Content-Description: PHP4 Generated Data" );   
 
$deliveryDate				= $_GET["deliveryDate"];
$locationId					= $_GET["locationId"];
$meridiemType				= $_GET["meridiemType"];


header( "Content-Disposition: attachment; filename = vehicle_".$toDay.".xls" ); 
?>
<style>
th,td{width:auto;text-align:center;font-size:15px;}
</style>
<?

$db = new Mysql();
$db->que = "SELECT	vr_vehicleNo			AS	vehicleNo
					,vr_vehicleNoIndex		AS	vehicleNoIndex 
					,vr_guestLat			AS	guestLat 
					,vr_guestLon			AS	guestLon 
					,vr_deguestLat			AS	deguestLat 
					,vr_deguestLon			AS	deguestLon 
					,vr_Juso				AS	Juso 
					,vr_distanceValue		AS	distanceValue 
					,vr_deliveryDate		AS	deliveryDate 
					,vr_meridiemType		AS	meridiemType 
					,vr_delivererId			AS	deliveryId 
					,vr_deliveryStatus		AS	deliveryStatus
					,vr_deliveryEndDate		AS	deliveryEndDate
					FROM vehicleAllocateResult 
					WHERE	vr_deliveryDate='".$deliveryDate."' AND 
							vr_locationId='".$locationId."' AND 
							vr_meridiemType='".$meridiemType."' 
					ORDER BY vr_vehicleNo ASC, vr_vehicleNoIndex ASC";
$db->query();  

$num = 1;
// 테이블 상단 만들기  
$EXCEL_STR = "  
<table border='1'>  
<tr bgcolor='#F2F2F2'>  
   <td>번호</td>  
   <td>배차번호</td> 
   <td>배차순서</td>
   <td>시작LAT</td> 
   <td>시작LON</td>
   <td>도착LAT</td>
   <td>도착LON</td>
   <td>도착주소</td>
   <td>거리</td>
   <td>배송날짜</td>
   <td>오전/오후</td>
   <td>배송기사님</td>
   <td>배송완료</td>
   <td>배송완료날짜</td>
   <td>추가배송</td>
</tr>";  
  
while($row = $db->getRow()) {
	$meridiemType ="오전";
	$deliveryEndDate = $row["deliveryEndDate"];
	$addDelivery = "";
	if($row["meridiemType"]=='1'){
		$meridiemType = "오후";
	}

	if($deliveryEndDate=='1900-01-01 00:00:00'){
		$deliveryEndDate = "";
	}
	
	if($row["guestLat"]==0 && $row["guestLon"]==0){
		$addDelivery = "추가배송";
	}

	$EXCEL_STR .= "  
	<tr>  
		<td>".$num."</td>  
		<td>".$row["vehicleNo"]."</td>  
		<td>".$row["vehicleNoIndex"]."</td>  
		<td>".$row["guestLat"]."</td> 
		<td>".$row["guestLon"]."</td> 
		<td>".$row["deguestLat"]."</td> 
		<td>".$row["deguestLon"]."</td> 
		<td>".$row["Juso"]."</td> 
		<td>".$row["distanceValue"]."m</td> 
		<td>".$row["deliveryDate"]."</td> 
		<td>".$meridiemType."</td> 
		<td>".$row["deliveryId"]."</td> 
		<td>".$row["deliveryState"]."</td> 
		<td>".$deliveryEndDate."</td> 
		<td>".$addDelivery."</td> 
   </tr>  
   ";  
	$num++;
}  
  
$EXCEL_STR .= "</table>";  
  
echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> ";  
echo $EXCEL_STR; 


 
?>  