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


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Json Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$json = new Json();


//$locationId				= $_POST['locationId'];
//$deliveryDate			= $_POST['deliveryDate'];
//$meridiemType			= $_POST['meridiemType'];
//$meridiemFlag			= $_POST['meridiemFlag'];

$ci_guestId = $_SESSION["OMember_id"];

//echo count($_POST['txtdeguestAccno']);
//echo "<Br>";

//for($i=0;$i<count($_POST['locationId']);$i++){
//}

/*
$fff= $_POST['txtdeguestAccno'];
foreach($fff as $value) {
  print "폼 value1 은 ".$value."\n";
}
*/

/*
echo "ci_guestId[".$ci_guestId."]<br>";
echo "deliveryDate[".$deliveryDate."]<br>";
echo "locationId[".$locationId."]<br>";
echo "meridiemType[".$meridiemType."]<br>";
echo "meridiemFlag[".$meridiemFlag."]<br>";

echo "<br>";
*/
//echo count($PARAM['vr_deguestAccno']);
//echo "<br>";

//for($i=0;$i<count($PARAM['vr_deguestAccno']);$i++){
//	echo $PARAM['vr_deguestAccno'][$i];
//}



// 3. insert
for($i=0;$i<count($_POST['txtlocationId']);$i++){


	$db->que = "SELECT MAX(vr_vehicleNoIndex)+1 AS vr_vehicleNoIndex FROM vehicleAllocateResult
					WHERE 1=1
					AND vr_deliveryDate='".$_POST['txtdeliveryDate'][0]."'
					AND vr_meridiemType='".$_POST['txtmeridiemType'][0]."'
					AND vr_meridiemFlag='".$_POST['txtmeridiemFlag'][0]."'
					AND vr_vehicleNo='".$_POST['txtvehicleNo'][$i]."' ";
	$db->query();
	$db->affected_rows();
	$row = $db->getRow();
	//$vr_vehicleNoIndex[$i] = $row['vr_vehicleNoIndex'];


	$DATA["vr_deguestAccno"]	= $_POST['txtdeguestAccno'][$i];
	$DATA["vr_deguestName"]		= $_POST['txtdeguestName'][$i];
	$DATA["vr_deguestTel"]		= $_POST['txtdeguestTel'][$i];
	$DATA["vr_deguestPay"]		= $_POST['txtdeguestPay'][$i];
	$DATA["vr_guestId"]			= "";
	$DATA["vr_deguestId"]		= "";
	$DATA["vr_guestJusoSubId"]	= "1";
	$DATA["vr_deguestJusoSubId"]= "1";
	$DATA["vr_vehicleNo"]		= $_POST['txtvehicleNo'][$i];
	$DATA["vr_vehicleNoIndex"]	= $row['vr_vehicleNoIndex'];

	$DATA["vr_guestLat"]		= "0";
	$DATA["vr_guestLon"]		= "0";	
	$DATA["vr_deguestLat"]		= $_POST['txtdeguestLat'][$i];
	$DATA["vr_deguestLon"]		= $_POST['txtdeguestLon'][$i];
	$DATA["vr_Juso"]			= $_POST['txtJuso'][$i];
	$DATA["vr_distanceValue"]	= "0";
	$DATA["vr_locationId"]		= $_POST['txtlocationId'][$i];

	$DATA["vr_deliveryDate"]	= $_POST['txtdeliveryDate'][$i];
	$DATA["vr_meridiemType"]	= $_POST['txtmeridiemType'][$i];

	$DATA["vr_delivererId"]		= "";
	$DATA["vr_deliveryEndId"]	= "";
	$DATA["vr_deliveryStatus"]	= "";
	$DATA["vr_jsonData"]		= "";

	$DATA["vr_createDate"]		= date(("Y-m-d H:i:s"), time());	// 생성날짜
	//$DATA["vr_updateDate"]	= $vr_updateDate;

	$DATA["vr_guestIsShop"]		= "0";
	$DATA["vr_deguestIsShop"]	= "0";
	$DATA["vr_accnoDupleJuso"]	= "";
	$DATA["vr_meridiemFlag"]	= $_POST['txtmeridiemFlag'][$i];
	$DATA["vr_errorJusoFlag"]	= "Y";

	$db->Insert("vehicleAllocateResult", $DATA, " vehicleAllocateResult Insert Error ");
}

echo $json->getResult();

$db->close();
exit;

?>