<?
header("Content-Type:text/html;charset=UTF-8");
//###################################################
// 로그인
// 2014.02.07
//###################################################


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/json.inc.php";
include "../inc/mysql.inc.php";
include "../inc/company.inc.php";

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$company = new Company($_POST);

//Json Class
$json = new Json();



$enabled = $company->getEnabled();

if(empty($enabled) || $enabled == "X")
{
	$json->add("code", "ERROR");
	$json->add("message", "아이디가 존재하지 않습니다.");
} 
else if($enabled != "Y")
{
	$json->add("code", "ERROR");
	$json->add("message", "이용이 정지된 계정 입니다.");
}
else if($company->checkPassword() == false)
{
	$json->add("code", "ERROR");
	$json->add("message", "비밀번호가 일치하지 않습니다.");
} 
else
{

	$companyInfo = $company->getCompany($_POST["cid"]);

	session_start();
	$_SESSION["OMember_id"] = $_POST["cid"];
	$_SESSION["OMember_seq"] = $companyInfo["seq"];
	$_SESSION["OMember_ikey"] = $companyInfo["ikey"];

	$company->setAutoLogin();

	$json->add("code", "OK");
	$json->add("message", "");
}


echo $json->getResult();
$company->dbClose();
exit;

?>
