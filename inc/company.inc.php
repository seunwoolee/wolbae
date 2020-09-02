<?

class Company extends Mysql
{
	var $DATA;						//$_POST 로 넘어온 파라메터
	var $db;							//mysql Class

	var $autoLoginParamUid = "Cds_cid";
	var $autoLoginParamKey = "Cds_key";


	function Company($DATA)
	{
		$this->DATA = $DATA;
		$this->db = new Mysql();
	}
	

	static function createIkey($db, $cid)
	{
		$char = strtoupper(substr($cid, 0, 1));
		if(!preg_match("/^[A-Z]$/", $char))
		{
			$char = LIB::randomString(1);
		}

		$db->que = "SELECT * FROM ikey WHERE alphabet='". $char. "' ORDER BY seq DESC LIMIT 1";
		$db->query();
		if($db->affected_rows() < 1)
		{
			$DATA["seq"] = 100;
		}
		else
		{
			$row = $db->getRow();
			$DATA["seq"] = $row["seq"] + 1;
		}

		$DATA["alphabet"] = $char;
		$db->Insert("ikey", $DATA, "insert ikey");

		return $char. $DATA["seq"];
	}



	//##########################################################################
	//##########################################################################
	// 로그인
	//##########################################################################

	
	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	// 계정 존재 여부
	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	function getEnabled()
	{
		$this->db->que = "SELECT * FROM company WHERE cid='". $this->DATA["cid"]. "'";

		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			$row = $this->db->getRow();
			return $row["enabled"];
		}
		else
		{
			return null;
		}
	}

	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	// 유저 정보
	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	function getCompany($cid)
	{
		$this->db->que = "SELECT * FROM company WHERE cid='". $cid. "'";

		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			return $this->db->getRow();
		}
		else
		{
			return null;
		}
	}

	
	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	// 비밀번호 일치 여부
	//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
	
	
	function checkPassword()
	{
 		
		$securePassword = trim(base64_encode(hash('sha256', $this->DATA["password"], true))); 
		$this->db->que = "SELECT seq FROM company WHERE cid='". $this->DATA["cid"]. "' AND  password='". $securePassword. "'";
		$this->db->query();

		if($this->db->affected_rows() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	


	function setAutoLogin()
	{
		if($this->DATA["autoLogin"] == "Y")
		{
			$DATA["autoLoginKey"] = uniqid();
			$this->db->Update("company", $DATA, "where cid='". $this->DATA["cid"]. "'", "update error");

			$time = time()+60*60*24*365;
			setcookie($this->autoLoginParamUid, $this->DATA["cid"], $time , '/');
			setcookie($this->autoLoginParamKey, $DATA["autoLoginKey"], $time , '/');
		}
		else
		{
			//$DATA["autoLoginKey"] = "";
			//$this->db->Update("admin", $DATA, "where uid='". $this->DATA["uid"]. "'", "update error");
			setcookie($this->autoLoginParamUid, "", 0 , '/');
			setcookie($this->autoLoginParamKey, "", 0 , '/');
		}
	}

	function checkAutoLogin()
	{
		$cid = $_COOKIE[$this->autoLoginParamUid];
		$key = $_COOKIE[$this->autoLoginParamKey];

		if(strlen($key) > 5)
		{
			$this->db->que = "select * from company where cid='". $cid. "'";
			$this->db->query();

			if($this->db->affected_rows() > 0)
			{
				$row = $this->db->getRow();
				if(strcmp($key, $row["autoLoginKey"]) == false)
				{
					session_start();
					$_SESSION["OMember_id"] = $cid;
					$_SESSION["OMember_seq"] = $row["seq"];
					$_SESSION["OMember_ikey"] = $row["ikey"];
					return true;
				}
			}
		}
		
		return false;
	}


	function logout()
	{
		session_start();
		$_SESSION["OMember_id"] = "";
		$_SESSION["OMember_seq"] = "";
		$_SESSION["OMember_ikey"] = "";
		setcookie($this->autoLoginParamUid, "", 0 , '/');
		setcookie($this->autoLoginParamKey, "", 0 , '/');

	}

	function dbClose()
	{
		$this->db->close();
	}
}