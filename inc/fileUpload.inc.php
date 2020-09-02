<?




class FileUpload
{
	var $attFile;
	var $uploadPath;
	var $homeDir;
	var $fileFormat;

	function FileUpload($attFile, $path)
	{
		$this->attFile = $attFile;
		$this->uploadPath = $path. date("/Y/m/d");
		$this->homeDir = $_SERVER["DOCUMENT_ROOT"];
		
		$dir = $this->homeDir. $this->uploadPath;
		if(is_dir($dir) == false)
		{
			mkdir($dir, 0747, true);
		}

		$this->extractFileFormat();
	}


	function fileTypeCheck($types)
	{
		$types = strtolower($types);
		$typeArr = explode("|", $types);

		if(in_array($this->getFileFormat(), $typeArr) == true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	//확장자
	function getFileType()
	{
		$ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $this->attFile["name"]);
		return $ext;
	}


	//이미지 포멧
	function extractFileFormat()
	{
		$size = @getimagesize($this->attFile["tmp_name"]);
		$format = $size[2];

		if($format == 1)
		{
			$this->fileFormat = "gif";
		}
		else if($format == 2)
		{
			$this->fileFormat = "jpg";
		}
		else if($format == 3)
		{
			$this->fileFormat = "png";
		}
		else
		{
			$this->fileFormat = "";
		}
	}


	function upload($uploadName)
	{
		$webPath = $this->uploadPath. "/". $uploadName. ".". $this->fileFormat;
		$docPath = $this->homeDir. $webPath;
		if (move_uploaded_file($this->attFile["tmp_name"], $docPath))
		{
			return $webPath;
		}
		else
		{
			return "";
		}
	}


	function deleteFile($webPath)
	{
		if(empty($webPath) == false)
		{
			$root = $_SERVER["DOCUMENT_ROOT"];
			$fullPath = $root. $webPath;
			
			if(file_exists($fullPath))
			{
				@unlink($fullPath);
			}
		}
	}


	function createThumbnail($uploadName, $width, $height)
	{
		$fileFormat = $this->fileFormat;
		$webPath = $this->uploadPath. "/". $uploadName. ".". $fileFormat;
		$docPath = $this->homeDir. $webPath;

		$webThumbPath = $this->uploadPath. "/". $uploadName. ".thumb.". $fileFormat;
		$docThumbPath = $this->homeDir. $webThumbPath;

		if($fileFormat == "gif")
		{
			$srcImg = ImageCreateFromGIF($docPath); //GIF 파일로부터 이미지를 읽어옵니다
		}
		else if($fileFormat == "jpg")
		{
			$srcImg = ImageCreateFromJPEG($docPath); //JPG파일로부터 이미지를 읽어옵니다
		}
		else if($fileFormat == "png")
		{
			$srcImg = ImageCreateFromPNG($docPath); //PNG 파일로부터 이미지를 읽어옵니다
		}

		

		$imgInfo = @getImageSize($docPath);//원본이미지의 정보를 얻어옵니다
		$imgWidth = $imgInfo[0];
		$imgHeight = $imgInfo[1];

		/*$x = (int) (($imgWidth - $imgHeight) / 2);
		$y = (int) (($imgHeight - $imgWidth) / 2);

		if($x < 1)
		{
			$x = 0;
			$targetSize = $imgWidth;
		}

		if($y < 1)
		{
			$y = 0;
			$targetSize = $imgHeight;
		}
		
		//높이가 너비의 2배 이상이면 중간지점 찾지 않고 좌측 꼭지점 부터 자름
		if($imgHeight > ($imgWidth * 2))
		{
			$y = 0;
		}*/

		$dstImg = imagecreatetruecolor($width, $height); //타겟이미지를 생성합니다
		ImageCopyResampled($dstImg, $srcImg, 0, 0, 0, 0, $width, $height, $imgWidth, $imgHeight); //ImageCopyResized 보다 높은 퀄리티
		ImageInterlace($dstImg);

		if($fileFormat == "gif")
		{
			ImageGIF($dstImg,  $docThumbPath); //실제로 이미지파일을 생성합니다
		}
		else if($fileFormat == "jpg")
		{
			ImageJPEG($dstImg,  $docThumbPath); //실제로 이미지파일을 생성합니다
		}
		else if($fileFormat == "png")
		{
			ImagePNG($dstImg,  $docThumbPath); //실제로 이미지파일을 생성합니다
		}

		
		ImageDestroy($dstImg);
		ImageDestroy($srcImg);

		return $webThumbPath;
	}
}
