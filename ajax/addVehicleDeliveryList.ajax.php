<?
header("Content-Type:text/html;charset=UTF-8");
//###################################################
// addVehicleDeliveryInsert
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
$deliveryDate			= $_GET['deliveryDate'];
$meridiemType			= $_GET['meridiemType'];
$meridiemFlag			= $_GET['meridiemFlag'];
$locationId				= $_GET['locationId'];

$ci_guestId			=	$_SESSION["OMember_id"];
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$db = new Mysql();

//Json Class
$json = new Json();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -


$db->que = "SELECT 
					 A.vr_vehicleNo				AS vehicleNo
					,sum(A.vr_distanceValue)	AS distanceValue
						FROM (SELECT * FROM vehicleAllocateResult 
								WHERE 1=1
									AND vr_deliveryDate='".$deliveryDate."' 
									AND vr_meridiemType='".$meridiemType."' 
									AND vr_meridiemFlag='".$meridiemFlag."' 
									AND vr_locationId='".$locationId."'  
										GROUP BY vr_vehicleNo,vr_vehicleNoIndex) AS A 
							GROUP BY A.vr_vehicleNo 
							ORDER BY A.vr_vehicleNo*1 ASC ";
$db->query();
$vehicleAllocateResultList = $db->getRows();


$json->add("vehicleAllocateResultList", $vehicleAllocateResultList);
$json->result["resultMessage"] = "고객정보데이터";
echo $json->getResult();
$db->close();

exit;

?>
