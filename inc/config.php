<?
session_start();
header("Content-Type:text/html;charset=UTF-8");

//---+---+---+---+---+---+---+---+---+---+---+---+
// mysql 접속 정보
//---+---+---+---+---+---+---+---+---+---+---+---+

//define("_DEBUG_MODE", true);

define("_DBHOST", "127.0.0.1");
define("_DBNAME", "jangboja_wolbae");
define("_DBID", "wolbae");
define("_DBPASS", "wolbae^^12");



define("_MSDBHOST", "211.195.9.131:4171");
define("_MSDBNAME", "DB_Jang_Erp");//DB_Jang_Erp
define("_MSDBID", "sa");
define("_MSDBPASS", "rootdb1234!@#$");

//---+---+---+---+---+---+---+---+---+---+---+---+
// 경로
//---+---+---+---+---+---+---+---+---+---+---+---+


// Data 경로
//define("_DATA_DIR", $_SERVER["DOCUMENT_ROOT"]. "/data/jangbogo");
//define("_DATA_SERVER", "http://". $_SERVER['HTTP_HOST']. "/data/jangbogo");
define("_DATA_DIR", $_SERVER["DOCUMENT_ROOT"]. "");
define("_DATA_SERVER", "http://". $_SERVER['HTTP_HOST']. "");


// 홈디렉토리
define("_HOME", "");

// 라이브러리
define("_INC", _HOME. "/inc");

//타이틀
define("_TITLE", "장보고 자동배차시스템");

//url
$host = "http://". $_SERVER["HTTP_HOST"];
define("_HTTPHOST", $host);

//---+---+---+---+---+---+---+---+---+---+---+---+
// 로그인 변수
//---+---+---+---+---+---+---+---+---+---+---+---+
if(strlen($_SESSION["OMember_id"]) > 2){
	$LOGIN_ID			= $_SESSION["OMember_id"];
	$COMPANY_SEQ		= $_SESSION["OMember_seq"];
	$LOGIN = true;
} else {
	$LOGIN = false;
}

?>