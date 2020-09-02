<?php
exit;

include "inc_html/header.html"; 


$db = new Mysql();
$db2 = new Mysql();
$db3 = new Mysql();



//$db->que = "select distinct(GUESTID), LON, LAT from tmpData where 1=1 and IDATE='20170501' and LOCAL='0' and LON != '0' AND LAT != '0' order by GUESTID DESC limit 15 ";
$db->que = "SELECT *,sum(ve_pay) AS sumData FROM vehicleGuestOrderData WHERE ve_deliveryDate = '2017-11-02' AND ve_meridiemType='1' AND ve_guestJuso != '' AND ve_guestLat != '' AND ve_guestLon != '' AND ve_guestId!='admin';
";
$db->query();
$rowRESULT1 = $db->getRow();
echo "쿼리 종합가격=".$rowRESULT1["sumData"]."<br>";


$db->que = "SELECT count(A.ve_seq) AS count,A.ve_accno,ve_accnoDupleJuso FROM (SELECT * FROM vehicleGuestOrderData WHERE ve_deliveryDate = '2017-11-02' AND ve_meridiemType='1' ANd ve_locationId='0' AND ve_guestJuso != '' AND ve_guestLat != '' AND ve_guestLon != '' AND ve_guestId!='admin' GROUP by ve_accno
) AS A GROUP BY A.ve_guestLat,A.ve_guestLon ORDER BY A.ve_accno";
$db->query();

$rowRESULT2 = $db->getRows();
$resultTotal2 = 0;
$resultTotal3 = 0;

for($i=0;$i<count($rowRESULT2);$i++) {


		if($rowRESULT2[$i]['ve_accnoDupleJuso'] == ''){

			$db2->que = "select sum(ve_pay) as sums from vehicleGuestOrderData where 1=1 and ve_meridiemType='1' and ve_accno='".$rowRESULT2[$i]['ve_accno']."'";

			$db2->query();
			$rowRESULT3 = $db2->getRow();
			$resultTotal2 += $rowRESULT3['sums'];

			echo "단일 [".$i."]"."가격=".$rowRESULT3['sums']."<br>";
 
		} else {
			$db3->que = "select sum(ve_pay) as sums from vehicleGuestOrderData where 1=1 and ve_meridiemType='1' and ve_accnoDupleJuso='".$rowRESULT2[$i]['ve_accnoDupleJuso']."'";

			$db3->query();
			$rowRESULT4 = $db3->getRow();
			$resultTotal3 += $rowRESULT4['sums'];

			echo "중복 [".$i."]"."가격=".$rowRESULT4['sums']."<br>";
		
		}
	

}





echo "단일 데이터 종합가격=".$resultTotal2."<br>";
echo "중복 데이터 종합가격=".$resultTotal3."<br>";


?>