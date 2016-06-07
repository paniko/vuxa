<?php include LIB.'/views/head.php' ?>
<div
	class="container-fluid">
	<div class="masthead">
		<h3 class="muted">
			<?= APPNAME; ?>
		</h3>
		<?php include UP . DS . 'includes' . DS . 'menu.inc.php'; ?>
		<!-- /.navbar -->
	</div>
<h3>Menu XML</h3>
<div id="menu">
<ul>
<?php 
		foreach($xmlData as $menuoverview)
		{
			echo "<li>".$menuoverview['name']."</li>";
			if(isset($menuoverview->menu)){
				echo "<dl>";
				foreach ($menuoverview->menu as $submenu){
					echo "<dt>".$submenu['id']."</dt>";
					echo "<dd>Title: <strong>".$submenu->title."</strong></dd>";
					echo "<dd>Level: ".$submenu->level."</dd>";
					echo "<dd>Order: ".$submenu->order."</dd>";
					echo "<h6>items</h6>";
					if(isset($submenu->items)){
						echo "<dl>";
						foreach ($submenu->items->item as $item){
							echo "<dd>Label: ".$item->label."</dd>";
							echo "<dd>Key: ".$item->key."</dd>";
							echo "<dd>Link: ".$item->link."</dd>";
						}
						echo "</dl>";
					}
				}
				echo "</dl>";
			}
		}
?>
</ul>
</div>
<?php include LIB.'/views/footer.php' ?>