<?
class User {
	var $db;
	var $fetchSize = 20;
	var $pageCount = 5;
	var $findkey;
	var $pageNum;
	var $where;

	
	function User($db)
	{
		$this->db = $db;
	}
	
	
	static function getEnabled($enabled)
	{
		$stateName["ALL"]			= "전체 사용자";
		$stateName["N"]				= "미승인";
		$stateName["C"]				= "기기변경";
		$stateName["Y"]				= "승인";
		$stateName["B"]				= "사용중지";
		
		
	
		return $stateName[$enabled];
	}

	function registUser($user)
	{
		$this->db->que = "SELECT * FROM user WHERE uid='". $user["uid"]. "'";
		$this->db->query();
		
		if($this->db->affected_rows() > 0)
		{
			return "이미 사용중인 아이디 입니다.";
		}
		else
		{
			$DATA["uid"]					= $user["uid"];
			$DATA["orgUid"]				= $user["orgUid"];
			$DATA["password"]			= $user["password"];
			$DATA["departmentSeq"]	= $user["departmentSeq"];
			$DATA["dutySeq"]				= $user["dutySeq"];
			$DATA["name"]					= $user["name"];;
			$DATA["companySeq"]		= $user["companySeq"];
			
			$DATA["enabled"]				= $user["enabled"];
			$this->db->Insert("user", $DATA, "user insert error");
		}
	}

	function modifyUser($user)
	{
		$DATA["name"]					= $user["name"];
		$DATA["departmentSeq"]	= $user["departmentSeq"];
		$DATA["dutySeq"]				= $user["dutySeq"];


		//관리자 웹에서 수정할때  BEGIN ---------------------
		if(is_int($user["totalDistance"]) == true)
		{
			$DATA["totalDistance"]		= $user["totalDistance"];
		}
		if(empty(trim($user["enabled"])) == false)
		{
			$DATA["enabled"]				= $user["enabled"];
		}
		//관리자 웹에서 수정할때  END ---------------------

		$this->db->Update("user", $DATA, "WHERE uid='". $user["uid"]. "'",  "update user error");
	}


	function modifyTotalDistance($uid, $totalDistance)
	{
		$DATA["totalDistance"] = $totalDistance;
		$this->db->Update("user", $DATA, "WHERE uid='". $uid. "'",  "update user error");
	}

	function setDevice($uid, $device)
	{
		$user = $this->getUser($uid);

		//기기변경시 임시 사용 중지
		if($user["lockDeviceChange"] == "Y")
		{
			//기기변경시 임시 사용 중지
			if($user["deviceId"] == "ADMIN")
			{
				//pass (관리자가 추가한 계정은 기기변경 처리 1회 생략)
			}
			else
			{
				if(trim($user["deviceId"]) != trim($device["deviceId"]))
				{
					if(empty(trim($user["deviceId"])))
					{
						$DATA["enabled"] = "N";
					}
					else
					{
						$DATA["enabled"] = "C";
					}
				}
			}
		}

		$DATA["deviceId"]				= $device["deviceId"];
		$DATA["pushId"]					= $device["pushId"];
		$DATA["osType"]					= $device["osType"];
		$DATA["osVersion"]				= $device["osVersion"];
		$DATA["versionName"]			= $device["versionName"];
		$DATA["country"]					= $device["country"];
		$DATA["language"]				= $device["language"];
		$DATA["model"]					= $device["model"];

		if(empty(trim($device["pushId"])) == false)
		{
			$DATA["pushId"]				= $device["pushId"];
		}

		$this->db->Update("user", $DATA, "where uid='". $uid. "'", "device update error");
	}

	function checkAuthority($uid)
	{
		$this->db->que = "SELECT * FROM user WHERE uid='". $uid. "'";
		$this->db->query();
		if($this->db->affected_rows() < 1)
		{
			return "계정이 존재하지 않습니다.";
		}
		else
		{
			$row = $this->db->getRow();
			if($row["enabled"] == "B")
			{
				return "이용이 정지된 계정 입니다.";
			}
			else
			{
				$this->db->que = "SELECT * FROM company WHERE seq=". $row["companySeq"];
				$this->db->query();
				if($this->db->affected_rows() < 1)
				{
					return "회사정보 오류!!";
				}
				else
				{
					$row = $this->db->getRow();
					if($row["payment"] != "Y")
					{
						return "사용료가 결재되지 않아 이용 하실 수 없습니다.";
					}
					else
					{
						if($row["enabled"] == "N")
						{
							return "이용이 정지된 회사의 계정 입니다.";
						}
						else if($row["enabled"] == "X")
						{
							return "회사정보 오류!!";
						}
						else
						{
							return "";
						}
					}
				}
			}
		}
	}

	function login($uid, $password)
	{
		$result = $this->checkAuthority($uid);
		if(empty($result))
		{
			$this->db->que = "SELECT * FROM user WHERE uid='". $uid. "'";
			$this->db->query();
			$user = $this->db->getRow();

			if($user["password"] != $password)
			{
				return "비밀번호가 일치하지 않습니다.";
			}
			else
			{
				return "";
			}
		} else {
			return $result;
		}
	}



	function getUser($uid)
	{
		$this->db->que = "SELECT u.*, de.name AS departmentName, du.name AS dutyName, ";
		$this->db->que .= "c.email AS adminEmail, c.lockDistance, c.lockDate, c.lockTime, c.lockSaveMapPoint, c.lockDeviceChange, c.ikey, c.name AS companyName, c.logoEnabled, c.logoUrl, c.autoStartMessage, c.autoStopMessage FROM user AS u ";
		$this->db->que .= " JOIN company AS c ON u.companySeq=c.seq ";
		$this->db->que .= " LEFT JOIN duty AS du ON u.dutySeq=du.seq ";
		$this->db->que .= " LEFT JOIN department AS de ON u.departmentSeq=de.seq ";
		$this->db->que .= " WHERE u.uid='". $uid. "'";
		
		$this->db->query();
		$user = $this->db->getRow();
		$user["password"] = "";

		if($user["logoEnabled"] != "Y")
		{
			$user["logoUrl"] = "";
		}


		$begin = date("Y"). "-". date("m"). "-01";
		$end = date("Y"). "-". date("m"). "-31";
		$this->db->que = "SELECT SUM(distance) FROM drivingLog WHERE userUid='". $uid. "' AND startDate >= '". $begin. "' AND startDate <= '". $end. "'";
		$this->db->query();
		$user["thisMonthDistance"] = $this->db->getOne();
		$user["thisMonth"] = date("m");
		return $user;
	}

}
