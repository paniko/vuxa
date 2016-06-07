<?php 
require_once LIB.'/utilities/Classes/PHPExcel.php';
require_once LIB.'/utilities/Classes/PHPExcel/IOFactory.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$sheet=0;
$menuData = $result;

$important = "b94a48";
$warning   = "f89406";
$success   = "468847";

foreach ($events as $cosid => $dataMenu){
	// Create a first sheet, representing sales data
	if ($sheet!=0)
		$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex($sheet);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Menu');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Feature');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Key');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Hits');
	
	// Rename sheet
	if($cosid == 'all')
		$objPHPExcel->getActiveSheet()->setTitle("All");
	else
		$objPHPExcel->getActiveSheet()->setTitle("cos".$cosid);
			
	//data
	$i=2;
	foreach ($menuData['submenu'] as $submenu){	
		foreach ($menuData['menu'] as $menu){
			if($menu['idMenu'] == $submenu['idMenu']){
				$hits=0;
				if($events[$cosid][$submenu['idMenu']][$menu['keypress']] != 0){
					$hits =  $events[$cosid][$submenu['idMenu']][$menu['keypress']];
					if($hits <= $dailyMin){
						$color = $important;
					}
					elseif($hits > $dailyMin && $hits < $dailyMax){
						$color = $warning;
					}
					else{
						$color = $success;
					}
					$objPHPExcel->getActiveSheet()->getStyle('D' . $i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$objPHPExcel->getActiveSheet()->getStyle('D' . $i)->getFill()->getStartColor()->setRGB($color);
				}
				$objPHPExcel->getActiveSheet()
				->setCellValue('A' . $i, $submenu['name'])
				->setCellValue('B' . $i, $menu['label'])
				->setCellValue('C' . $i, $menu['keypress'])
				->setCellValue('D' . $i, $hits);
		
				$i++;
			}
		}
		$i++;
	}
	$sheet++;	
}

$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="vuxa_'.$d1.'_'.$d2.'.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
?>
