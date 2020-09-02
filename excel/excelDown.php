<?
require_once '../excel/Classes/PHPExcel.php';
include "../inc/config.php";
include "../inc/lib.inc.php";
include "../inc/mysql.inc.php";


$deliveryDate				= $_GET["deliveryDate"];
$locationId					= $_GET["locationId"];
$meridiemType				= $_GET["meridiemType"];
$meridiemFlag				= $_GET["meridiemFlag"];

$ci_guestId			=	$_SESSION["OMember_id"];

$objPHPExcel = new PHPExcel();



$db = new Mysql();
$db2 = new Mysql();

$sheetNum = 0;
$db->que = "SELECT vr_vehicleNo AS vehicleNo 
					FROM vehicleAllocateResult 
					WHERE 1=1
						AND vr_deliveryDate='".$deliveryDate."' 
						AND vr_locationId='".$locationId."' 
						AND vr_meridiemType='".$meridiemType."' 
						AND vr_meridiemFlag='".$meridiemFlag."' 
					GROUP BY vr_vehicleNo 
					ORDER BY vr_vehicleNo*1 ASC";
$db->query();


while($row = $db->getRow()) {
	
	$number = $row["vehicleNo"];
	$sheetIndex = $objPHPExcel->createSheet($sheetNum);
	$sheetNum++;

	//-------------------------------------------------------------------------------
	//너비 설정
	$sheetIndex->getColumnDimension('A')->setWidth('10');
	$sheetIndex->getColumnDimension('B')->setWidth('20');
	$sheetIndex->getColumnDimension('C')->setWidth('15');
	$sheetIndex->getColumnDimension('D')->setWidth('8');
	$sheetIndex->getColumnDimension('E')->setWidth('12');
	$sheetIndex->getColumnDimension('F')->setWidth('50');
	$sheetIndex->getColumnDimension('G')->setWidth('15');
	$sheetIndex->getColumnDimension('H')->setWidth('12');
	$sheetIndex->getColumnDimension('I')->setWidth('12');
	$sheetIndex->getColumnDimension('J')->setWidth('12');
	$sheetIndex->getColumnDimension('K')->setWidth('12');
	$sheetIndex->getColumnDimension('L')->setWidth('12');



	//-------------------------------------------------------------------------------
	//높이 설정
	$sheetIndex->getRowDimension('1')->setRowHeight(22);
	$sheetIndex->getRowDimension('2')->setRowHeight(22);
	$sheetIndex->getRowDimension('3')->setRowHeight(22);
	$sheetIndex->getRowDimension('4')->setRowHeight(22);
	$sheetIndex->getRowDimension('5')->setRowHeight(22);
	$sheetIndex->getRowDimension('6')->setRowHeight(22);
	$sheetIndex->getRowDimension('7')->setRowHeight(22);
	$sheetIndex->getRowDimension('8')->setRowHeight(22);
	$sheetIndex->getRowDimension('9')->setRowHeight(22);
	$sheetIndex->getRowDimension('10')->setRowHeight(22);
	$sheetIndex->getRowDimension('11')->setRowHeight(22);
	$sheetIndex->getRowDimension('12')->setRowHeight(22);



	//-------------------------------------------------------------------------------
	//타이틀 속성  
	$sheetIndex->getStyle("A1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 번호
	$sheetIndex->getStyle("B1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 주문번호
	$sheetIndex->getStyle("C1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 주문금액
	$sheetIndex->getStyle("C1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);					// 주문금액
	$sheetIndex->getStyle("D1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 배차번호
	$sheetIndex->getStyle("E1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 배차순서
	$sheetIndex->getStyle("F1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 고객명
	$sheetIndex->getStyle("G1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 도착주소
	$sheetIndex->getStyle("H1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 고객전화번호
	$sheetIndex->getStyle("I1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 거리
	$sheetIndex->getStyle("J1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 배송날짜
	$sheetIndex->getStyle("K1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 오전/오후
	$sheetIndex->getStyle("L1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 오전/오후 플래그
	$sheetIndex->getStyle("M1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F2F2F2');	// 추가배송


	$sheetIndex->setCellValue('A1', "번호");
	$sheetIndex->setCellValue('B1', "주문번호");
	$sheetIndex->setCellValue('C1', "주문금액(원)");
	$sheetIndex->setCellValue('D1', "배차번호");
	$sheetIndex->setCellValue('E1', "배차순서");
	$sheetIndex->setCellValue('F1', "고객명");
	$sheetIndex->setCellValue('G1', "도착주소");
	$sheetIndex->setCellValue('H1', "고객전화번호");
	$sheetIndex->setCellValue('I1', "거리");
	$sheetIndex->setCellValue('J1', "배송날짜");
	$sheetIndex->setCellValue('K1', "오전/오후");
	$sheetIndex->setCellValue('L1', "플래그");
	$sheetIndex->setCellValue('M1', "추가배송");

	$num = 1;
	$index = 2;

	$db2->que = "SELECT	vr_vehicleNo		AS vehicleNo
					,vr_vehicleNoIndex		AS vehicleNoIndex 
					,vr_deguestAccno		AS accno 
					,vr_deguestId			AS deguestId 
					,vr_guestLat			AS guestLat 
					,vr_guestLon			AS guestLon 
					,vr_deguestLat			AS deguestLat 
					,vr_deguestLon			AS deguestLon 
					,vr_deguestName			AS deguestName
					,vr_Juso				AS Juso 
					,vr_deguestTel			AS deguestTel
					,vr_distanceValue		AS distanceValue 
					,vr_deliveryDate		AS deliveryDate 
					,vr_meridiemType		AS meridiemType 
					,vr_meridiemFlag		AS meridiemFlag 
					,vr_delivererId			AS deliveryId 
					,vr_deliveryStatus		AS deliveryStatus
					,vr_deliveryEndDate		AS deliveryEndDate
					,vr_deguestPay			AS deguestPay
					,vr_accnoDupleJuso		AS accnoDupleJuso
					,vr_errorJusoFlag		AS errorJusoFlag
						FROM vehicleAllocateResult 
							WHERE 1=1
								AND vr_deliveryDate='".$deliveryDate."' 
								AND vr_locationId='".$locationId."' 
								AND vr_meridiemType='".$meridiemType."' 
								AND vr_meridiemFlag='".$meridiemFlag."' 
								AND vr_vehicleNo='".$row["vehicleNo"]."' 
								AND vr_deguestId!='".$ci_guestId."'  
								ORDER BY vr_vehicleNoIndex ";

	$db2->query();

	$accnoDupleJuso = '';
	$accnoDupleJusoCopy = '';
	$accnoDupleCnt = 0;

	$deguestId = '';
	$deguestIdCopy = '';
	
	while($row2 = $db2->getRow()) {

		$strAccnoDupleFlag	= '';	// 주소 중첩지역 메시지처리
		$strErrorJusoFlag	= '';	// 주소 오류지역 메시지처리(배송경로오류건, 배송경로추가건 포함)

		$meridiem ="오전";
		$deliveryEndDate = $row2["deliveryEndDate"];
		$addDelivery = "";
		if($row2["meridiemType"]=='1'){
			$meridiem = "오후";
		}

		if($deliveryEndDate=='1900-01-01 00:00:00'){
			$deliveryEndDate = "";
		}
		
		if($row2["guestLat"]==0 && $row2["guestLon"]==0){
			$addDelivery = "추가배송";
		}
		$distance = $row2["distanceValue"];

		if($distance > 999){
			$distance = sprintf("%2.1f",$distance*0.001)."km";
		} else {
			$distance = $distance."m";
		}

		$sheetIndex->getStyle("B".$index)->getNumberFormat()->setFormatCode('0');

		//if($row2['accnoDupleJuso']){
		//	$accnoDupleFlg = " (중첩)";
		//} else {
		//	$accnoDupleFlg = "";
		//}

		$accnoDupleJuso = $row2['accnoDupleJuso'];
		$deguestId = $row2['deguestId'];
		if($accnoDupleJuso != '' && ($accnoDupleJusoCopy == $accnoDupleJuso) && ($deguestIdCopy != $deguestId)){
			//$accnoDupleCnt++;
			$strAccnoDupleFlag = ' (중첩)';
			$num--;
		} else {
			$accnoDupleJusoCopy = $accnoDupleJuso;
			$deguestIdCopy = $deguestId;
		}

		if($row2['errorJusoFlag'] == 'Y'){
			$strErrorJusoFlag = '(배송경로추가건)';
		}

		$sheetIndex->setCellValue('A'.$index, $num.$strAccnoDupleFlag.$strErrorJusoFlag);
		$sheetIndex->setCellValue('B'.$index, $row2['accno']);
		$sheetIndex->setCellValue('C'.$index, number_format($row2['deguestPay']));
		$sheetIndex->setCellValue('D'.$index, (int)($row2['vehicleNo'])+1);
		$sheetIndex->setCellValue('E'.$index, (int)($row2['vehicleNoIndex'])+1);
		$sheetIndex->setCellValue('F'.$index, $row2['deguestName']);
		$sheetIndex->setCellValue('G'.$index, $row2['Juso']);
		$sheetIndex->setCellValue('H'.$index, $row2['deguestTel']);
		$sheetIndex->setCellValue('I'.$index, $distance);
		$sheetIndex->setCellValue('J'.$index, $row2['deliveryDate']);
		$sheetIndex->setCellValue('K'.$index, $meridiem);
		$sheetIndex->setCellValue('L'.$index, $meridiemFlag."번");
		$sheetIndex->setCellValue('M'.$index, $addDelivery);

		$num++;
		$index++;

	}  
	$numberSheet = (string)((int)($number)+1);
	$sheet      = $objPHPExcel->getActiveSheet();
	$sheet->getDefaultStyle()->getFont()->setName('맑은 고딕');
	$sheet->setTitle("배차번호".$numberSheet);

}
$objPHPExcel->setActiveSheetIndex(0);
$date = date("Ymd_His");
$filename = iconv("utf-8","euc-kr", "vehicle_".$date.".xls");

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename='. $filename);
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');


 
?>  