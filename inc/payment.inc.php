<?
class Payment {
	var $mode = 'test'; //test or service
	var $id = "cartax";
	var $mertkey = '08d6ec917f90b354e10c47a0a97094b6';
	var $host;
	var $osType;

	public static $stateNone = 'None';
	public static $stateApply = 'Apply';
	public static $stateComplete = 'Complete';

	public static $CODE_CARD 								= "SC0010";//신용카드			
	public static $CODE_ACCOUNT_TRANSFER 		= "SC0030";//계좌이체
	public static $CODE_NONE_BANKBOOK_TEMP 	= "SC0999"; //카택스 계좌로 입금 (무통장 입금)
	public static $CODE_ADMIN 							= "SC0998"; //관리자가 추가

	var $db;
	var $companySeq;

	function Payment($db, $companySeq)
	{
		$this->db = $db;
		$this->companySeq = $companySeq;

		
		// 모바일 목록
		 $mobilechk = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS)/i'; 
		 if(preg_match($mobilechk, $_SERVER['HTTP_USER_AGENT'])) {
			$this->osType = "m";
		 } else { 
			$this->osType = "pc";
		 }

		if($_SERVER['HTTP_HOST'] == "cds.jeycorp.com")
		{
			$this->host = "http://cds.jeycorp.com/oil/payment/". $this->osType;
		}
		else
		{
			$this->host = "https://cds.carbeast.co.kr/oil/payment/". $this->osType;
		}
	}


	function setPaymentTemp($type, $licenceQuantity, $term, $beginDate, $endDate, $totalAmount, $discountAmount, $amount, $discount)
	{
		$DATA["type"]						= $type;
		$DATA["companySeq"]			= $this->companySeq;
		$DATA["licenceQuantity"]		= $licenceQuantity;
		$DATA["term"]						= $term;
		$DATA["beginDate"]				= $beginDate;
		$DATA["endDate"]					= $endDate;
		$DATA["totalAmount"]			= $totalAmount;
		$DATA["discountAmount"]		= $discountAmount;
		$DATA["amount"]					= $amount;
		$DATA["discount"]				= $discount;

		$this->db->Insert("paymentTemp", $DATA, "paymentTemp insert error");
		return $this->db->insert_id();
	}

	function setLGD_LOG($LGD_TID, $LGD_MID, $LGD_OID, $LGD_AMOUNT, $LGD_RESPCODE, $LGD_PARAMS)
	{
		$DATA["LGD_TID"]						= $LGD_TID;
		$DATA["LGD_MID"]						= $LGD_MID;
		$DATA["paymentTempSeq"]			= $LGD_OID;
		$DATA["LGD_AMOUNT"]					= $LGD_AMOUNT;
		$DATA["LGD_RESPCODE"]				= $LGD_RESPCODE;
		$DATA["LGD_PARAMS"]					= $LGD_PARAMS;
		$this->db->Insert("LGD_LOG", $DATA, "LGD_LOG insert error");
	}

	function complete($paymentTempSeq)
	{
		$this->db->que = "SELECT * FROM paymentTemp WHERE seq=". $paymentTempSeq;
		$this->db->query();
		$temp = $this->db->getRow();

		if($temp == false)
		{
			return 0;
		}
		else
		{
			$DATA["type"]						= $temp["type"];
			$DATA["companySeq"]			= $temp["companySeq"];
			$DATA["licenceQuantity"]		= $temp["licenceQuantity"];
			$DATA["term"]						= $temp["term"];
			$DATA["beginDate"]				= $temp["beginDate"];
			$DATA["endDate"]					= $temp["endDate"];
			$DATA["totalAmount"]			= $temp["totalAmount"];
			$DATA["discountAmount"]		= $temp["discountAmount"];
			$DATA["amount"]					= $temp["amount"];
			$DATA["discount"]				= $temp["discount"];
			$DATA["state"]						= "Complete";
			$DATA["paymentTempSeq"]	= $paymentTempSeq;
			$this->db->Insert("payment", $DATA, "payment insert error");
			
			if($this->db->insert_id() > 0)
			{
				$seq = $this->db->insert_id();
				$this->setFinally();
				return $seq;
			}
			else
			{
				return 0;
			}
		}
	}

	
	function addNoneBankBook($licenceQuantity, $term, $beginDate, $endDate, $totalAmount, $discountAmount, $amount, $discount)
	{
		$DATA["type"]						= "SC0999";
		$DATA["state"]						= "Apply";
		
		$DATA["companySeq"]			= $this->companySeq;
		$DATA["licenceQuantity"]		= $licenceQuantity;
		$DATA["term"]						= $term;
		$DATA["beginDate"]				= $beginDate;
		$DATA["endDate"]					= $endDate;
		$DATA["totalAmount"]			= $totalAmount;
		$DATA["discountAmount"]		= $discountAmount;
		$DATA["amount"]					= $amount;
		$DATA["discount"]				= $discount;

		$this->db->Insert("payment", $DATA, "payment insert error");
		$seq = $this->db->insert_id();
		return $seq;
	}


	function setFinally()
	{
		$DATA["finally"] = "N";
		$this->db->Update("payment", $DATA, "where companySeq=". $this->companySeq, "payment update error1");

		$this->db->que = "SELECT * FROM payment WHERE companySeq=". $this->companySeq. " ORDER BY endDate DESC LIMIT 1";
		$this->db->query();
		$row = $this->db->getRow();

		if($row != false)
		{
			$DATA["finally"] = "Y";
			$this->db->Update("payment", $DATA, "where seq=". $row["seq"], "payment update error2");
		}
	}

	function getState()
	{
		$this->db->que = "SELECt * FROM payment WHERE companySeq=". $this->companySeq. " and state != 'Cancel' AND beginDate <= '". date("Y-m-d"). "' AND endDate >= '". date("Y-m-d"). "' ORDER BY state DESC LIMIT 1";
		$this->db->query();
		$payment = $this->db->getRow();


		if($payment == false)
		{
			return "None";
		}
		else
		{
			return $payment["state"];
		}
	}

	function getLicenceQuantity()
	{
		$this->db->que = "SELECt * FROM payment WHERE companySeq=". $this->companySeq. " AND state='Complete' AND beginDate <= '". date("Y-m-d"). "' AND endDate >= '". date("Y-m-d"). "' ORDER BY state DESC LIMIT 1";
		$this->db->query();
		$payment = $this->db->getRow();
		if($payment == false)
		{
			return 0;
		}
		else
		{
			return $payment["licenceQuantity"];
		}
	}

	function isOverLicence()
	{

		$this->db->que = "SELECt * FROM payment WHERE companySeq=". $this->companySeq. " AND state='Complete' AND beginDate <= '". date("Y-m-d"). "' AND endDate >= '". date("Y-m-d"). "' ORDER BY state DESC LIMIT 1";
		$this->db->query();
		$payment = $this->db->getRow();
		if($payment == false)
		{
			return true;
		}
		else
		{
			$this->db->que = "SELECT COUNT(*) FROM user WHERE companySeq=". $this->companySeq. " AND enabled='Y'";
			$this->db->query();
			$count = $this->db->getOne();

			if($count > $payment["licenceQuantity"])
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	function getLicenceExpireTime()
	{
		$this->db->que = "SELECt * FROM payment WHERE companySeq=". $this->companySeq. " AND state='Complete' ORDER BY endDate DESC LIMIT 1";
		$this->db->query();
		$payment = $this->db->getRow();
		if($payment == false)
		{
			return 0;
		}
		else
		{
			return strtotime($payment["endDate"]);
		}
	}



	static function getTypeText($type)
	{
		if($type == Payment::$CODE_CARD)
		{
			return "신용카드";
		}
		else if($type == Payment::$CODE_ACCOUNT_TRANSFER)
		{
			return "계좌이체";
		}
		else if($type == Payment::$CODE_NONE_BANKBOOK_TEMP)
		{
			return "무통장 입금";
		}
		else if($type == Payment::$CODE_ADMIN)
		{
			return "무통장 입금";
		}
		else
		{
			return "기타";
		}
	}

}

?>