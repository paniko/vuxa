<?php 
// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('Feature', 'Key' ,'Hits'));

		$content="";
		$menuData = $result;
		$changeLevel = false;
		$levelPrev = 1;
		$count = 0;
		$links = array();
		//print_r($menuData['submenu']);
		foreach ($menuData['submenu'] as $submenu){
			$outRow = array(
					'name'     => $submenu['name'],
					'keypress' => '',
					'hits'     => ''
			);
			fputcsv($output, $outRow);			
			foreach ($menuData['menu'] as $menu){
				$hits=0;
				if($events[$submenu['idMenu']][$menu['keypress']]!=0){
					$hits =  $events[$submenu['idMenu']][$menu['keypress']];
				}
				$outRow = array(
						'name'     => $menu['label'],
						'keypress' => $menu['keypress'],
						'hits'     => $hits				
				);
				fputcsv($output, $outRow);		
			}	
			
		}
?>
