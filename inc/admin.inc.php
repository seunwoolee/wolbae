<?

class Admin extends DB_MySQL
{
	var $DATA;						//$_POST 로 넘어온 파라메터
	var $db;							//mysql Class

	var $autoLoginParamUid = "Cds_cid";
	var $autoLoginParamKey = "Cds_key";


	function Admin($DATA)
	{
		$this->DATA = $DATA;
		$this->db = new DB_MySQL(_DBHOST , _DBID, _DBPASS, _DBNAME);
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
					$_SESSION["Member_id"] = $cid;
					$_SESSION["Member_seq"] = $row["seq"];
					return true;
				}
			}
		}
		
		return false;
	}


	function logout()
	{
		session_start();
		$_SESSION["Member_id"] = "";
		$_SESSION["Member_seq"] = "";
		setcookie($this->autoLoginParamUid, "", 0 , '/');
		setcookie($this->autoLoginParamKey, "", 0 , '/');

	}

	function dbClose()
	{
		$this->db->close();
	}
}