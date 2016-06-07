<?php 
// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');
// output the column headings
fputcsv($output, array('Date','MSISDN','Menu','Feature', 'Key' ));

foreach ($msisdn as $number){
	$outRow = array(
			'date'     => $number['date'],
			'msisdn'   => $number['msisdn'],
			'menu'     => $number['menu'],
			'name'     => $number['label'],
			'keypress' => $number['keypress']
	);
	fputcsv($output, $outRow);
}



?>
