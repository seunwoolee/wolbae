<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Step01
// 고객 업데이트정보 API
// ERP Server <===== local Server (call)
// ERP Server로부터 고객정보가 변경된 데이터만 가져와서 거점서버에 업데이트합니다.
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

include "../inc/config.php";
include "../inc/mysql.inc.php";
include "../inc/user.inc.php";
include "../inc/json.inc.php";
include "../inc/lib.inc.php";
include "../query/vehicleQuery.php";

//error_reporting(E_ALL);
//ini_set("display_errors", "1"); 


$db = new Mysql();
$vehicleQuery = new VehicleQuery();
$vehicleQuery->init($db);
$companyInfo = $vehicleQuery->getCompanyInfo();
$locationId = $companyInfo['ci_no'];


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

// ERP서버 적용시 PARAM 값이 맞지 않을수 있으니, ERP서버측에서 넘어오는 데이터 확인 필요!!!
$PARAM = json_decode(file_get_contents("php://input"), JSON_UNESCAPED_UNICODE);


$jum = $_GET['jum'];

$gbn = $_GET['gbn'];


if($jum == ""){
	exit;
}


// $gbn 값이 없으면, 기본값 1로
if($gbn == ""){
	$gbn = "1"; // $gbn 1:오전, 2:오후
	$meridiemType = "0";
} else if($gbn == "a"){
	$gbn = "1";
	$meridiemType = "0";
} else {
	$gbn = "2";
	$meridiemType = "1";
}

$conn = mssql_connect('egServer70', _MSDBID, _MSDBPASS) or DIE("DB Connect Fail");
mssql_select_db(_MSDBNAME, $conn);

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$json = new Json();


$stmt = mssql_init("Iregen_OrderSheet_Ready_List_Vehicle", $conn);


$CRUN		= "MONITER";
$CorpCode	= "10001";//1001
$StartDDay	= date("Y-m-d");
$EndDDay	= date("Y-m-d");
$pStCode	= $jum;
$StCode		= $jum;	//"0006"
$IsMorning	= $gbn;

mssql_bind($stmt, "@CRUD",		$CRUD,			SQLVARCHAR);
mssql_bind($stmt, "@CorpCode",	$CorpCode,		SQLVARCHAR);
mssql_bind($stmt, "@StartDDay",	$StartDDay,		SQLVARCHAR);
mssql_bind($stmt, "@EndDDay",	$EndDDay,		SQLVARCHAR);
mssql_bind($stmt, "@pStCode",	$pStCode,		SQLVARCHAR);
mssql_bind($stmt, "@StCode",	$StCode,		SQLVARCHAR);
mssql_bind($stmt, "@IsMorning",	$IsMorning,		SQLVARCHAR);

$result = mssql_execute($stmt);
$nIndex = 0;

$vehicleGuestOrderDataList = "";

do{
	while($row = mssql_fetch_array($result)){

		if(isset($row['JoinLocation'])){
			//echo $row['JoinLocation']."<br>";
		}

		if(isset($row['SaCode'])){
			//echo $row['SaCode']."<br>";
			$vehicleGuestOrderDataList[$nIndex]['no']			= ($nIndex+1);
			$vehicleGuestOrderDataList[$nIndex]['accno']		= $row['SaCode'];
			//$vehicleGuestOrderDataList[$nIndex]['locationId']	= "6";
			$vehicleGuestOrderDataList[$nIndex]['locationId']	= $locationId;
			$vehicleGuestOrderDataList[$nIndex]['deliveryDate']	= $row['DevDDay'];
			$vehicleGuestOrderDataList[$nIndex]['meridiemType']	= $meridiemType;
			$vehicleGuestOrderDataList[$nIndex]['guestId']		= $row['CtCode'];
			$vehicleGuestOrderDataList[$nIndex]['name']			= $row['JoinUserName'];		//$row['OrderName']
			$vehicleGuestOrderDataList[$nIndex]['guestTel']		= "";						//$row['OrderMobile']
			$vehicleGuestOrderDataList[$nIndex]['jusoSubid']	= "1";
			$vehicleGuestOrderDataList[$nIndex]['juso']			= $row['Address1'];
			$vehicleGuestOrderDataList[$nIndex]['pay']			= $row['TotalSalePrice'];
			$vehicleGuestOrderDataList[$nIndex]['lat']			= $row['DevY'];
			$vehicleGuestOrderDataList[$nIndex]['lon']			= $row['DevX'];
			$vehicleGuestOrderDataList[$nIndex]['isNew']		= "1";
			$vehicleGuestOrderDataList[$nIndex]['isShop']		= "0";
			$vehicleGuestOrderDataList[$nIndex]['flag']			= "1";
			$vehicleGuestOrderDataList[$nIndex]['isRoad']		= "n";

			$nIndex++;
		}

	}

} while(mssql_next_result($result));


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$json->add("item", $vehicleGuestOrderDataList);
$json->result["resultMessage"] = "result";
echo $json->getResult();
//$ms->ms_close();
mssql_close($conn);
exit;

?>