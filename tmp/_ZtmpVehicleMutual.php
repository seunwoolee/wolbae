<?php
include "inc_html/header.html"; 


$db = new Mysql();
$db2 = new Mysql();

$allCount=0;
$noAllCount=0;


$db->que = " SELECT ve_seq AS seq
					,ve_guestId				AS guestId
					,ve_guestJusoSubId		AS guestJusoSubId
					,ve_guestLat			AS guestLat
					,ve_guestLon			AS guestLon
					,ve_isShop				AS isShop 
					,vg_updateDate			AS updateDate
						FROM vehicleGuestOrderData 
						JOIN vehicleGuestInfo 
						ON ve_guestId=vg_guestId AND ve_guestJusoSubId=vg_guestJusoSubId AND ve_isShop = vg_isShop  
						WHERE ve_isJuso='Y' AND 
						ve_deliveryDate='2017-11-13' AND 
						ve_locationId='0' AND 
						ve_meridiemType='1' 
						GROUP BY ve_guestId,ve_guestJusoSubId,ve_isShop 
						ORDER BY ve_guestId ASC";
$db->query();

$rowRESULT2 = $db->getRows();
$resultTotal2 = 0;
$resultTotal3 = 0;

for($i=0;$i<count($rowRESULT2);$i++) {
	for($j=0;$j<count($rowRESULT2);$j++) {
		$db2->que = "SELECT count(vd_seq) AS distanceCount, vd_updateDate  
						FROM vehicleGuestMutualDistance WHERE 
						vd_guestId='".$rowRESULT2[$i]['guestId']."' AND vd_guestJusoSubId='".$rowRESULT2[$i]['guestJusoSubId']."' AND 
						vd_guestIsShop='".$rowRESULT2[$i]['isShop']."' AND 
						vd_deguestId='".$rowRESULT2[$j]['guestId']."' AND 
						vd_deguestJusoSubId='".$rowRESULT2[$j]['guestJusoSubId']."' AND 
						vd_deguestIsShop='".$rowRESULT2[$j]['isShop']."'"; 

			$db2->query();
			$distanceResultData = $db2->getRow();
			if($rowRESULT2[$i]['guestId']==$rowRESULT2[$j]['guestId']){
				if($rowRESULT2[$i]['guestJusoSubId']==$rowRESULT2[$j]['guestJusoSubId']){
					if($rowRESULT2[$i]['isShop']==$rowRESULT2[$j]['isShop']){
						continue;
					}
				}
			}
			//조회가 없을시 INSERT,있을시 UPDATE
			if($distanceResultData['distanceCount'] > 0 ){

				$allCount++;
				//echo "있다<br>";
			}
			else{
				echo "NONONONONONO:".$rowRESULT2[$i]['guestId'].",".$rowRESULT2[$j]['guestId']."<br>";
				$noAllCount++;
			}
	}
	

}


echo "있다:".$allCount."<br>";
echo "없다:".$noAllCount;


//9506


?>