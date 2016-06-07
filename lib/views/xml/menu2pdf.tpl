<?php 
require_once(LIB. '/utilities/html2pdf/html2pdf.class.php');
ob_start();
?>
<page>
<html>
<body>
<style type="text/css">
body{
	font-size:10px;
}
.titleMenu{
	background-color:#0088cc;
}
.view{
	text-align:center;
}
.badge-important{
	background-color: #b94a48;
}
.badge-warning{
	background-color: #f89406;
}
.badge-success{
	background-color: #468847;
}
.level{
	background-color: #ccc;
}
</style>
<h3><?= $title; ?></h3>
<table>
	<tr><td>Start Date:</td><td><?= $d1; ?></td></tr>
	<tr><td>End Date:</td><td><?= $d2; ?></td></tr>
	<tr><td>Cos ID:</td><td>
	<?php
		$multicos=false;
		$str="";
		foreach ($cosid as $cos){
			if($cos == "-1"){
				$str = "All";
			}
			else{
				$multicos=true;
				$str.= $cos.",";
			} 
		}
		if($multicos) $str=substr($str, 0, -1);
		echo $str;
		
	?>
	</td></tr>
	<tr><td>Threshold daily min:</td><td><?= $dailyMinThreshold; ?></td></tr>
	<tr><td>Threshold daily max:</td><td><?= $dailyMaxThreshold; ?></td></tr>
</table>
<table>
	<thead>
		<tr>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		

<?php
		$content="";
		$menuData = $result;
		$changeLevel = false;
		$levelPrev = 1;
		$count = 0;
		$links = array();
		//print_r($menuData['submenu']);
		foreach ($menuData['submenu'] as $submenu){
			if($submenu['level'] != $levelPrev || $count==0){
				if($count > 0){
					$content.=  "<tr><td class='level' colspan='3'></td></tr>";
				}
			}
			$content.=  "<tr><th colspan='3' class='titleMenu'>".$submenu['name']."</th></tr>";	
			$content.= "<tr>";
			$content.= "<th>Feature</th>";
			$content.= "<th>Key</th>";
			$content.= "<th>Hits</th>";
			$content.= "</tr>";
			$typeBadge="";
			foreach ($menuData['menu'] as $menu){
				if($menu['idMenu'] == $submenu['idMenu']){
					$content.=  "<tr>";
					$content.=  "<td>".$menu['label']."</td>";
					$content.=  "<td>".$menu['keypress']."</td>";
					$hits = $events[$submenu['idMenu']][$menu['keypress']];
					if($hits != 0){
						if($hits <= $dailyMin){
							$typeBadge="badge-important";
						}
						elseif($hits > $dailyMin && $hits < $dailyMax){
							$typeBadge="badge-warning";
						}
						else{
							$typeBadge="badge-success";
						}					
						$content.=  "<td class='view $typeBadge'>";
						$content.=  "<span class='badge'>".$hits."</span>";
					}
					else{
						$content.=  "<td class='view'>";
					}
					$content.= "</td>";
					$content.= "</tr>";
				}
			}
			$levelPrev = $submenu['level'];
			$count++;			
		}
		echo $content;
?>
	</tbody>
</table>

</body>
</html>
</page>
<?php 
$html = ob_get_contents();
ob_end_clean(); 		
		$html2pdf = new HTML2PDF('P','A4','it');
		$html2pdf->WriteHTML($html);
		$html2pdf->Output('menu_overview.pdf', 'D');		
		
		
?>
