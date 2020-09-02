<?
header("Content-Type:text/html;charset=UTF-8");
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../lib/mssql/db.mssql.class.php";
include "../inc/json.inc.php";


// /usr/local/freetds/etc/freetds.conf
$conn = mssql_connect('egServer70','sa','rootdb1234!@#$');
$ms = new ms_db($conn,'DB_Jang_Erp');


/*
$conn = mssql_connect('egServer70', 'sa', 'rootdb!9');
mssql_select_db("DB_ERP", $conn) or DIE("error");

echo $conn;
exit;

$ms = new ms_db($conn, 'DB_ERP');
echo $ms;
exit;
*/

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$strSearchType			= $_GET['strSearchType'];
$strSearchWord			= $_GET['strSearchWord'];

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$json = new Json();

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Code
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
if($strSearchType == "CtCode"){
	$SearchSql = " AND ".$strSearchType."='".$strSearchWord."'";
} else {
	$SearchSql = " AND ".$strSearchType." like '%".$strSearchWord."%' ";
}

/*
$que = "SELECT CtCode, CCorpName, DevAddress1 FROM table_Corp_Customer WHERE 1=1 
			AND (JoinLocation = '0003' OR JoinLocation = '0010' ) ";
*/
$que = "SELECT CtCode, JoinUserName, Address1 FROM table_Customer  WHERE 1=1 ";
$que .= $SearchSql;

$mrow = $ms->ms_fetchrowset($ms->ms_query($que));

//echo count($mrow);
//echo $mrow;
//echo "[=========".$mrow[0]['CtCode']."========]";

for($i=0;$i<count($mrow);$i++){
	$data[] = array(
						"no" => $i,
						"vg_guestId"	=> $mrow[$i][CtCode],
						"vg_guestName"	=> $mrow[$i][JoinUserName],
						"vg_guestJuso"	=> $mrow[$i][Address1]
					);
}

$json->add("vehicleGuestInfoSearchList", $data);
$json->result["resultMessage"] = "고객정보데이터";
echo $json->getResult();

$ms->ms_close();
exit;

?>
