<?php
include "inc_html/header.html"; 


$db = new Mysql();
$db2 = new Mysql();
$db3 = new Mysql();



//$db->que = "select distinct(GUESTID), LON, LAT from tmpData where 1=1 and IDATE='20170501' and LOCAL='0' and LON != '0' AND LAT != '0' order by GUESTID DESC limit 15 ";
$db->que = "SELECT * FROM(SELECT * FROM vehicleGuestOrderData WHERE 1=1 AND vo_deliveryDate = '2017-05-01' AND vo_meridiemType='0' AND vo_guestJuso != '' AND vo_guestLat != '' AND vo_guestLon != '') AS orderData GROUP BY orderData.vo_guestLat,vo_guestLon ORDER BY orderData.vo_guestId DESC";
$db->query();
$rowRESULT1 = $db->getRows();
echo "count[".count($rowRESULT1)."]<br>";


// 108 1 ~ 108

// 1 지점 = 배송물품(주문번호) //

$resultTotal2 = 0;
$resultTotal3 = 0;

$resultSum2 = 0;
$resultSum3 = 0;
$paySum = 0;
$modPaySum = 0;



//$mod = 1542552.0;
$mod = 2000000.0;

for($i=0;$i<count($rowRESULT1);$i++) {

	if($rowRESULT1[$i]['vo_guestId'] == 'admin'){
	
	} else {

		if($rowRESULT1[$i]['vo_accnoDupleJuso'] == ''){

			/*
			$db2->que = "select count(*) as cnt2 from vehicleGuestOrderData where 1=1 and vo_meridiemType='0' and vo_accno='".$rowRESULT1[$i]['vo_accno']."'";
			//echo "select * from vehicleGuestOrderData where 1=1 and vo_meridiemType='0' and vo_accno='".$rowRESULT1[$i]['vo_accno']."';<br>";
			$db2->query();
			$rowRESULT2 = $db2->getRow();
			$resultTotal2 += $rowRESULT2['cnt2'];
			*/

			$db2->que = "select sum(vo_pay * vo_gsu) as paySum from vehicleGuestOrderData where 1=1 and vo_locationId='0' and vo_meridiemType='0' and vo_accno='".$rowRESULT1[$i]['vo_accno']."'";
			//echo "select * from vehicleGuestOrderData where 1=1 and vo_meridiemType='0' and vo_accno='".$rowRESULT1[$i]['vo_accno']."';<br>";
			$db2->query();
			$rowRESULT2 = $db2->getRow();
			$resultSum2 = $rowRESULT2['paySum'];
			$paySum += $resultSum2;
			echo "현재금액[".number_format($resultSum2)."]	누적금액[".number_format($paySum)."]<br>";

			$modPaySum += $resultSum2;
			if($modPaySum > $mod){
				echo "-------------------------------------------중간누적값[".number_format($modPaySum)."]<br>";
				$modPaySum = 0; // 초기화
			}

		} else {

			/*
			$db3->que = "select count(*) as cnt3 from vehicleGuestOrderData where 1=1 and vo_meridiemType='0' and vo_accnoDupleJuso='".$rowRESULT1[$i]['vo_accnoDupleJuso']."'";
			//echo "---- select * from vehicleGuestOrderData where 1=1 and vo_meridiemType='0' and vo_accnoDupleJuso='".$rowRESULT1[$i]['vo_accnoDupleJuso']."';<br>";
			$db3->query();
			$rowRESULT3 = $db3->getRow();
			$resultTotal3 += $rowRESULT3['cnt3'];
			//echo $rowRESULT3['cnt3']."<br>";
			*/
			$db3->que = "select sum(vo_pay * vo_gsu) as paySum from vehicleGuestOrderData where 1=1 and vo_locationId='0' and vo_meridiemType='0' and vo_accnoDupleJuso='".$rowRESULT1[$i]['vo_accnoDupleJuso']."'";
			$db3->query();
			$rowRESULT3 = $db3->getRow();
			$resultSum3 = $rowRESULT3['paySum'];
			$paySum += $resultSum3;
			echo "현재금액[".number_format($resultSum3)."]	누적금액[".number_format($paySum)."]<br>";

			$modPaySum += $resultSum3;
			if($modPaySum > $mod){
				echo "-------------------------------------------중간누적값[".number_format($modPaySum)."]<br>";
				$modPaySum = 0; // 초기화
			}
		}
	}
}

echo "합계[".number_format($paySum)."]<bR>";

$db->close();
$db2->close();
$db3->close();

exit;

$db4 = new Mysql();

//중복된 주소를 업데이트
$db4->que = "SELECT count(A.vo_seq) AS dupleCount, A.vo_accno,A.vo_guestJuso,vo_guestLat,vo_guestLon FROM 
			(SELECT * FROM vehicleGuestOrderData WHERE vo_deliveryDate ='2017-05-01' AND 
				vo_meridiemType='0' AND 
				vo_locationId='0' AND 
				vo_guestJuso != '' AND 
				vo_guestLat != '' AND 
				vo_guestLon != '' 
				GROUP by vo_accno
			) AS A 
			GROUP BY A.vo_guestLat, A.vo_guestLon 
			HAVING count(dupleCount) > 1 
			ORDER BY A.vo_accno;";
$db4->query();
$item = $db4->getRows();

for($i=0;$i<count($item);$i++){

	unset($DATA);
	$DATA["vo_accnoDupleJuso"] = $item[$i]["vo_accno"];
	$WHERE = "WHERE vo_guestLat='".$item[$i]["vo_guestLat"]."' AND 
					vo_guestLon='".$item[$i]["vo_guestLon"]."' AND 
					vo_deliveryDate='2017-05-01' AND 
					vo_locationId='0' AND 
					vo_meridiemType='0'";
	//$db->Update("vehicleGuestOrderData", $DATA, $WHERE, " vehicleGuestOrderData Insert Error ");
	//echo "update vehicleGuestOrderData set vo_accnoDupleJuso = '".$item[$i]["vo_accno"]."' ".$WHERE."<br>";
}

//echo "resultTotal2[".$resultTotal2."]<br>";
//echo "resultTotal3[".$resultTotal3."]<br>";



$db4->close();


?>
