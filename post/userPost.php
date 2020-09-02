<?
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";
include "../inc/user.inc.php";
include "../inc/payment.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$companySeq			= $COMPANY_SEQ;
$mode				= $_POST["mode"];
$seq				= $_POST["seq"];
$orgUid				= $_POST["orgUid"];
$password			= $_POST["p_assword"];
$departmentSeq		= $_POST["departmentSeq"];
$dutySeq			= $_POST["dutySeq"];
$name				= $_POST["name"];
$carModel			= $_POST["carModel"];
$carNumber			= $_POST["carNumber"];
$totalDistance		= $_POST["totalDistance"];
$enabled			= $_POST["enabled"];




//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();
$user = new User($db);
$payment = new Payment($db, $companySeq);

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db->que = "SELECT * FROM company WHERE seq=". $companySeq;
$db->query();
$company = $db->getRow();
$uid = $company["ikey"]. "_". $orgUid;


if($mode == "remove")
{
	$completeMessage = "사용자 정보가 삭제 되었습니다.";
	$changeUid = "*remove_". $company["ikey"]. "_". uniqid();
	$DATA["uid"] = $changeUid;
	$DATA["enabled"] = "X";
	$db->Update("user", $DATA, "where seq=". $seq, "user delete error");
}
else
{
	if($payment->getState() != Payment::$stateComplete)
	{
		$db->close();
		LIB::Alert("사용료가 결제되지 않아 이용하실 수 없습니다. 결제 후 이용해 주세요.", "-1");
		exit;
	}
	else
	{
		$userInfo = $user->getUser($uid);
		if($enabled == "Y" && $userInfo["enabled"] != "Y")
		{
			$db->que = "SELECT COUNT(*) FROM user WHERE companySeq=". $companySeq. " AND enabled='Y'";
			$db->query();
			$activeUserCount = $db->getOne();
			$licenceQuantity = $payment->getLicenceQuantity();

			if($activeUserCount >= $licenceQuantity)
			{
				$db->close();
				LIB::Alert("활성 사용자수 ". $licenceQuantity. "명 으로 제한되어 있습니다. (현재 활성사용자 ". $activeUserCount. "명)", "-1");
				exit;
			}
		}
	}


	$totalDistance = (int) $totalDistance;
	if($totalDistance < 1)
	{
		$totalDistance = 0;
	}


	if(empty($password) == false)
	{
		$password = LIB::getHashPassword($password);
	}

	if($seq > 0)
	{
		if($payment->getState() != Payment::$stateComplete)
		{
			$db->close();
			LIB::Alert("사용료가 결제되지 않아 이용하실 수 없습니다. 결제 후 이용해 주세요.", "-1");
			exit;
		}

		if($payment->isOverLicence() == true && $enabled == "Y")
		{
			$db->close();
			LIB::Alert("라이센스 수량 초과!! 사용하지 않는 사용자 계정을 이용정지 또는 삭제 후 이용해 주세요.", "-1");
			exit;
		}

		$completeMessage = "사용자 정보가 수정 되었습니다.";
		$DATA["uid"]						= $uid;
		$DATA["departmentSeq"]		= $departmentSeq;
		$DATA["dutySeq"]					= $dutySeq;
		$DATA["name"]						= $name;
		$DATA["carModel"]				= $carModel;
		$DATA["carNumber"]				= $carNumber;
		$DATA["totalDistance"]			= $totalDistance;
		$DATA["enabled"]					= $enabled;

		if(empty($password) == false)
		{
			$DATA["password"]				= $password;
		}

		$user->modifyUser($DATA);
	}
	else
	{
		$completeMessage = "사용자가 등록 되었습니다.";
		$DATA["uid"]						= $uid;
		$DATA["orgUid"]					= $orgUid;
		$DATA["password"]				= $password;
		$DATA["departmentSeq"]		= $departmentSeq;
		$DATA["dutySeq"]					= $dutySeq;
		$DATA["name"]						= $name;
		$DATA["carModel"]				= $carModel;
		$DATA["carNumber"]				= $carNumber;
		$DATA["totalDistance"]			= $totalDistance;
		$DATA["enabled"]					= $enabled;
		$DATA["companySeq"]			= $companySeq;
		$result = $user->registUser($DATA);

		if(empty($result) == true)
		{
			unset($DATA);
			$DATA["deviceId"] = "ADMIN";
			$db->Update("user", $DATA, "where uid='". $uid. "'", "update error");
		}
	}

	
}


$db->close();
if(empty($result) == false)
{
	LIB::Alert($result, "-1");
}
else
{
	LIB::Alert("", "openerReload");
	LIB::Alert($completeMessage, "close");
}
exit;
?>
