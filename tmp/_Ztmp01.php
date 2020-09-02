<?php
include "inc_html/header.html"; 


$db = new Mysql();
$db2 = new Mysql();


$db->que = " select * from vehicleGuestOrderData where 1=1
				and ve_locationId='0'
				and ve_meridiemType='0'
				and ve_meridiemFlag='1'
				and ve_deliveryDate='2018-03-26'";
$db->query();

$row = $db->getRows();

//echo "[".$row[0]['vg_seq']."]";
//echo $row[1]['vd_seq'];

echo count($row)."<br>";

for($i=0;$i<count($row);$i++){

	for($j=0;$j<count($row);$j++){


		if($row[$i]['ve_guestId'] == $row[$j]['ve_guestId']){
			continue;
		}

		echo "[".$i."] [".$j."]";


		if( ($row[$i]['ve_isJuso'] == "Y") && ($row[$j]['ve_isJuso'] == "Y") ){

			$sql = "select * from vehicleGuestMutualDistance where 1=1
						and vd_guestId				='".$row[$i]['ve_guestId']."'
						and vd_guestJusoSubId		='".$row[$i]['ve_guestJusoSubId']."'
						and vd_guestIsShop			='".$row[$i]['ve_isJuso']."'
						and vd_deguestId			='".$row[$j]['ve_guestId']."'
						and vd_deguestJusoSubId		='".$row[$j]['ve_guestJusoSubId']."'
						and vd_deguestIsShop		='".$row[$j]['ve_isJuso']."'";

			//echo $sql."<br>";

			$db2->que = $sql;
			$db2->query();

			$row_sub = $db2->getRow();

			//echo count($row_sub);

			if(count($row_sub) > 0){

				echo "[경로있음] ".$row[$i]['ve_guestId']."	".$row[$j]['ve_guestId'];
				
			} else {

				// 주소는 있지만, 경로데이터가 없는건
				echo "[경로없음] ".$row[$i]['ve_guestId']."	".$row[$j]['ve_guestId'];
			
			}

			//exit;
		} else {
			echo "[좌표정보없음] ".$row[$i]['ve_guestId']."	".$row[$j]['ve_guestId'];
		}

		echo "<bR>";

	}

}





?>