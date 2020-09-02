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

/*
if($locationId == ""){
	$locationId = "0";
}
if(!$deliveryDate == ""){
	$deliveryDate = "2017-12-13";
}
if(!$meridiemType == ""){
	$meridiemType = "1";
}
if(!$meridiemFlag == ""){
	$meridiemFlag = "1";
}
*/

$db->que = " set @num:=0";
$db->query();

$db->que = " SELECT  @num:=@num+1			AS no
					,vr_deguestAccno		AS accno
					,vr_deguestId			AS guestId
					,vr_vehicleNo			AS vehicleNo
					,vr_locationId			AS locationId
					,vr_deliveryDate		AS deliveryDate
					,vr_meridiemType		AS meridiemType
					,vr_meridiemFlag		AS flag
					,(vr_vehicleNoIndex+1)	AS num
					,vr_distanceValue		AS dist
					,vr_accnoDupleJuso		AS accnoDupleJuso
					,vr_errorJusoFlag		AS errorJusoFlag

						FROM vehicleAllocateResult WHERE 1=1
							AND vr_deguestId != 'admin'
							AND vr_locationId='".$locationId."' 
							AND vr_deliveryDate='".$deliveryDate."' 
							AND vr_meridiemType='".$meridiemType."' 
							AND vr_meridiemFlag='".$meridiemFlag."' 
								ORDER BY vr_vehicleNo ASC, vr_vehicleNoIndex ASC ";

$db->query();
$vehicleAllocateResultList = $db->getRows();

$accnoDupleJuso		= '';
$accnoDupleJusoCopy	= '';
$deguestId			= '';
$deguestIdCopy		= '';

for($i=0;$i<count($vehicleAllocateResultList);$i++){

	$strDupleJusoFlg	= '';
	$strErrorJusoFlg	= '';


	$DATA[$i]['no']				= $vehicleAllocateResultList[$i]['no'];
	$DATA[$i]['accno']			= $vehicleAllocateResultList[$i]['accno'];
	$DATA[$i]['guestId']		= $vehicleAllocateResultList[$i]['guestId'];
	$DATA[$i]['vehicleNo']		= ($vehicleAllocateResultList[$i]['vehicleNo']+1);
	$DATA[$i]['locationId']		= $vehicleAllocateResultList[$i]['locationId'];
	$DATA[$i]['deliveryDate']	= $vehicleAllocateResultList[$i]['deliveryDate'];
	$DATA[$i]['meridiemType']	= $vehicleAllocateResultList[$i]['meridiemType'];
	$DATA[$i]['flag']			= $vehicleAllocateResultList[$i]['flag'];
	
	$accnoDupleJuso				= $vehicleAllocateResultList[$i]['accnoDupleJuso'];
	$deguestId					= $vehicleAllocateResultList[$i]['guestId'];

	
	if($accnoDupleJuso != '' && ($accnoDupleJusoCopy == $accnoDupleJuso) && ($deguestIdCopy != $deguestId)){
		//$strDupleJusoFlg = "(중첩지역)";
	} else {
		$accnoDupleJusoCopy	= $accnoDupleJuso;
		$deguestIdCopy		= $deguestId;
	}

	// 배송경로추가건
	if($vehicleAllocateResultList[$i]['errorJusoFlag'] == 'Y'){
		//$DATA[$i]['nums']		=	($vehicleAllocateResultList[$i]['num']-1)."(배송경로추가건)";
		$DATA[$i]['nums']		=	($vehicleAllocateResultList[$i]['num']-1);
	} else {
		$DATA[$i]['nums']		=	$vehicleAllocateResultList[$i]['num'].$strDupleJusoFlg;
	}
	$DATA[$i]['dist']			=	$vehicleAllocateResultList[$i]['dist'];

}

$json->add("Erp_vehicleAllocateResult", $DATA);
$json->result["resultMessage"] = "배차완료데이터";

echo $json->getResult();
$db->close();
exit;

?>