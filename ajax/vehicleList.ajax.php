<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";

include "../query/vehicleQuery.php";
include "../php/routeColor.php";

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

$resultGroupListDataSe	= $vehicleQuery->getMapGroupListData($deliveryDate, $meridiemType, $locationId, $meridiemFlag);
$errorVehicleCount		= $vehicleQuery->getErrorVehicleCount($deliveryDate, $meridiemType, $locationId, $meridiemFlag);
$vehicleComplete		= $vehicleQuery->getVehicleComplete($deliveryDate, $meridiemType, $locationId, $meridiemFlag);
$vectorNoCount=0;
$vectorNoCurrent;

//echo "vehicleComplete[".$vehicleComplete."]<br>";

// 환산금액계산
//echo "[".count($resultGroupListDataSe)."]<br>";
$resultListDataSumPay = $vehicleQuery->getMapGroupListDataSumPay($deliveryDate, $meridiemType, $locationId, count($resultGroupListDataSe), $meridiemFlag);


for($i=0;$i<count($resultGroupListDataSe);$i++){	
	$row = $resultGroupListDataSe[$i];
    LIB::PLog($row);

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
								and vr_deliveryDate	= '".$row['deliveryDate']."'
								and vr_meridiemType	= '".$meridiemType."'
								and vr_meridiemFlag	= '".$row['meridiemFlag']."'
								and vr_vehicleNo	= '".$row['vehicleNo']."'
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

	$listReset = "<button class='btn btn-xs btn-new-find pull-right' onclick='vehicleRelocation(\"".$row['deliveryDate']."\",\"".$meridiemType."\",\"".$row['meridiemFlag']."\",\"".$row['vehicleNo']."\");' style='position:absolute; top:10px; left:64%'>".($row['vehicleNo']+1)."번경로 다시그리기</button>";

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
	$LIST2 .= '<div class="listItem checkItem">'.$listReset.'<span class="driveCheck">
					<label class="driveLabel" style="background:'.$color.'">
						<input type="checkbox" id="map'.$vectorNoCount.'" name="map['.$vectorNoCount.']" class="setCarItem" style="margin:0;" onchange="checkListData('.$vectorNoCount.');" vertical-align:"middle" checked="true" /> 선택
					</label></span>
					<a href="javascript:;" onclick="detail('.$row['vehicleNo'].','.$row['meridiemFlag'].');">
						<div class="driveIndex"><img src="images/icon/marker_'.$row['vehicleNo'].'.png" width="12" /><strong style="margin-left:5px; color:'.$color.'">'.$number.'경로</strong>
						</div>
						<div class="trip">
							<div class="tripDetails">
								<div class="middleBox">
									<div class="distance stat">'.$distance.'</div>
									<div class="duration stat">'.((int)$row['count']-1).$strDupleFlg.'건</div>
									<div class="totalPrice stat">'.number_format($resultListDataSumPay[$i]['nSumPay']).' 원</div>
								</div>
							</div>
						</div>
					</a>
				</div>';
	
	/*
	$LIST .= "<tr height='30'>
				<td align='center'>
					<input type='checkbox' id='map".$vectorNoCount."' name='map[".$vectorNoCount."]' class='setCarItem' style='margin:0;' onchange='checkListData(".$vectorNoCount.");' vertical-align:'middle' /> <b>".$number." 경로</b> <font style='color:".$color."'>■</font>
				</td>
				<!-- <td align='center' style='cursor:pointer' onclick='detail(".$row['vehicleNo'].",".$row['meridiemFlag'].");'></td> -->
				<td align='center' style='cursor:pointer' onclick='detail(".$row['vehicleNo'].",".$row['meridiemFlag'].");'>".$distance."</td>
				<td align='center' style='cursor:pointer' onclick='detail(".$row['vehicleNo'].",".$row['meridiemFlag'].");'>".((int)$row['count']-1).$strDupleFlg."</td>
				<!-- <td align='center' style='cursor:pointer' onclick='vehicleRelocation(\"".$row['deliveryDate']."\",\"".$meridiemType."\",\"".$row['meridiemFlag']."\",\"".$row['vehicleNo']."\");'>리셋</td> -->
				".$listReset."
				<td align='center' style='cursor:pointer' onclick='detail(".$row['vehicleNo'].",".$row['meridiemFlag'].");'><b>".number_format($resultListDataSumPay[$i]['nSumPay'])." 원</b></td>
			 </tr>";
	*/
				//<td align='center' style='cursor:pointer' onclick='detail(".$row['vehicleNo'].");'><b>".number_format($row['deguestPay'])." 원</b></td>
	$vectorNoCount++;
}

$db->close();
//echo "vehicleComplete[".$vehicleComplete."]<br>";
?>

	<div class="card">
		<div class="cardTitle">
			
			<span class="titCard">
				<script>
				
				// 경로선 표시,제거
				$(".chgLineMap").on("click", function(){
					if($(this).hasClass("btn-new-ok")){
						bCheckBoxLineRemove = true;
						$(this).removeClass("btn-new-ok").addClass("btn-new-cancle").html("경로선 표시");
						lineCheck('Y');
					}else{
						bCheckBoxLineRemove = false;
						$(this).removeClass("btn-new-cancle").addClass("btn-new-ok").html("경로선 제거");
						lineCheck('');
					}
					//lineCheck2();
				});
				</script>
				총 배송 차량 대수 : <strong><?=count($resultGroupListDataSe)?>대</strong>
				<!-- <a href="javascript:;" class="btn btn-sm btn-new-ok pull-right chgLineMap" style="margin-top:-5px;">경로선 제거</a> -->
			<!-- <label><input type="checkbox" name="check-line" id="check-line" value="Y" onchange="lineCheck();" style="margin:0; vertical-align:middle" />지도 경로선 제거</label> -->
			</span>
		</div>
		<div class="cardSearch" <?//=$display?>>
			<!-- <a href="javascript:reset()" class="btn btn-sm btn-new-cancle" style="width:300px;" <?=$display?>>초기 재설정</a> -->
			<!-- <a href="javascript:reset()" class="btn btn-sm btn-new-cancle" style="width:200px;" <?=$display?>>초기 재설정</a> -->
			<!-- <a href="javascript:displayRouteRemove()" class="btn btn-sm btn-new-cancle" style="width:100px;" <?//=$display?>>선삭제</a> -->
			<!--<input type="button" value="임시 배차 하기" class="btn btn-sm btn-new-find" />-->
			
			<a href="javascript:;" class="btn btn-sm btn-new-cancle chgLine" style="margin-top:-5px;">전체선택 해제</a>
			<a href="javascript:;" class="btn btn-sm btn-new-cancle pull-right chgLineMap" style="margin-top:-5px;">경로선 제거</a>
			<script>
			$(".chgLine").on("click", function(){
				if($(this).hasClass("btn-new-ok")){
					bCheckBoxAll = true;
					$(this).removeClass("btn-new-ok").addClass("btn-new-cancle").html("전체선택 해제");
					$(".listItem").each(function(){
						$(this).addClass("checkItem");
						$(this).find("input[type='checkbox']").prop("checked", true);
					});
				}else{
					bCheckBoxAll = false;
					$(this).removeClass("btn-new-cancle").addClass("btn-new-ok").html("전체선택");
					$(".listItem").each(function(){
						$(this).removeClass("checkItem");
						$(this).find("input[type='checkbox']").prop("checked", false);
					});
				};
				//lineCheck2();
				allCheck();
			});
			</script>
		<label style=" display:none;"><input type="checkbox" name="check-all" id="check-all" value="Y" onchange="allCheck();" style="margin:0; vertical-align:middle;" /> 전체 경로 표시</label>
		</div>
		<div class="setCarList" style="max-height:570px;">
			<?=$LIST2?>
		</div>

		<div class="cardFoot text-center" id="btnComplete" <?=$display?>>

			<? if($vehicleComplete=='Y'){ ?>
				<button class="btn btn-lg btn-new-find" style="width:100%;" onclick="javascript:alert('이미 배차가 완료되었습니다');"> 배차 완료
			<? }else{ ?>
				<button class="btn btn-lg btn-new-danger" onclick="getVehicleError('<?php echo $errorVehicleCount; ?>', '<?php echo $meridiemFlag; ?>');">
					배송 경로 오류 <span class="badge" style="margin-left:8px; background:#fff; color:#444;"><?=$errorVehicleCount?></span></button>
				<button class="btn btn-lg btn-new-danger" onclick="setVehicleComplete();">배차 완료 확인 </button>
			<? } ?>
			<!-- <button class="btn btn-lg btn-new-danger" style="width:100%;">배차 완료</button> -->
			<!-- 2017-09-21 박용태
			<a href="javascript:;" class="btn btn-new-cancle pull-right"><i class="fa fa-file-excel-o mr5"></i>배차 엑셀 출력</a>
			-->
		</div>
		<? if($vehicleComplete != 'Y'){ ?>
		<div class="cardFoot text-center" id="btnCompleteDeliveryAdd" <?=$display?>>
			<button class="btn btn-lg btn-new-biz" style="width:100%;" onclick="vehicleAdd();">배송경로 생성하기</button>
		</div>
		<? } ?>
		<script>

		$(window).load(function(){

			$(".setCarItem").each(function(){
				if($(this).is(":checked")){
					$(this).parents(".listItem").addClass("checkItem");
				}else{
					$(this).parents(".listItem").removeClass("checkItem");
				}
			});
		})
		$(document).on("change",".setCarItem",function(){
			if($(this).is(":checked")){
				$(this).parents(".listItem").addClass("checkItem");
			}else{
				$(this).parents(".listItem").removeClass("checkItem");
			}
		});

		</script>
	</div>

