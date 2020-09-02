<?
header("Content-Type:text/html;charset=UTF-8");

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "inc/config.php";
include "inc/lib.inc.php";
include "inc/mysql.inc.php";
//include "inc/receipt.inc.php";


//-----------------------------------------------------------------------------------------------
// 결제 정보 입력
//-----------------------------------------------------------------------------------------------
$db = new Mysql();

$db->que = "SELECT * FROM company WHERE payment='Y' AND enabled='Y'";
$db->query();
while($row = $db->getRow())
{
	if(empty($row["licenceExpireDate"]) == false)
	{
		$endTime = strtotime($row["licenceExpireDate"]);
		$beginTime = strtotime("-". $row["paymentTerm"]. " month", $endTime);
		if($beginTime > time())
		{
			$beginTime = time();
		}

		$beginDate = date("Y-m-d", $beginTime);

		$totalAmount = $row["paymentTerm"] * 5000 * $row["licenceQuantity"];
		if($row["paymentTerm"] == 36)
		{
			$discount = 15;
		}
		else if($row["paymentTerm"] == 24 || $row["paymentTerm"] == 12)
		{
			$discount = 10;
		}
		else if($row["paymentTerm"] == 6)
		{
			$discount = 5;
		}
		else
		{
			$discount = 0;
		}

		$discountAmount = $totalAmount - ($discount/100*$totalAmount);
		$amount = $discountAmount * 1.1;

		$query .= ", ('Complete', 'SC0999',". $row["seq"]. ",". $row["licenceQuantity"]. ",". $row["paymentTerm"]. ",'". $beginDate. "', '". $row["licenceExpireDate"]. "', ". $totalAmount. ", ". $discountAmount. ", ". $amount. ", ". $discount. ",'Y') ";
	}
}

$query = "insert into payment (state, type, companySeq, licenceQuantity, term, beginDate, endDate, totalAmount, discountAmount, amount, discount, finally) values ". substr($query, 1, strlen($query));
$db->que = $query;
$db->query();

echo str_replace(", (", "<br>, (", $query);
//-----------------------------------------------------------------------------------------------
// 영수증 기본 정보 입력
//-----------------------------------------------------------------------------------------------
/*$db = new Mysql();
$db2 = new Mysql();
$receipt = new Receipt($db, $companySeq);


$db2->que = "SELECT c.seq, c.name, count(*) AS count FROM company AS c LEFT OUTER JOIN receiptAccountCode AS r ON c.seq=r.companySeq group by c.seq";
$db2->query();
while($row = $db2->getRow())
{
	if($row["count"] == 1)
	{
		$receipt->setDefaultReceiptInfo($row["seq"]);
		echo "insert : ". $row["name"]. "<br>";
	}
	else
	{
		echo "pass : ". $row["name"]. "<br>";
	}
}
*/
?>