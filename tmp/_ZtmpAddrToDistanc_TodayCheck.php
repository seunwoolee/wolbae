<?php
include "inc_html/header.html"; 


$db = new Mysql();
$db2 = new Mysql();


$db->que = " SELECT * FROM (SELECT * FROM (SELECT * FROM vehicleGuestOrderData WHERE ve_deliveryDate = '2018-04-06' AND ve_meridiemType = '1' AND ve_meridiemFlag = '1' AND ve_locationId = '0' AND ve_guestJuso != '' AND ve_guestLat != '0' AND ve_guestLon != '0' GROUP by ve_accno ) AS A GROUP BY A.ve_guestLat, A.ve_guestLon ORDER BY A.ve_accno ) AS B";
$db->query();

$row = $db->getRows();

//echo "[".$row[0]['vg_seq']."]";
//echo $row[1]['vd_seq'];

echo count($row)."<br>";

$countEmpty = 0;

for($i=0;$i<count($row);$i++){

	for($j=0;$j<count($row);$j++){



		$sql = "select * from vehicleGuestMutualDistance where 1=1
					and vd_guestId				='".$row[$i]['ve_guestId']."'
					and vd_guestJusoSubId		='".$row[$i]['ve_guestJusoSubId']."'
					and vd_guestIsShop			='".$row[$i]['ve_isShop']."'
					and vd_deguestId			='".$row[$j]['ve_guestId']."'
					and vd_deguestJusoSubId		='".$row[$j]['ve_guestJusoSubId']."'
					and vd_deguestIsShop		='".$row[$j]['ve_isShop']."'";

		//echo $sql."<br>";

		$db2->que = $sql;
		$db2->query();

		$row_sub = $db2->getRow();


		if($row_sub['vd_distanceValue']){
			//echo "[".$row[$i]['ve_guestId']."] -> [".$row[$j]['ve_guestId']."] = ".$row_sub['vd_distanceValue']."<br>";
		} else {
			$countEmpty++;
			echo "Empty [".$row[$i]['ve_guestId']."]->[".$row[$j]['ve_guestId']."]<br>";
		}

		/*
		if(count($row_sub) > 0){
			
		} else {

			// 주소는 있지만, 경로데이터가 없는건
			echo $row[$i]['vg_guestId']."	".$row[$j]['vg_guestId']."<br>";
		
		}
		*/

	}

}

echo $countEmpty;



?>