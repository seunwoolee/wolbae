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

session_start();
$company = new Company($_POST);
$companyInfo = $company->getCompany("demo");
$_SESSION["OMember_id"] = "demo";
$_SESSION["OMember_seq"] = $companyInfo["seq"];
$_SESSION["OMember_ikey"] = $companyInfo["ikey"];


$company->dbClose();
header('Location:../main.html');
exit;
?>
