<?

/**
*
*
*		@package MySQL DB Interface Class
*/

class Mysql
{

    var $HostName = "";		// 특별한 이유없는한 localhost
    var $UserName = "";		// User ID
    var $UserPass = "";			// User Password
    var $DBName = "";			// Database Name


	var $Conn="";					// Database Handler
	var $result="";					// result
	var $row="";
	var $que="";

	var $Auto_Free	= 0;     ## Set to 1 for automatic mysql_free_result()
	var $Debug			= 0;     ## Set to 1 for debugging messages.

	/**
	* Function : 생성자
	* Input : Database Connection Information
	* Output : None
	*/
	function Mysql($HostName="", $UserName="", $UserPass="", $DBName="")
	{
		//$this->Language = LANGUAGE;
		if(empty($HostName))
		{
			$this->HostName=_DBHOST;
			$this->UserName=_DBID;
			$this->UserPass=_DBPASS;
			$this->DBName=_DBNAME;
		}
		else
		{
			$this->HostName=$HostName;
			$this->UserName=$UserName;
			$this->UserPass=$UserPass;
			$this->DBName=$DBName;
		}
		

		$this->connect();
	}


	/*
	** Function : Connect
	** Input : None
	** Output : None
	*/
	function connect()
	{
		$this->Conn=mysql_connect($this->HostName, $this->UserName, $this->UserPass, true);
		if (!$this->Conn) //연결실패
		{
			$this->errMsg("Database Connection Error, DB 계정과 암호를 체크하세요.", "");
		}
		else //연결성공
		{
			if (!(mysql_select_db($this->DBName))) //Database 선택 실패
				$this->errMsg("Database Select Error, Database 이름을 체크하세요.", "");
		}
	}


	/*
	** Function : nQuery
	** Input : Query String, Error Message
	** Output : Recordset
	** Descript : mysql_query()
	*/
	function Query($msg="")
	{
		if (!($this->result=@mysql_query($this->que, $this->Conn)))
		{
			$this->errMsg($msg);
		} else {
			if ($this->Debug == 1) $this->PrintQue($msg);
		}
		return $this->result;
	}


	/*
	** Function : rQuery
	** Input : Query string, Error message
	** Output : Record
	** Descript : mysql_result()
	*/
	function get_one($msg="") {
		$this->Query($msg);
		if(mysql_num_rows($this->result)):
			return mysql_result($this->result, 0, 0);
		else:
			return 0;
		endif;
	}

	function numRows(){
		return mysql_num_rows($this->result);
	}

	// $data[n]형태 1차 배열데이터 입력
	function InsertDB($table, $data, $msg="")
	{
		if(!is_array($data))
			$this->errMsg("입력데이터에 오류가 있습니다.", "$data가 배열이 아닙니다.");

		$i=0;
		foreach ($data as $field => $value)
		{
			if (0 < $i) {						// field와 value가 2개 이상일 경우 , 자동 입력
				$field_que.=",";
				$value_que.=",";
			}
			if(eregi("^_", $field))			// field 이름이 _ 로 시작할경우에는 field에 들어가는 값을 함수로 가정 _ 를 때어내고, 따옴표를 넣지 않는다.
			{
				$field = substr($field, 1);
				$quot = "";
			} else {
				$quot = "'";
			}
			$field_que.=$field;							// field에 해당하는 쿼리
			$value_que.=$quot.$value.$quot;		// value에 해당하는 쿼리
			$i++;
		}
		$msg = "[ <i>$table</i> ] Table에 Data 입력 : $msg";
		$this->que = "insert into $table ($field_que) values ($value_que)";

		$this->query($msg);
	}

	// $data[n]형태 2차 배열데이터 입력
	function InsertsDB($table, $data, $msg="")
	{
		if(!is_array($data))
			$this->errMsg("입력데이터에 오류가 있습니다.", "$data가 배열이 아닙니다.");

		$dataCount = count($data);

		for($j=0;$j<$dataCount;$j++){

			$i=0;
			$field_que = "";
			$value_que = "";
			foreach ($data[$j] as $field => $value)
			{
				if (0 < $i) {						// field와 value가 2개 이상일 경우 , 자동 입력
					$field_que.=",";
					$value_que.=",";
				}
				if(eregi("^_", $field))			// field 이름이 _ 로 시작할경우에는 field에 들어가는 값을 함수로 가정 _ 를 때어내고, 따옴표를 넣지 않는다.
				{
					$field = substr($field, 1);
					$quot = "";
				} else {
					$quot = "'";
				}
				$field_que.=$field;							// field에 해당하는 쿼리
				$value_que.=$quot.$value.$quot;		// value에 해당하는 쿼리
				$i++;
			}
			$msg = "[ <i>$table</i> ] Table에 Data 입력 : $msg";
			$this->que = "insert into $table ($field_que) values ($value_que)";

			$this->query($msg);
		}
	}


	#
	#	Insert DB
	#
	function Insert($table, $data, $msg="")
	{
		$this->InsertDB($table, $data, $msg);
	}

	function Inserts($table, $data, $msg="")
	{
		$this->InsertsDB($table, $data, $msg);
	}


	function UpdateDB($table, $data, $where="", $msg="")
	{

		if(!is_array($data))
			$this->errMsg("입력데이터에 오류가 있습니다.", "$data가 배열이 아닙니다.");

		$i=0;
		foreach ($data as $field => $value) {
			if (0 < $i) {						// field와 value가 2개 이상일 경우 , 자동 입력
				$sub_que.=",";
			}
			if(eregi("^_", $field))			// field 이름이 _ 로 시작할경우에는 field에 들어가는 값을 함수로 가정 _ 를 때어내고, 따옴표를 넣지 않는다.
			{
				$field = substr($field, 1);
				$quot = "";
			} else {
				$quot = "'";
			}
			$sub_que.=$field."=".$quot.$value.$quot; // 서브 쿼리 생성
			$i++;
		}

		if($where) {
			if(!eregi("^where", trim($where))) $where = "where " . $where;
			$sub_que.=" ".$where; // where 쿼리가 존재할 경우 입력
		}
		$msg = "[ <i>$table</i> ] Table에 Data 수정 : $msg";
		$this->que = "update $table set $sub_que";

		//echo $this->que."<br>";

		$this->query($msg);
	}

	#
	#	UpdateDB
	#
	function Update($table, $data, $where="", $msg="")
	{
		$this->UpdateDB($table, $data, $where, $msg);
	}

	#
	#	데이터 삭제
	#
	function DeleteDB($table, $where, $msg)
	{
		if($where) {
			if(!eregi("^where", trim($where))) $where = "where " . $where;
			$sub_que.=" ".$where; // where 쿼리가 존재할 경우 입력
		}
		$this->que = "delete from $table $sub_que";
		$msg = "[ <i>$table</i> ] Table에 Data 삭제 : $msg";

		$this->Query($msg);
	}


	#
	#	DeleteDB
	#
	function Delete($table, $where, $msg)
	{
		$this->DeleteDB($table, $where, $msg);
	}

	#
	#	한개 값을 구함
	#
	function getOne()
	{
		$value = @mysql_result($this->result, 0, 0);
		if ($this->Auto_Free) {
		  $this->free();
		}

		return $value;
	}

	#
	#	결과 값을 구함
	#
	function getRow()
	{
		$this->row = @mysql_fetch_array($this->result, MYSQL_ASSOC);

		$stat = is_array($this->row);
		if (!$stat && $this->Auto_Free) {
		  $this->free();
		}

		return $this->row;
	}

	function getRows()
	{
		$i=0;
		while($row = $this->getRow())
		{
			$rows[$i] = $row;
			$i++;
		}

		return $rows;
	}


	function affected_rows()
	{
		return mysql_affected_rows();
	}

	function insert_id()
	{
		return @mysql_insert_id ($this->Conn);
	}



	/*
	** Function : CLOSE
	** Input : None
	** Output : None
	** Descript : mysql_close()
	*/
	function close()
	{
		if($this->Conn)
		{
			mysql_close($this->Conn);
			$this->destroy();
		}
	}


	function destroy()
	{
		unset($this->Conn); unset($this->UserName);
		unset($this->UserPass); unset($this->HostName);
		unset($this->DBName);
	}


	//Print Error Message and Exit
	function errMsg($msg)
	{
		$mesg="<p><b>DB Error Message !!</b></p>";
		$mesg.=mysql_errno()." : ".mysql_error()."<p><b>User Message</b> :<br> ".$msg."<p><b>Query String</b> :<br> ".$this->que;

		$this->PrintMsg($mesg);
		exit;
	}

	function PrintQue($msg)
	{
		$mesg = "<p><b>Query String</b> : $msg<br> ".$this->que;
		$this->PrintMsg($mesg);
	}

	function PrintMsg($msg) {
		echo $msg;
	}

}


?>