<?php 
require_once LIB.'/utilities/Classes/PHPExcel.php';
require_once LIB.'/utilities/Classes/PHPExcel/IOFactory.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$sheet=0;
foreach ($data as $cosid => $msisdn){
	// Create a first sheet, representing sales data
	if ($sheet!=0)
		$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex($sheet);
	//$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Date');
	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Menu');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Feature');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Key');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', 'MSISDN');
	
	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle("cos".$cosid);
	
	//data
	$i=2;
	foreach ($msisdn as $number){
		$objPHPExcel->getActiveSheet()->getCell('D' . $i)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->getStyle('D' . $i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objPHPExcel->getActiveSheet()
		//->setCellValue('A' . $i, $number['date'])
		->setCellValue('A' . $i, $number['menu'])
		->setCellValue('B' . $i, $number['label'])
		->setCellValue('C' . $i, $number['keypress'])
		->setCellValue('D' . $i, $number['msisdn']);
		$i++;
	}
	$sheet++;
	
}

$objPHPExcel->setActiveSheetIndex(0);
$filename = 'vuxa_msisdn_'.$d1.'_'.$d2;

$numRecords = count($msisdn);
if( $numRecords <= 65535){//Excel 5
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename= '$filename.xls'");
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
}
elseif( $numRecords > 65535 && $numRecords < 1048576 ){
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename= '$filename.xlsx'");
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
}
$objWriter->save('php://output');
?>
