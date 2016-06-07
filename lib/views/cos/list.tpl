<option value='-1'>All</option>
<?php 
	foreach ($listCos as $item){
		echo "<option value='".$item['cosid']."'>".$item['cosid']."</option>";
	}
?>