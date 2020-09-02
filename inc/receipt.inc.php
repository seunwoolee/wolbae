<?
class Receipt
{
    var $db;
	var $companySeq;


	function Receipt($db, $companySeq)
	{
		$this->db = $db;
		$this->companySeq = $companySeq;
	}

	static function getCardText($card)
	{
		if($card == "Personal")
		{
			return "개인";
		}
		else if($card == "Company")
		{
			return "법인";
		}
	}

	static function getCardFullText($card)
	{
		if($card == "Personal")
		{
			return "개인카드 (현금)";
		}
		else if($card == "Company")
		{
			return "회사카드 (법인카드)";
		}
	}

	static function getStateText($state)
	{
		if($state == "N")
		{
			return "미승인";
		}
		else if($state == "Y")
		{
			return "승인";
		}
		else if($state == "X")
		{
			return "반려";
		}
	}


	function setDefaultReceiptInfo($companySeq=0)
	{
		
		if($companySeq == 0)
		{
			$companySeq = $this->companySeq;
		}

		//계정과목
		$db = $this->db;

		$db->que = "insert into receiptAccountCode (companySeq, name, code, sort) values (". $companySeq. ", '여비교통비', '812', 1)";
		$db->query();
		$seq_812 = $db->insert_id();

		$db->que = "insert into receiptAccountCode (companySeq, name, code, sort) values (". $companySeq. ", '차량유지비', '822', 2)";
		$db->query();
		$seq_822 = $db->insert_id();


		$db->que = "insert into receiptAccountCodeDetail (receiptAccountCodeSeq, type, name, sort) values ";
		$db->que .= " (". $seq_822. ", 'Car', '유류비', 100) ";
		$db->que .= ", (". $seq_822. ", 'Car', '통행료', 99) ";
		$db->que .= ", (". $seq_822. ", 'Car', '수선비', 98) ";
		$db->que .= ", (". $seq_822. ", 'Car', '차량유지비-기타', 97) ";
		$db->que .= ", (". $seq_812. ", 'Def', '숙박비', 96) ";
		$db->que .= ", (". $seq_812. ", 'Def', '식대', 95) ";
		$db->que .= ", (". $seq_812. ", 'Def', '출장비-기타', 94) ";
		$db->query();



		//여비교통비 (812)
		//세금과공과금 (817)
		//지급임차료 (819)
		//수선비 (820)
		//보험료 (821)
		//차량유지비 (822)


		//프로젝트
		//$db->que = "insert into receiptProject (companySeq, name, sort) values  (". $companySeq. ", '기본 프로젝트', 1) ";
		//$db->query();
	}

	
	var $imageUrl;
	var $thumbUrl;
	var $thumbWidth;
	var $thumbHeight;
	
	var $maxImageSize = 1500;
	var $thumbImageSize = 500;
	static function removeImage($webPath)
	{
		if(empty($webPath) == false)
		{
			$fullPath = _DATA_DIR. $webPath;
			
			if(file_exists($fullPath))
			{
				@unlink($fullPath);
			}
		}
	}

	function uploadImage($image, $seq)
	{
		$imageName = $seq. "_". uniqid();
		$uploadPath = "/receipt". date("/Y/m/d");
		$homeDir = _DATA_DIR;
		$dir = $homeDir. $uploadPath;
		if(is_dir($dir) == false)
		{
			mkdir($dir, 0747, true);
		}
		
		
		
		$fileFormat = $this->getFileFormat($image);
		if($fileFormat == "gif")
		{
			$srcImg = ImageCreateFromGIF($image["tmp_name"]); //GIF 파일로부터 이미지를 읽어옵니다
		}
		else if($fileFormat == "jpg")
		{
			$srcImg = ImageCreateFromJPEG($image["tmp_name"]); //JPG파일로부터 이미지를 읽어옵니다
		}
		else if($fileFormat == "png")
		{
			$srcImg = ImageCreateFromPNG($image["tmp_name"]); //PNG 파일로부터 이미지를 읽어옵니다
		}

		$bigWebPath = $uploadPath. "/". $imageName. ".". $fileFormat;
		$bigDocPath = $homeDir. $bigWebPath;

		$thumbWebPath = $uploadPath. "/". $imageName. ".thumb.". $fileFormat;
		$thumbDocPath = $homeDir. $thumbWebPath;







		//이미지 크기 관련 처리
		$imgInfo = @getImageSize($image["tmp_name"]);
		$imgWidth = $imgInfo[0];
		$imgHeight = $imgInfo[1];

		//LIB::PLog("width:". $imgWidth. ",". $imgHeight);

		$avgSize = ($imgWidth + $imgHeight) / 2;

		$ratio = 1;
		$bigWidth = $imgWidth;
		$bigHeight = $imgHeight;
		$thumbWidth = $imgWidth;
		$thumbHeight = $imgHeight;


		//원본 이미지 리사이징
		if($avgSize > $this->maxImageSize)
		{
			$ratio = $this->maxImageSize / $avgSize;
			$bigWidth = $bigWidth * $ratio;
			$bigHeight = $bigHeight * $ratio;

			
			$dstImg = imagecreatetruecolor($bigWidth, $bigHeight); //타겟이미지를 생성합니다
			ImageCopyResampled($dstImg, $srcImg, 0, 0, 0, 0, $bigWidth, $bigHeight, $imgWidth, $imgHeight);
			ImageInterlace($dstImg);

			if($fileFormat == "gif")
			{
				ImageGIF($dstImg,  $bigDocPath);
			}
			else if($fileFormat == "jpg")
			{
				ImageJPEG($dstImg,  $bigDocPath);
			}
			else if($fileFormat == "png")
			{
				ImagePNG($dstImg,  $bigDocPath);
			}
		}
		//원본 이미지 그대로 업로드
		else
		{
			move_uploaded_file($image["tmp_name"], $bigDocPath);
		}

		//섬네일 이미지 리사이징
		if($avgSize > $this->minImageSize)
		{
			$ratio = $this->thumbImageSize / $avgSize;
			$thumbWidth = $thumbWidth * $ratio;
			$thumbHeight = $thumbHeight * $ratio;

			$dstThumbImg = imagecreatetruecolor($thumbWidth, $thumbHeight); //타겟이미지를 생성합니다
			ImageCopyResampled($dstThumbImg, $srcImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $imgWidth, $imgHeight);
			ImageInterlace($dstThumbImg);

			if($fileFormat == "gif")
			{
				ImageGIF($dstThumbImg,  $thumbDocPath); //실제로 이미지파일을 생성합니다
			}
			else if($fileFormat == "jpg")
			{
				ImageJPEG($dstThumbImg,  $thumbDocPath); //실제로 이미지파일을 생성합니다
			}
			else if($fileFormat == "png")
			{
				ImagePNG($dstThumbImg,  $thumbDocPath); //실제로 이미지파일을 생성합니다
			}
		}
		//섬네일 이미지 원본 사이즈 그대로 copy
		else
		{
			copy($bigDocPath, $thumbDocPath);
		}


		$this->imageUrl = $bigWebPath;
		$this->thumbUrl = $thumbWebPath;
		$this->thumbWidth = $thumbWidth;
		$this->thumbHeight = $thumbHeight;

		//Destroy
		if($srcImg != null)
		{
			ImageDestroy($srcImg);
		}

		if($dstImg != null)
		{
			ImageDestroy($dstImg);
		}

		if($dstThumbImg != null)
		{
			ImageDestroy($dstThumbImg);
		}
	}


	//이미지 포멧
	function getFileFormat($image)
	{
		$size = @getimagesize($image["tmp_name"]);
		$format = $size[2];

		if($format == 1)
		{
			return "gif";
		}
		else if($format == 2)
		{
			return "jpg";
		}
		else if($format == 3)
		{
			return "png";
		}
		else
		{
			return "jpg";
		}
	}


	function getImageUrl()
	{
		return $this->imageUrl;
	}

	function getThumbUrl()
	{
		return $this->thumbUrl;
	}

	function getThumbWidth()
	{
		return $this->thumbWidth;
	}

	function getThumbHeight()
	{
		return $this->thumbHeight;
	}
}
?>
