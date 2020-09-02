<?
header("Content-Type:text/html;charset=UTF-8");
//###################################################
// 로그인
// 2016.04.14
//###################################################


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Include
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/json.inc.php";
include "../inc/mysql.inc.php";
include "../inc/company.inc.php";


//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Function
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
function asciiCheck($str)
{
	$chk = false;

	for($i=0; $i<strlen($str); $i++)
	{
		$char = substr($str, $i, 1);
		$asciiCode = ord($char);


		if($asciiCode >= 48 && $asciiCode <= 57)			//숫자
		{
			continue;
		} 
		else if($asciiCode >= 65 && $asciiCode <= 90)		//대문자
		{
			continue;
		}
		else if($asciiCode >= 97 && $asciiCode <= 122)	 //소문자
		{
			continue;
		}
		else if($asciiCode == 45)										// 바(-)
		{
			continue;
		}
		else if($asciiCode == 95)										// 언더바(_)
		{
			continue;
		}
		else
		{
			return false;
		}
	}

	return true;
}

//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
// Class
//- - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - - + - -
$company = new Company($_POST);


//Json Class
$json = new Json();
$enabled = $company->getEnabled();

if(asciiCheck($_POST["cid"]) == false)
{
	$json->add("message", "<span style='color:#ff3b27'>영문, 숫자, 특수문자( - _ ) 외 사용이 불가능 합니다.</span>");
}
else
{
	if(empty($enabled))
	{
		$json->add("message", "<span style='color:#1c69ea'>". $_POST["cid"]. " 는(은) 사용이 가능한 아이디 입니다.</span>");
	} 
	else
	{
		$json->add("message", "<span style='color:#ff3b27'>". $_POST["cid"]. " 는(은) 사용할 수 없는 아이디 입니다.</span>");
	}
}


echo $json->getResult();
$company->dbClose();
exit;
?>
