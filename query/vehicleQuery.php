<?php
class VehicleQuery
{
	var $db;
	function Vehicle()
	{	
	}

	function init($db){
		$this->db = $db;
	}
	function getCompanyInfo(){
		$ci_guestId			=	$_SESSION["OMember_id"];

		$this->db->que = "SELECT * FROM companyInfo WHERE ci_guestId='".$ci_guestId."'";
		$this->db->query();

		return $this->db->getRow();
	}

	//function getMapGroupListData($deliveryDate, $meridiemType, $locationId){
	function getMapGroupListData($deliveryDate, $meridiemType, $locationId, $meridiemFlag){
		$ci_guestId			=	$_SESSION["OMember_id"];

		$this->db->que = "SELECT 
							 A.vr_vehicleNo									AS vehicleNo
							,COUNT(distinct vr_deguestLat,vr_deguestLon)	AS count
							,SUM(A.vr_distanceValue)						AS sum 
						    ,SUM(A.vr_deguestPay)							AS deguestPay
							,A.vr_meridiemFlag								AS meridiemFlag
							,A.vr_deliveryDate								AS deliveryDate
								FROM (SELECT * FROM vehicleAllocateResult 
										WHERE 1=1
										AND vr_deliveryDate='".$deliveryDate."' 
										AND vr_meridiemType='".$meridiemType."' 
										AND vr_meridiemFlag='".$meridiemFlag."' 
										AND vr_locationId='".$locationId."'  
										GROUP BY vr_vehicleNo,vr_vehicleNoIndex) AS A 
								GROUP BY A.vr_vehicleNo 
								ORDER BY A.vr_vehicleNo*1 ASC, A.vr_vehicleNoIndex ASC";

		/*
		echo "SELECT 
							 A.vr_vehicleNo									AS vehicleNo
							,COUNT(distinct vr_deguestLat,vr_deguestLon)	AS count
							,SUM(A.vr_distanceValue)						AS sum 
						    ,SUM(A.vr_deguestPay)							AS deguestPay
								FROM (SELECT * FROM vehicleAllocateResult 
										WHERE 1=1
										AND vr_deliveryDate='".$deliveryDate."' 
										AND vr_meridiemType='".$meridiemType."' 
										AND vr_meridiemFlag='".$meridiemFlag."' 
										AND vr_locationId='".$locationId."'  
										GROUP BY vr_vehicleNo,vr_vehicleNoIndex) AS A 
								GROUP BY A.vr_vehicleNo 
								ORDER BY A.vr_vehicleNo*1 ASC, A.vr_vehicleNoIndex ASC";
		*/


		/*
		$this->db->que = " SELECT 
							 vr_vehicleNo		AS vehicleNo 
							,count(distinct vr_deguestLat,vr_deguestLon)		AS count 
							,sum(distinct vr_distanceValue)	AS sum 
								FROM vehicleAllocateResult 
								WHERE vr_deliveryDate='".$deliveryDate."' AND 
								vr_meridiemType='".$meridiemType."' AND 
								vr_locationId='".$locationId."'
								GROUP BY vr_vehicleNo 
								ORDER BY vr_vehicleNo*1 ASC, vr_vehicleNoIndex ASC";
		*/
		$this->db->query();
		return $this->db->getRows();
	}

	// 전체금액계산
	function getMapGroupListDataSumPay($deliveryDate, $meridiemType, $locationId, $nCountVehicle, $meridiemFlag){

		for($i=0;$i<$nCountVehicle;$i++){

			$this->db->que = "SELECT * FROM vehicleAllocateResult 
											WHERE 1=1 
												AND vr_deliveryDate='".$deliveryDate."' 
												AND vr_meridiemType='".$meridiemType."' 
												AND vr_meridiemFlag='".$meridiemFlag."' 
												AND vr_locationId='".$locationId."' 
												AND vr_vehicleNo='".$i."'";
			$this->db->query();
			$row = $this->db->getRows();

			$nSumPay = 0;
			$accnoDupleJuso = '';
			$accnoDupleJusoCopy = '';
			$accnoDupleCnt = 0;

			$deguestId = '';
			$deguestIdCopy = '';

			// 중첩지역 계산
			if(count($row) > 0){
				for($j=0;$j<count($row);$j++){
					$nSumPay += $row[$j]['vr_deguestPay'];
					
					$accnoDupleJuso = $row[$j]['vr_accnoDupleJuso'];
					$deguestId = $row[$j]['vr_deguestId'];
					if($accnoDupleJuso != '' && ($accnoDupleJusoCopy == $accnoDupleJuso) && ($deguestIdCopy != $deguestId)){
						$accnoDupleCnt++;
					} else {
						$accnoDupleJusoCopy = $accnoDupleJuso;
						$deguestIdCopy = $deguestId;
					}
				}
				$list[$i]['nSumPay'] = $nSumPay;				// 합계저장
				$list[$i]['nAccnoDupleCnt'] = $accnoDupleCnt;	// 중첩갯수 저장

			} else {
				$list[$i]['nSumPay'] = 0;						// 합계저장
				$list[$i]['nAccnoDupleCnt'] = 0;				// 중첩갯수 저장
			}
		}

		return $list;
	}

	/*
	function getErrorVehicleCount($deliveryDate, $meridiemType, $locationId, $meridiemFlag){
		$this->db->que = "SELECT count(ve_seq) AS errorCount 
							FROM vehicleGuestOrderData 
								WHERE 1=1
									AND ve_deliveryDate = '".$deliveryDate."' 
									AND ve_meridiemType='".$meridiemType."' 
									AND ve_meridiemFlag='".$meridiemFlag."' 
									AND ve_locationId='".$locationId."' 
									AND ve_guestId != 'admin' 
									AND ve_isJuso = 'N' 
									AND ve_errorJusoFlag != 'Y'
										GROUP BY ve_guestId,ve_guestJusoSubId,ve_isShop ";
		$this->db->query();
		return $this->db->affected_rows();
	}
	*/
	function getErrorVehicleCount($deliveryDate, $meridiemType, $locationId, $meridiemFlag){
		$this->db->que = "SELECT count(ve_seq) AS errorCount 
							FROM vehicleGuestOrderData 
								WHERE 1=1
									AND ve_deliveryDate = '".$deliveryDate."' 
									AND ve_meridiemType='".$meridiemType."' 
									AND ve_meridiemFlag='".$meridiemFlag."' 
									AND ve_locationId='".$locationId."' 
									AND ve_guestId != 'admin' 
									AND ve_isJuso = 'N' 
									AND ve_errorJusoFlag != 'Y' ";
		$this->db->query();
		
		return $this->db->getOne();
	}
	
	function getVehicleComplete($deliveryDate, $meridiemType, $locationId, $meridiemFlag){

		$this->db->que = "SELECT vs_vehicleEndStatus AS vehicleEndStatus 
							FROM vehicleAllocateStatus 
								WHERE 1=1
									AND vs_deliveryDate = '".$deliveryDate."' 
									AND vs_meridiemType='".$meridiemType."' 
									AND vs_meridiemFlag='".$meridiemFlag."' 
									AND vs_locationId='".$locationId."'";
		$this->db->query();
		return $this->db->getOne();

	}

	
}


?>