<?php
    require_once '../Classes/PHPExcel.php';
    $objPHPExcel = new PHPExcel();

    $sheet      = $objPHPExcel->getActiveSheet();

	//$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn('A')->setAutoSize(false);
	

    // 글꼴
    $sheet->getDefaultStyle()->getFont()->setName('맑은 고딕');

    $sheetIndex = $objPHPExcel->setActiveSheetIndex(0);

	$sheetIndex->getColumnDimension('A')->setWidth('50');
	$sheetIndex->getColumnDimension('B')->setWidth('30');
	$sheetIndex->getColumnDimension('C')->setWidth('10');

	$sheetIndex->getRowDimension('1')->setRowHeight(50);
	$sheetIndex->getRowDimension('2')->setRowHeight(22);
	$sheetIndex->getRowDimension('3')->setRowHeight(22);

    // 제목
    $sheetIndex->setCellValue('A1','제 목');
    $sheetIndex->mergeCells('A1:D1');
    $sheetIndex->getStyle('A1')->getFont()->setSize(20)->setBold(true);
    $sheetIndex->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheetIndex->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$defaultBorder = array(
	'style' => PHPExcel_Style_Border::BORDER_THIN,
	'color' => array('rgb'=>'000000')
	);

	$headBorder = array(
	'borders' => array(
	'bottom' => $defaultBorder,
	'left'   => $defaultBorder,
	'top'    => $defaultBorder,
	'right'  => $defaultBorder
	)
	);

	$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn('C')->setWidth('100');

	//$sheetIndex->getStyle("A1:D1")->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	foreach(range('A','F') as $i => $cell){
		for($i=1; $i<5; $i++)
		{
			$sheetIndex->getStyle($cell.$i)->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		}
	}

    // 내용
    $sheetIndex ->setCellValue('A2', '하나')
                ->setCellValue('B2', '둘')
                ->setCellValue('C2', '셋')
                ->setCellValue('D2', '넷');

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=sample.xls');
    header('Cache-Control: max-age=0');
 
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
 
    exit;
?>