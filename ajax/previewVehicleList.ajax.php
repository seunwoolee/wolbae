<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";

include "../query/vehicleQuery.php";
include "../php/routeColor.php";


/*
$PARAM = json_decode(file_get_contents("php://input"), JSON_UNESCAPED_UNICODE);

$resultOrderListData = $PARAM['resultOrderListData'];

for($i=0;$i<count($resultOrderListData);$i++){

	echo $resultOrderListData['itme'][$i]['accno'];
	echo "<br>";

}

exit;
*/


$deliveryDate		= ( $_POST['deliveryDate'] )	? $_POST['deliveryDate']	: $_GET['deliveryDate'];	// 배송날짜
$meridiemType		= ( $_POST['meridiemType'] )	? $_POST['meridiemType']	: $_GET['meridiemType'];	// 오전, 오후
$meridiemFlag		= ( $_POST['meridiemFlag'] )	? $_POST['meridiemFlag']	: $_GET['meridiemFlag'];	// 오전, 오후 분할 플래그
$locationId			= ( $_POST['locationId'] )		? $_POST['locationId']		: $_GET['locationId'];		// 거점Id

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


if($meridiemType == ""){
	// 0값으로 보냈는데, 값이 사라짐, 0 이외에는 되는데 0일때만 그렇네, 누가 아는사람 수정좀 나중에
	$meridiemType = "0";
}

if($locationId == ""){
	// 0값으로 보냈는데, 값이 사라짐, 0 이외에는 되는데 0일때만 그렇네, 누가 아는사람 수정좀 나중에
	$locationId = "0";
}

$today				= date("Y-m-d");
if($today > $deliveryDate){
	$display		= "style='display:none;'";
}

$vehicleQuery = new VehicleQuery();
$vehicleQuery->init($db);

/*
$companyInfo = $vehicleQuery->getCompanyInfo();

$initLat = $companyInfo['ci_lat'];
$initLon = $companyInfo['ci_lon'];
$initGuestId = $companyInfo['ci_guestId'];
*/

$resultGroupListDataSe = $vehicleQuery->getMapGroupListData($deliveryDate, $meridiemType, $locationId, $meridiemFlag);
$errorVehicleCount = $vehicleQuery->getErrorVehicleCount($deliveryDate, $meridiemType, $locationId, $meridiemFlag);
$vehicleComplete = $vehicleQuery->getVehicleComplete($deliveryDate, $meridiemType, $locationId, $meridiemFlag);
$vectorNoCount=0;
$vectorNoCurrent;

//echo "vehicleComplete[".$vehicleComplete."]<br>";

// 환산금액계산
//echo "[".count($resultGroupListDataSe)."]<br>";
$resultListDataSumPay = $vehicleQuery->getMapGroupListDataSumPay($deliveryDate, $meridiemType, $locationId, count($resultGroupListDataSe), $meridiemFlag);

for($i=0;$i<count($resultGroupListDataSe);$i++){	
	$row = $resultGroupListDataSe[$i];
	
	//if($vectorNoCurrent==null){
	//	$vectorNoCurrent = $row['vehicleNo'];
	//}
	//if($vectorNoCurrent < $row['vehicleNo']){
	//	$vectorNoCurrent = $row['vehicleNo'];
	//	$vectorNoCount++;
	//}

	// json 데이터 유무 채크
	$db->que = " select count(vr_seq) as jsonEmpty from vehicleAllocateResult
							where 1=1 
								and vr_deliveryDate='".$row['deliveryDate']."'
								and vr_meridiemType='".$meridiemType."'
								and vr_meridiemFlag='".$row['meridiemFlag']."'
								and vr_vehicleNo='".$row['vehicleNo']."'
								and vr_jsonData is null";

	$db->query();
	$rowJsonEmpty = $db->getRow();
	
	// 리셋버튼 활성, 비활성채크
	/*
	if($rowJsonEmpty['jsonEmpty'] > 0){
		// 리셋버튼 활성
		$listReset = "<td align='center' style='cursor:pointer' onclick='vehicleRelocation(\"".$row['deliveryDate']."\",\"".$meridiemType."\",\"".$row['meridiemFlag']."\",\"".$row['vehicleNo']."\");'><b><font style='color:red'>리셋</font></b></td>";
	} else {
		// 리셋버튼 비활성
		$listReset = "<td align='center' style='cursor:pointer'>&nbsp;</td>";
	}
	*/

	$listReset = "<td align='center' style='cursor:pointer' onclick='vehicleRelocation(\"".$row['deliveryDate']."\",\"".$meridiemType."\",\"".$row['meridiemFlag']."\",\"".$row['vehicleNo']."\");'><b><font style='color:red'>Reload</font></b></td>";

	$color = $routeColor[$row['vehicleNo']];
	$number = (int)$row['vehicleNo']+1;
	$distance = $row['sum'];
	if($distance > 999){
		$distance = sprintf("%2.1f",$distance*0.001)."km";
	} else{
		$distance = $distance."m";
	}

	if($resultListDataSumPay[$i]['nAccnoDupleCnt'] > 0){
		$strDupleFlg = " <font style='color:red'>(+".$resultListDataSumPay[$i]['nAccnoDupleCnt'].")</font>";
	} else {
		$strDupleFlg = "";
	}

	$LIST .= "<tr height='30'>
				<td align='center'>
					<input type='checkbox' id='map".$vectorNoCount."' name='map[".$vectorNoCount."]' class='setCarItem' style='margin:0;' 
					onchange='checkListData(".$vectorNoCount.");' vertical-align:'middle' /> <b>".$number." 경로</b> <font style='color:".$color."'>■</font></td>
				<!-- <td align='center' style='cursor:pointer' onclick='detail(".$row['vehicleNo'].",".$row['meridiemFlag'].");'></td> -->
				<td align='center' style='cursor:pointer' onclick='detail(".$row['vehicleNo'].",".$row['meridiemFlag'].");'>".$distance."</td>
				<td align='center' style='cursor:pointer' onclick='detail(".$row['vehicleNo'].",".$row['meridiemFlag'].");'>".((int)$row['count']-1).$strDupleFlg."</td>
				<!-- <td align='center' style='cursor:pointer' onclick='vehicleRelocation(\"".$row['deliveryDate']."\",\"".$meridiemType."\",\"".$row['meridiemFlag']."\",\"".$row['vehicleNo']."\");'>리셋</td> -->
				".$listReset."
				<td align='center' style='cursor:pointer' onclick='detail(".$row['vehicleNo'].",".$row['meridiemFlag'].");'><b>".number_format($resultListDataSumPay[$i]['nSumPay'])." 원</b></td>
			 </tr>";
				//<td align='center' style='cursor:pointer' onclick='detail(".$row['vehicleNo'].");'><b>".number_format($row['deguestPay'])." 원</b></td>
	$vectorNoCount++;
}

$db->close();
//echo "vehicleComplete[".$vehicleComplete."]<br>";
?>

	<div class="card">
		<div class="cardTitle">
			<span class="titCard">
				
				<!-- 2017년 9월 25일 오전 배송 차량 목록 -->
			</span>
		</div>
		<div class="cardSearch text-center" <?//=$display?>>
			<!-- <a href="javascript:reset()" class="btn btn-sm btn-new-cancle" style="width:300px;" <?=$display?>>초기 재설정</a> -->
			<!-- <a href="javascript:reset()" class="btn btn-sm btn-new-cancle" style="width:200px;" <?=$display?>>초기 재설정</a> -->
			<!-- <a href="javascript:displayRouteRemove()" class="btn btn-sm btn-new-cancle" style="width:100px;" <?//=$display?>>선삭제</a> -->
			<!--<input type="button" value="임시 배차 하기" class="btn btn-sm btn-new-find" />-->
		</div>
		<div class="cardCont setCarList" style="padding:10px; height:768px">
			<div><!-- 총 배송 차량 대수 : <strong><?=count($resultGroupListDataSe)?>대</strong> --></div>
			<!-- <input type="checkbox" name="check-line" id="check-line" value="Y" onchange="lineCheck();" style="margin:0; vertical-align:middle" />지도 경로선 제거 -->
			<table class="tblBasic table-hover mt10">
				<colgroup>
					<col width="90">
					<col width="60">
					<col width="60">
					<col width="60">
					<col width="">
				</colgroup>
				<thead>
				<tr height="30">
					<th align="center" class="text-center"><!-- <input type="checkbox" name="check-all" id="check-all" value="Y" onchange="allCheck();" style="margin:0; vertical-align:middle" />  전체경로--></th>
					<!-- <th align="center" class="text-center">색상</th> -->
					<!-- <th align="center" class="text-center">경로</th> -->

					<th align="center" class="text-center">data1</th>
					<th align="center" class="text-center">data2</th>
					<th align="center" class="text-center">data3</th>
					<th align="center" class="text-center">data4</th>
				</tr>
				</thead>
				<tbody>
				<?=$LIST?>
				</tbody>
			</table>
		</div>
	</div>

