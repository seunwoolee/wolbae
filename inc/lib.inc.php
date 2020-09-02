<?
class LIB {


	static function PLog($log)
	{
		if(is_array($log))
		{
			$logs = "Array { \r\n";
			foreach($log as $key => $value)
			{
				$logs .= $key. " : ". $value. "\r\n";
			}

			$logs .= "}";
			$log = $logs;
		}

		$file_path = $_SERVER['DOCUMENT_ROOT']. "/jangbogo/log.txt";
		$php_filename = $_SERVER['PHP_SELF'];
		
		$log_file = fopen($file_path, "a");  
		fwrite($log_file, date("Y-m-d H:i:s"). " ". $php_filename. " ==> ". $log."\r\n");  
		fclose($log_file);  
	}


	static function getHashPassword($password)
	{
		$hashSha256 = hash("sha256", trim($password), true);
		$hashPassword = base64_encode($hashSha256);
		return trim($hashPassword);
	}


	static function ellipsize($text, $length)
	{
		$strLen = mb_strlen($text, 'UTF-8');
		if($length > $strLen)
		{
			return $text;
		}
		else
		{
			$text = mb_substr($text, 0, $length, 'UTF-8');
			return $text. "···";
		}
	}



	static function Alert($msg, $go)
	{
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<script language='javascript'>";
		
		if($msg == "")
		{
			// 메세지 없음
		}
		else
		{
			echo "alert('". $msg. "');";
		}


		if($go == '-1')
		{
			echo "history.go(-1);";
		}
		else if($go == "")
		{
			// 이동 없음
		}

		else if($go == 'close')
		{
			echo "window.close();";
		}

		//iframe 에서 부모 윈도우 리로드
		else if($go == 'parentReload')
		{
			echo "parent.location.reload();";
		}

		else if($go == 'openerReload')
		{
			echo "opener.location.reload();";
		}

		//하이퍼링크
		else
		{
			echo "window.location.replace('". $go. "');";
		}

		echo "</script>";
	}
	
	

	static function isChecked($text1, $text2) {
		if(strcmp($text1, $text2) == true)
		{
			return "";
		}
		else
		{
			return "checked";
		}
	}

	static function isSelected($text1, $text2) {
		if(strcmp($text1, $text2) == true)
		{
			return "";
		}
		else
		{
			return "selected";
		}
	}

	static function selectBoxYear($selected)
	{
		$begin = 2015;
		$end = date("Y");

		for($i=$end; $i>=$begin; $i--)
		{
			if($selected == $i)
			{
				$options .= "<option value='". $i. "' selected>". $i. "년</option>";
			}
			else
			{
				$options .= "<option value='". $i. "'>". $i. "년</option>";
			}
		}

		return $options;
	}

	static function selectBoxMonth($selected)
	{
		for($i=1; $i<=12; $i++)
		{
			$month = str_pad($i, 2, "0", STR_PAD_LEFT );
			if($selected == $month)
			{
				$options .= "<option value='". $month. "' selected>". $month. "월</option>";
			}
			else
			{
				$options .= "<option value='". $month. "'>". $month. "월</option>";
			}
		}

		return $options;
	}

	static function getOsType()
	{
		$iPod = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
		$iPhone = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
		$iPad = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
		$Android = stripos($_SERVER['HTTP_USER_AGENT'], "Android");

		if($iPod != false || $iPhone != false || $iPad != false)
		{
			return "iOS";
		}
		else if($Android  != false)
		{
			return "Android";
		}
		else
		{
			return "ETC";
		}
	}


	static function startsWith($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}

	static function endsWith($haystack, $needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}

	static function inFileType($types, $fileName)
	{
		$types = strtolower($types);
		$fileName = strtolower($fileName);
		$tmp = explode(".", $fileName);
		$ext = array_pop($tmp);

		if(@ereg($ext, $types))
		{ 
			return true;
		}
		else
		{
			return false;
		}
	}

	static function getFileType($fileName)
	{
		$tmp = explode(".", $fileName);
		return array_pop($tmp);
	}


	static function randomString($length = 1)
	{
		$str = "";
		//$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
		$characters = array_merge(range('A','Z'));
		$max = count($characters) - 1;
		for ($i = 0; $i < $length; $i++)
		{
			$rand = mt_rand(0, $max);
			$str .= $characters[$rand];
		}

		return $str;
	}



	// 날짜 select box 만들기
	static function selectDate($level, $name, $option, $value)
	{
		if($level == 'Y')
		{
			$start = date('Y') - 10;
			$end = date('Y');
		}
		else if($level == 'm')
		{
			$start = 1;
			$end = 12;
		}
		else
		{
			$start = 1;
			$end = 31;
		}

		for($i=$start; $i<=$end; $i++)
		{
			if($i < 10)
			{
				$date = "0". $i;
			}
			else
			{
				$date = $i;
			}
			
			if($date == $value)
			{
				$selected = "selected";
			}
			else
			{
				$selected = "";
			}


			$OPTIONS .= "
				<option value='$date' $selected>$date</option>";
		}

		$selectBox = "
			<select name='$name' id='$name' $option>". $OPTIONS. "</select>";

		return $selectBox;

	}	
}

?>