<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// 배송경로 새로 생성
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/mysql.inc.php";
include "../inc/user.inc.php";
include "../inc/json.inc.php";
include "../inc/lib.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// ERP서버 적용시 PARAM 값이 맞지 않을수 있으니, ERP서버측에서 넘어오는 데이터 확인 필요!!!
$PARAM = json_decode(file_get_contents("php://input"), JSON_UNESCAPED_UNICODE);


$locationId				= $_GET['locationId'];
$deliveryDate			= $_GET['deliveryDate'];
$meridiemType			= $_GET['meridiemType'];
$meridiemFlag			= $_GET['meridiemFlag'];

$ci_guestId = $_SESSION["OMember_id"];

//echo "ci_guestId[".$ci_guestId."]<br>";
//echo "deliveryDate[".$deliveryDate."]<br>";
//echo "locationId[".$locationId."]<br>";
//echo "meridiemType[".$meridiemType."]<br>";
//echo "meridiemFlag[".$meridiemFlag."]<br>";

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Json Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$json = new Json();

// 1. 배송매장 admin 정보, 좌표 정보 가져오긔
$db->que = "SELECT * FROM companyInfo ";
$db->query();
$db->affected_rows();
$row = $db->getRow();

$vr_deguestAccno	= $row["ci_no"];
$vr_guestId			= $row["ci_guestId"];
$vr_deguestId		= $row["ci_guestId"];

$vr_guestLat		= $row["ci_lat"];
$vr_guestLon		= $row["ci_lon"];

$vr_deguestLat		= $row["ci_lat"];
$vr_deguestLon		= $row["ci_lon"];

$vr_locationId		= $locationId;
$vr_deliveryDate	= $deliveryDate;
$vr_meridiemType	= $meridiemType;

$vr_delivererId		= "";
$vr_deliveryEndId	= "";
$vr_deliveryStatus	= "";
$vr_deliveryEndDate	= "";
$vr_jsonData		= "";
//$vr_createDate		= 
$vr_updateDate		= "";
$vr_guestIsShop		= "0";
$vr_deguestIsShop	= "0";
$vr_accnoDupleJuso	= "";
$vr_meridiemFlag	= "1";
$vr_errorJusoFlag	= "N";



//echo $row["ci_no"]."<br>";
//echo $row["ci_guestId"]."<bR>";
//echo $row["ci_lat"]."<br>";
//echo $row["ci_lon"]."<bR>";


// 2. 배송경로 MAX Count 가져오긔
$db->que = "SELECT MAX(vr_vehicleNo)+1 AS vr_vehicleNo FROM vehicleAllocateResult
				WHERE 1=1
				AND vr_deliveryDate='".$deliveryDate."'
				AND vr_meridiemType='".$meridiemType."'
				AND vr_meridiemFlag='".$meridiemFlag."'
				";
$db->query();
$db->affected_rows();
$row = $db->getRow();

$vr_vehicleNo = $row["vr_vehicleNo"];

//echo $vr_vehicleNo."<Br>";
//echo "22222<br>";


// 3. insert
for($i=0;$i<=1;$i++){
	$DATA["vr_deguestAccno"]	= $vr_deguestAccno;
	$DATA["vr_deguestName"]		= "guestName";
	$DATA["vr_deguestTel"]		= "";
	$DATA["vr_deguestPay"]		= "0";
	$DATA["vr_guestId"]			= $vr_guestId;
	$DATA["vr_deguestId"]		= $vr_deguestId;
	$DATA["vr_guestJusoSubId"]	= "1";
	$DATA["vr_deguestJusoSubId"]= "1";
	$DATA["vr_vehicleNo"]		= $vr_vehicleNo;
	$DATA["vr_vehicleNoIndex"]	= $i;

	$DATA["vr_guestLat"]		= $vr_guestLat;
	$DATA["vr_guestLon"]		= $vr_guestLon;	
	$DATA["vr_deguestLat"]		= $vr_deguestLat;
	$DATA["vr_deguestLon"]		= $vr_deguestLon;
	$DATA["vr_Juso"]			= "guestJuso";
	$DATA["vr_distanceValue"]	= "0";
	$DATA["vr_locationId"]		= $vr_locationId;

	$DATA["vr_deliveryDate"]	= $vr_deliveryDate;
	$DATA["vr_meridiemType"]	= $vr_meridiemType;

	$DATA["vr_delivererId"]		= $vr_delivererId;
	$DATA["vr_deliveryEndId"]	= $vr_deliveryEndId;
	$DATA["vr_deliveryStatus"]	= $vr_deliveryStatus;
	$DATA["vr_jsonData"]		= $vr_jsonData;

	$DATA["vr_createDate"]		= date(("Y-m-d H:i:s"), time());	// 생성날짜
	//$DATA["vr_updateDate"]		= $vr_updateDate;

	$DATA["vr_guestIsShop"]		= $vr_guestIsShop;
	$DATA["vr_deguestIsShop"]	= $vr_deguestIsShop;
	$DATA["vr_accnoDupleJuso"]	= $vr_accnoDupleJuso;
	$DATA["vr_meridiemFlag"]	= $vr_meridiemFlag;
	$DATA["vr_errorJusoFlag"]	= $vr_errorJusoFlag;

	$db->Insert("vehicleAllocateResult", $DATA, " vehicleAllocateResult Insert Error ");
}

echo $json->getResult();

$db->close();
exit;

?>