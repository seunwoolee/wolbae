<?
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


//error_reporting(E_ALL);
//ini_set("display_errors", "1"); 



//include "../inc/db.mssql.class.php";



//$conn = mssql_connect(egServer70, 'sa', 'rootdb!9');
//$conn = mssql_connect('211.195.9.149:4171','sa','food1qaz!QAZ');
//$ms = new ms_db($conn,'DB_ERP');

//$conn = mssql_connect('211.195.9.154:47001','sa','root@123');

//$conn = mssql_connect('211.195.9.154:47001','sa','root@123') or DIE("DB Connect Fail");
$conn = mssql_connect('egServer70', _MSDBID, _MSDBPASS) or DIE("DB Connect Fail");
//$ms = new ms_db($conn, 'DB_ERP');
mssql_select_db(_MSDBNAME, $conn);


//echo "conn[".$conn."]";
//echo "ms[".$ms."]";

//exit;


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

// ERP서버 적용시 PARAM 값이 맞지 않을수 있으니, ERP서버측에서 넘어오는 데이터 확인 필요!!!
$PARAM = json_decode(file_get_contents("php://input"), JSON_UNESCAPED_UNICODE);

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$json = new Json();

//echo _MSDBHOST;


//$conn = mssql_connect(_MSDBHOST, _MSDBID, _MSDBPASS);
//echo "conn[".$conn."]";
//$ms = new ms_db($conn, _MSDBNAME);



//$conn = mssql_connect('211.195.9.131:4171','sa','rootdb!9');
//echo "conn[".$conn."]";
//$ms = new ms_db($conn, 'DB_ERP');

//echo "tetest";
//echo "ms[".$ms."]<br>";

//exit;

//$db = new Mysql();

/*
$sql = "select T1.*, T2.* FROM DB_ERP.dbo.table_OrderSheet T1
	left join JBJ_DB_JOINTEST.dbo.table_JShop_Master T2 on T1.CorpCode='JangBoGo' and T1.SaCode=T2.SaCode
	where 1=1
	and (T1.StCode='0023' or T2.CoutStCode='0023')
	AND (T1.devgubun = '2' or T2.DevGubun='2')
	AND (T1.DEvDDay = '2020-02-05' or T2.DevDDay='2020-02-05')
	AND (T1.IsMorning = '2' or T2.CIsMorning='2')
	and ( 
		(T1.IsStatus >= '1' and T1.IsStatus != '5' and T1.IsStatus != 'D')
		or	
		( T2.IsStatus = '3' and T2.IsStatus != '5' )
	)
	order by T2.sacode desc";
*/

$sql = "select 
			ISNULL(T2.SaCode, T1.SaCode) AS SaCode 
			,ISNULL(T2.DevDDay, T1.DEvDDay) AS DevDDay
			,ISNULL(T2.CtCode, T1.CtCode) AS CtCode
			,ISNULL(T2.ReceverName, T1.OrderName) AS OrderName
			,ISNULL(ISNULL(T2.OrderMobile, T1.OrderMobile), '') AS OrderMobile
			,ISNULL(ISNULL(T2.DevAddress, T1.DevAddress), '') AS DevAddress
			,ISNULL(T2.TotalPrice, T1.TotalSalePrice) AS TotalSalePrice
			,ISNULL(T3.DevX, '') AS DevX
			,ISNULL(T3.DevY, '') AS DevY

			FROM [DB_ERP].[dbo].[table_OrderSheet] T1
				LEFT JOIN [JBJ_DB_JOINTEST].[dbo].[table_JShop_Master] T2 ON T1.CorpCode='JangBoGo' AND T1.SaCode=T2.SaCode
				LEFT JOIN [DB_ERP].[dbo].[table_Corp_Customer] T3 ON T1.CorpCode='JangBoGo' AND T1.CtCode=T3.CtCode
			where 1=1
				AND (T1.StCode='0023' OR T2.CoutStCode='0023')
				AND (T1.devgubun = '2' OR T2.DevGubun='2')
				AND (T1.DEvDDay = '2020-02-11' OR T2.DevDDay='2020-02-11')
				AND (T1.IsMorning = '1' OR T2.CIsMorning='1')
				AND ( 
						(T1.IsStatus >= '1' AND T1.IsStatus != '5' AND T1.IsStatus != 'D')
						OR	
						( T2.IsStatus = '3' AND T2.IsStatus != '5' )
				)
			ORDER BY T2.sacode DESC";


//$sql = " select top 10 SaCode,UserName FROM [DB_ERP].[dbo].[table_OrderSheet] ";

//$sql = " select top 10 OrderNo, ReqNo FROM [JBJ_DB_JOINTEST].[dbo].[table_JShop_Master] ";
//$que = " select 'test' ";

/*
$mrow = $ms->ms_fetchrowset($ms->ms_query($que));
$nums = $ms->ms_numrows($ms->ms_query($que));

echo "mrow :[".$mrow."]";
for($i=0;$i<$nums;$i++){
	echo "tetest<br>";
}
*/

$result = mssql_query($sql, $conn);
//$row = mssql_fetch_row($result)
$nIndex = 0;
while($row = mssql_fetch_array($result)) {
	//echo "SaCode[".$row['SaCode']." UserName[".$row['UserName']."]<br>";

	$vehicleGuestOrderDataList[$nIndex]['no']			= ($nIndex+1);
	$vehicleGuestOrderDataList[$nIndex]['accno']		= $row['SaCode'];
	$vehicleGuestOrderDataList[$nIndex]['locationId']	= "2";
	$vehicleGuestOrderDataList[$nIndex]['deliveryDate']	= $row['DevDDay'];
	$vehicleGuestOrderDataList[$nIndex]['meridiemType']	= "0";
	$vehicleGuestOrderDataList[$nIndex]['guestId']		= $row['CtCode'];
	$vehicleGuestOrderDataList[$nIndex]['name']			= $row['OrderName'];
	$vehicleGuestOrderDataList[$nIndex]['guestTel']		= $row['OrderMobile'];
	$vehicleGuestOrderDataList[$nIndex]['jusoSubid']	= "1";
	$vehicleGuestOrderDataList[$nIndex]['juso']			= $row['DevAddress'];
	$vehicleGuestOrderDataList[$nIndex]['pay']			= $row['TotalSalePrice'];
	$vehicleGuestOrderDataList[$nIndex]['lat']			= $row['DevX'];
	$vehicleGuestOrderDataList[$nIndex]['lon']			= $row['DevY'];

	$nIndex++;
}




//exit;



//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -



$json->add("vehicleGuestOrderDataList", $vehicleGuestOrderDataList);
$json->result["resultMessage"] = "tetestset";
echo $json->getResult();
//$ms->ms_close();
mssql_close($conn);
exit;

?>