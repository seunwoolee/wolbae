<?php
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Step03
// 금일 업데이트된 주소DB와 거리DB 비교
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/mysql.inc.php";
include "../inc/user.inc.php";
include "../inc/json.inc.php";
include "../inc/lib.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Variable
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -

$PARAM = json_decode(file_get_contents("php://input"), JSON_UNESCAPED_UNICODE);

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CLASS
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$db = new Mysql();

//Json Class
$json = new Json();


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// CODE
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
/*
$vehicleGuestInfoData = $PARAM['vehicleGuestInfoData'];
$vehicleGuestInfoData02 = $PARAM['vehicleGuestInfoData'];
$vehicleGuestDistanceData;
$count=0;


$deliveryDate  = $PARAM["deliveryDate"]." 00:00:00";
//$deliveryDate  = "2017-09-29 00:00:00";

for($i=0;$i<count($vehicleGuestInfoData);$i++){
	for($j=0;$j<count($vehicleGuestInfoData02);$j++){
		$isList = false;
		
		if($deliveryDate < $vehicleGuestInfoData[$i]['updateDate'] || $deliveryDate < $vehicleGuestInfoData02[$j]['updateDate']){
			if($vehicleGuestInfoData[$i]['guestId']==$vehicleGuestInfoData02[$j]['guestId']){
				if($vehicleGuestInfoData[$i]['guestJusoSubId']!=$vehicleGuestInfoData02[$j]['guestJusoSubId']){
					$isList = true;
				}
			}
			else{
				$isList = true;
			}
			if($isList){
				$db->que = "SELECT count(vd_seq) AS distanceCount, vd_guestDate, vd_deguestDate 
					FROM vehicleGuestMutualDistance WHERE 
					vd_guestId='".$vehicleGuestInfoData[$i]['guestId']."' AND vd_guestJusoSubId='".$vehicleGuestInfoData[$i]['guestJusoSubId']."' AND 
					vd_deguestId='".$vehicleGuestInfoData02[$j]['guestId']."' AND 
					vd_deguestJusoSubId='".$vehicleGuestInfoData02[$j]['guestJusoSubId']."'"; 
				$db->query();
				$distanceResultData = $db->getRow();
				
				if($vehicleGuestInfoData[$i]['updateDate']!=$distanceResultData['vd_guestDate'] || 
					$vehicleGuestInfoData02[$j]['updateDate']!=$distanceResultData['vd_deguestDate']){

					$vehicleGuestDistanceData[$count]['guestId'] = $vehicleGuestInfoData[$i]['guestId'];
					$vehicleGuestDistanceData[$count]['deguestId'] = $vehicleGuestInfoData02[$j]['guestId'];

					$vehicleGuestDistanceData[$count]['guestJusoSubId'] = $vehicleGuestInfoData[$i]['guestJusoSubId'];
					$vehicleGuestDistanceData[$count]['deguestJusoSubId'] = $vehicleGuestInfoData02[$j]['guestJusoSubId'];

					$vehicleGuestDistanceData[$count]['guestLat'] = $vehicleGuestInfoData[$i]['guestLat'];
					$vehicleGuestDistanceData[$count]['deguestLat'] = $vehicleGuestInfoData02[$j]['guestLat'];
					$vehicleGuestDistanceData[$count]['guestLon'] = $vehicleGuestInfoData[$i]['guestLon'];
					$vehicleGuestDistanceData[$count]['deguestLon'] = $vehicleGuestInfoData02[$j]['guestLon'];
					$vehicleGuestDistanceData[$count]['guestDate'] = $vehicleGuestInfoData[$i]['updateDate'];
					$vehicleGuestDistanceData[$count]['deguestDate'] = $vehicleGuestInfoData02[$j]['updateDate'];

					if($distanceResultData['distanceCount'] > 0 ){
						$vehicleGuestDistanceData[$count]['status'] = 'UPDATE';
					}
					else{
						$vehicleGuestDistanceData[$count]['status'] = 'INSERT';
					}
					$count++;
				}
			}
		}
	}
}
*/

/*
$vehicleGuestInfoData = $PARAM['vehicleGuestInfoData'];
$vehicleGuestDistanceData;
$count=0;

$toDay = date("Y-m-d")." 00:00:00";
//2중 for문을 통해서 고객정보주소 A,B
for($i=0;$i<count($vehicleGuestInfoData);$i++){
	for($j=0;$j<count($vehicleGuestInfoData);$j++){
		//금일날짜 A혹은 B중에 있는지 판단 
		if($toDay <= $vehicleGuestInfoData[$i]['updateDate'] || $toDay <= $vehicleGuestInfoData[$j]['updateDate']){
			//만약 A,B중 둘다 동일한 guestId중에 동일한 서브 주소값이 있으면 패스
			if($vehicleGuestInfoData[$i]['guestId']==$vehicleGuestInfoData[$j]['guestId']){
				if($vehicleGuestInfoData[$i]['guestJusoSubId']==$vehicleGuestInfoData[$j]['guestJusoSubId']){
					continue;
				}
			}
			$db->que = "SELECT count(vd_seq) AS distanceCount, vd_guestDate, vd_deguestDate 
				FROM vehicleGuestMutualDistance WHERE 
				vd_guestId='".$vehicleGuestInfoData[$i]['guestId']."' AND vd_guestJusoSubId='".$vehicleGuestInfoData[$i]['guestJusoSubId']."' AND 
				vd_deguestId='".$vehicleGuestInfoData[$j]['guestId']."' AND 
				vd_deguestJusoSubId='".$vehicleGuestInfoData[$j]['guestJusoSubId']."'"; 
			$db->query();
			$distanceResultData = $db->getRow();
			
			if($vehicleGuestInfoData[$i]['updateDate']!=$distanceResultData['vd_guestDate'] || 
				$vehicleGuestInfoData[$j]['updateDate']!=$distanceResultData['vd_deguestDate']){

				$vehicleGuestDistanceData[$count]['guestId'] = $vehicleGuestInfoData[$i]['guestId'];
				$vehicleGuestDistanceData[$count]['deguestId'] = $vehicleGuestInfoData[$j]['guestId'];

				$vehicleGuestDistanceData[$count]['guestJusoSubId'] = $vehicleGuestInfoData[$i]['guestJusoSubId'];
				$vehicleGuestDistanceData[$count]['deguestJusoSubId'] = $vehicleGuestInfoData[$j]['guestJusoSubId'];

				$vehicleGuestDistanceData[$count]['guestLat'] = $vehicleGuestInfoData[$i]['guestLat'];
				$vehicleGuestDistanceData[$count]['deguestLat'] = $vehicleGuestInfoData[$j]['guestLat'];
				$vehicleGuestDistanceData[$count]['guestLon'] = $vehicleGuestInfoData[$i]['guestLon'];
				$vehicleGuestDistanceData[$count]['deguestLon'] = $vehicleGuestInfoData[$j]['guestLon'];
				$vehicleGuestDistanceData[$count]['guestDate'] = $vehicleGuestInfoData[$i]['updateDate'];
				$vehicleGuestDistanceData[$count]['deguestDate'] = $vehicleGuestInfoData[$j]['updateDate'];

				if($distanceResultData['distanceCount'] > 0 ){
					$vehicleGuestDistanceData[$count]['status'] = 'UPDATE';
				}
				else{
					$vehicleGuestDistanceData[$count]['status'] = 'INSERT';
				}
				$count++;
			}
			
		}
	}
}
*/



$vehicleGuestInfoData = $PARAM['vehicleGuestInfoData'];
$vehicleGuestDistanceData;
$count=0;

$toDay = date("Y-m-d")." 00:00:00";
//$toDay = "2017-10-30 00:00:00";


$tmpCnt = 0;

//2중 for문을 통해서 고객정보주소 A,B
for($i=0;$i<count($vehicleGuestInfoData);$i++){
	for($j=0;$j<count($vehicleGuestInfoData);$j++){
		//금일날짜 A혹은 B중에 있는지 판단 
		//if($toDay<=$vehicleGuestInfoData[$i]["updateDate"]||$toDay<=$vehicleGuestInfoData[$j]["updateDate"]){
			//만약 A,B중 둘다 동일한 guestId중에 동일한 서브 주소값이 있으면 패스
			if($vehicleGuestInfoData[$i]['guestId']==$vehicleGuestInfoData[$j]['guestId']){
				if($vehicleGuestInfoData[$i]['guestJusoSubId']==$vehicleGuestInfoData[$j]['guestJusoSubId']){
					if($vehicleGuestInfoData[$i]['isShop']==$vehicleGuestInfoData[$j]['isShop']){
						continue;
					}
				}
			}

			$db->que = "SELECT count(vd_seq) AS distanceCount, vd_updateDate  
								FROM 
									vehicleGuestMutualDistance WHERE 1=1
									AND vd_guestId='".$vehicleGuestInfoData[$i]['guestId']."' 
									AND vd_guestJusoSubId='".$vehicleGuestInfoData[$i]['guestJusoSubId']."' 
									AND vd_guestIsShop='".$vehicleGuestInfoData[$i]['isShop']."' 

									AND vd_deguestId='".$vehicleGuestInfoData[$j]['guestId']."' 
									AND vd_deguestJusoSubId='".$vehicleGuestInfoData[$j]['guestJusoSubId']."' 
									AND vd_deguestIsShop='".$vehicleGuestInfoData[$j]['isShop']."'"; 

			$db->query();
			$distanceResultData = $db->getRow();
			$status = "";

			//A,B두개의 고객정보날짜를 비교해서 큰거를 가져옴 - 이부분은 일단 나중에...
			if($vehicleGuestInfoData[$i]["updateDate"] >= $vehicleGuestInfoData[$j]["updateDate"]){
				$vehicleDate = $vehicleGuestInfoData[$i]["updateDate"];
			} else {
				$vehicleDate = $vehicleGuestInfoData[$j]["updateDate"];
			}

			//조회가 없을시 INSERT, 있을시 UPDATE
			if($distanceResultData['distanceCount'] > 0 ){
				//$status = 'UPDATE';
				continue;
			} else {
				$status = 'INSERT';
				$tmpCnt++;
			}

			//거리테이블 날짜보다 고객정보 날짜가 큰경우 위치정보 변경
			if($distanceResultData["vd_updateDate"] < $vehicleDate){

				$vehicleGuestDistanceData[$count]['status'] = $status;
				$vehicleGuestDistanceData[$count]['guestId'] = $vehicleGuestInfoData[$i]['guestId'];
				$vehicleGuestDistanceData[$count]['deguestId'] = $vehicleGuestInfoData[$j]['guestId'];

				$vehicleGuestDistanceData[$count]['guestJusoSubId'] = $vehicleGuestInfoData[$i]['guestJusoSubId'];
				$vehicleGuestDistanceData[$count]['deguestJusoSubId'] = $vehicleGuestInfoData[$j]['guestJusoSubId'];

				$vehicleGuestDistanceData[$count]['guestLat'] = $vehicleGuestInfoData[$i]['guestLat'];
				$vehicleGuestDistanceData[$count]['deguestLat'] = $vehicleGuestInfoData[$j]['guestLat'];

				$vehicleGuestDistanceData[$count]['guestLon'] = $vehicleGuestInfoData[$i]['guestLon'];
				$vehicleGuestDistanceData[$count]['deguestLon'] = $vehicleGuestInfoData[$j]['guestLon'];

				$vehicleGuestDistanceData[$count]['guestDate'] = $vehicleGuestInfoData[$i]['updateDate'];
				$vehicleGuestDistanceData[$count]['deguestDate'] = $vehicleGuestInfoData[$j]['updateDate'];

				$vehicleGuestDistanceData[$count]['guestIsShop'] = $vehicleGuestInfoData[$i]['isShop'];
				$vehicleGuestDistanceData[$count]['deguestIsShop'] = $vehicleGuestInfoData[$j]['isShop'];

				$vehicleGuestDistanceData[$count]['updateDate'] = $vehicleDate;
				//LIB::PLog($vehicleGuestDistanceData[$count]);
				$count++;

			}
		//}
	}
}

//LIB::PLog($vehicleGuestInfoData[$i]["guestId"].",".$vehicleGuestInfoData[$j]["guestId"]);


$json->add("vehicleGuestDistanceData", $vehicleGuestDistanceData);
$json->result["resultMessage"] = "고객배송중복제거데이터";
$json->result["resultCnt"] = $tmpCnt;
echo $json->getResult();
$db->close();
exit;
?>