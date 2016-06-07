<?php include LIB.'/views/head.php' ?>
<link rel="stylesheet" href="/js/morris.js-0.4.3/morris.css">
<script src="/js/raphael-min.js"></script>
<script src="/js/morris.js-0.4.3/morris.min.js"></script>
<link rel="stylesheet" href="/js/slider/css/slider.css">
<script src="/js/slider/js/bootstrap-slider.js"></script>
<div id="waiting" class="modal show fade text-center" style="top:30%;height:300px">
		<img src="/img/ajax-loader.gif" style="position: absolute; top:120px;left:260px" />
		<p style="font-family: 'Oswald';position:absolute; width:100%;top:150px">Waiting...</p>
</div>
<div class="container-fluid">
	<div class="masthead">
		<?php include UP . DS . 'includes' . DS . 'menu.inc.php'; ?>
		<!-- /.navbar -->
	</div>
	<h5>
		<?= $title ?>
	</h5>
		<div id="toolbar" class="row-fluid">
			<div class="span12 well">
				<form id="searchForm" class="form-inline span10" method="POST" action="/xml/processMsisdn/<?= $idmenu; ?>">
					<label>Range Date:</label>
					<div id="datetimepicker1" data-date-format="yyyy-mm-dd" data-date="<?= date('Y-m-d'); ?>" class="input-append date">
						<input id="date1" name="date1"  type="text" placeholder="date start..." value="<?= !empty($data['date1']) ? $data['date1'] : (!empty($userData['date1']) ? $userData['date1'] : date('Y-m-d'));  ?>"/>
						<span class="add-on"><i class="icon-th"></i></span>
					</div>
					<div id="datetimepicker2" class="input-append date" data-date="<?= date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
						<input id="date2" name="date2" type="text" placeholder="date end..." value="<?= !empty($data['date2']) ? $data['date2'] : (!empty($userData['date2']) ? $userData['date2'] : date('Y-m-d')); ?>"/>
						<span class="add-on"><i class="icon-th"></i></span>
					</div>
					<label>Cos:</label>
					<select id="listCos" name="cosid" class="span1"></select>
					<button id="submitForm" name="FormFilter" type="submit" class="btn btn-primary">Start</button>
					<?php if($toolExport) {?>
					<span id="threshold">
						<label> Threshold:</label>
	       				<input type="text" value="" data-slider-min="0" data-slider-max="<?= $dailyMax+($dailyMax-$dailyMin)/2; ?>" data-slider-step="5" data-slider-value="[<?= $dailyMin; ?>,<?= $dailyMax?>]" id="sl2" />
	       			</span>
	       			<?php } ?>					
				</form>
				<?php if($toolExport) {?>
				<div class="pull-right	">
					<form id="formExportPDF" class="form-inline" method="POST" action="/xml/menu2pdf/<?= $idmenu; ?>">
						<input type="hidden" name="date1" value="<?php if(!empty($userData['d1'])) echo $userData['d1'];?>"/>
						<input type="hidden" name="date2" value="<?php if(!empty($userData['d2'])) echo $userData['d2'];?>"/>
						<input type="hidden" name="cosid" value="<?php if(!empty($userData['cosid'])) echo $userData['cosid'];?>"/>
						<input type="hidden" name="typeExport" value="msisdn">
						<div class="btn-group">
							<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Export <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a id="exportPDF" href="#">PDF</a></li>
								<li><a id="exportCSV" href="#">CSV</a></li>
								<li><a id="exportXLS" href="#">XLS</a></li>
							</ul>
						</div>
					</form>
				</div>
				<?php }?>
				<?php
					if (isset($errors)) 
					{
						echo '<div class="span12">';
						echo '<ul>';
						foreach ($errors as $e)
						{
							echo '<li class="text-error">' . $e . '</li>';
						}
						echo '</ul>';
						echo '</div>';
					} 
				?>
		</div>
	</div>	
	<div id="myModal" class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Modal header</h3>
		</div>
		<div class="modal-body">
			<div id="analytics"></div>
		</div>
		<div class="modal-footer">
			 <button id="btnCloseModal" class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		</div>
	</div>
	
	<div id="menu">
		<?php
		$menuData = $result;
		$changeLevel = false;
		$levelPrev = 1;
		$count = 0;
		$links = array();
		foreach ($menuData['submenu'] as $submenu){
			if($submenu['level'] != $levelPrev || $count==0){
				if($count > 0){
					echo "</div>";
					echo "<hr>";
				}
				echo '<div class="text-center">';
			}
			echo "<div id='menu_".$submenu['idMenu']."' class='w boxmenu'>";
			echo "<table class='table table-striped table-condensed'>";
			echo "<caption class='info'>".$submenu['name']."</caption>";
			echo "<tr>";
			echo "<th>Item</th>";
			echo "<th><span class='icon-th'></span></th>";
			echo "<th></th>";
			echo "</tr>";
			foreach ($menuData['menu'] as $menu){
				if($menu['idMenu'] == $submenu['idMenu']){
					echo "<tr id='menu_".$submenu['idMenu']."_".$menu['idItem']."' class='rowMenu'>";
					echo "<td class='";
					//add class for cosid
					foreach ($menuData['cos'] as $cos){
						if ($cos["idItem"] == $menu['idItem']){
							echo "cos_".$cos['cosid']." ";
						}
					}
					echo "'>".$menu['label']."</td>";
					echo "<td>".$menu['keypress']."</td>";
					//$nextLevel = current($submenu);
					echo "<td class='view'>";
					if($events[$submenu['idMenu']][$menu['keypress']]!=0){
						echo "<a class='btnBadge'><span id='".$submenu['idMenu']."_".$menu['idItem']."_".$menu['keypress']."' class='badge'>".$events[$submenu['idMenu']][$menu['keypress']]."</span></a>";
					}
					echo"</td>";
					echo "</tr>";
				}
				if($menu['idDestination']!=null  && $menu['level'] <= $menu['levelDestination'] && $count==0){
					array_push($links,$menu['idMenu']."_".$menu['idItem']."_".$menu['idDestination']);
				}
			}
			echo "</table>";
			echo "</div>";
			$levelPrev = $submenu['level'];
			$count++;
			
		}
		?>
		</div>
</div>
<script type="text/javascript">
$(function() {
	$('#waiting').modal('hide');
	$('#datetimepicker1').datepicker();
	$('#datetimepicker2').datepicker();
	var date1 = $('#date1').val();
	var tmp = date1.split('-');
	var day   = tmp[2];
	var month = tmp[1]-1;
	var year  = tmp[0];
	var d1 = new Date(year,month,day);

	var date2 = $('#date2').val();
	var tmp = date2.split('-');
	var day   = tmp[2];
	var month = tmp[1]-1;
	var year  = tmp[0];
	var d2 = new Date(year,month,day);

	var days = parseInt((d2.getTime() - d1.getTime())/(24*3600*1000));

	$('#sl2').slider();
	var sl= $('#sl2').slider('getValue')
	.on('slide',function(){
		var range = sl.getValue();
		$('#dailyMin').val(range[0]);
		$('#dailyMax').val(range[1]);
		$('.badge').each(function(){
			var numberView = $(this).text();
			var dailyMin = range[0] * days;
			var dailyMax = range[1] * days;

			if (numberView <= dailyMin) {
				classBadge = 'badge-important';
			} else if (numberView > dailyMin && numberView < dailyMax) {
				classBadge = 'badge-warning';
			} else {
				classBadge = 'badge-success';
			}
			
			$(this).removeClass('badge-important badge-warning badge-success').addClass(classBadge);
		});
	}).data('slider');
	
	$('.badge').each(function(){
		var numberView = $(this).text();
		var dailyMin = <?= isset($dailyMin) ? $dailyMin : 0; ?> * days;
		var dailyMax = <?= isset($dailyMax) ? $dailyMax : 0; ?> * days;
		console.log(dailyMin+'-'+dailyMax);
		if (numberView <= dailyMin) {
			classBadge = 'badge-important';
		} else if (numberView > dailyMin && numberView < dailyMax) {
			classBadge = 'badge-warning';
		} else {
			classBadge = 'badge-success';
		}
		$(this).addClass(classBadge);
	});
	<?php if(isset($userData['cosid'])) { ?>	
	$('.cos_<?= $userData['cosid']; ?>').each(function(){
		$(this).addClass('highlight');
	});
	<?php } ?>
	//list cos
	$.ajax({
		url: "/cos/listcos/<?= $idmenu; ?>",
		method: "get",
		success: function(data){
			$('#listCos').empty().append(data);
			<?php if(isset($userData['cosid'])) { ?>
			$('#listCos').prop('selectedIndex', '<?= $userData['cosid']==-1 ? 0 : $userData['cosid']; ?>');
			<?php } 
			else{
			?>
			$('#listCos').prop('selectedIndex', '<?= $data['cosid']==-1 ? 0 : $data['cosid']; ?>');
			<?php 
			}
			?>
		}
	});
	jsPlumb.ready(function() {
		var firstInstance = jsPlumb.getInstance();
		var common = {
			Endpoint : ["Dot", {radius:2}],
			HoverPaintStyle : {strokeStyle:"#0088cc", lineWidth:2 },	
			Connector : [ "Flowchart", { stub:[40, 40], gap:0, midpoint: 1,cornerRadius:8, alwaysRespectStubs:true }],
			ConnectionOverlays : [
				[ "Arrow", { 
					location:1,
					id:"arrow",
					width:7,
					length:10,
					foldback:0.9
				} ],
				[ "Label", { label:"", id:"label" }]
			],
			Anchors : [ "LeftMiddle","Top", "RightMiddle" ],
			paintStyle : {
				strokeStyle : "#FC3",
				lineWidth : 3
			}
		};
		var allSourceEndpoints = [], allTargetEndpoints = [];
		_addEndpoints = function(toId, sourceAnchors, targetAnchors) {
			for (var i = 0; i < sourceAnchors.length; i++) {
				var sourceUUID = toId + sourceAnchors[i];
				allSourceEndpoints.push(firstInstance.addEndpoint(toId, sourceEndpoint, { anchor:sourceAnchors[i], uuid:sourceUUID }));						
			}
			for (var j = 0; j < targetAnchors.length; j++) {
				var targetUUID = toId + targetAnchors[j];
				allTargetEndpoints.push(firstInstance.addEndpoint(toId, targetEndpoint, { anchor:targetAnchors[j], uuid:targetUUID }));						
			}
		};

		var dynamicAnchors = [ "Top","Right", "Left" ];
		firstInstance.importDefaults(common);
		<?php
// 			foreach($links as $link){
// 				$temp = explode("_", $link);
// 				echo '_addEndpoints("menu_'.$temp[0].'_'.$temp[1].'", ["TopCenter", "BottomCenter"], ["LeftMiddle", "RightMiddle"]);\n';
// 			}
			foreach($links as $link){
				$temp = explode("_", $link);	
				echo "firstInstance.connect({";
				echo 'source: "menu_'.$temp[0].'_'.$temp[1].'",';
				echo 'target: "menu_'.$temp[2].'",';
				echo 'anchors:[ "RightMiddle", "Top" ]';
				//echo 'anchor:["Continuous", { faces:["right", "left"] } ]';
				//echo 'anchor: dynamicAnchors';
				echo "}, common);\n";	
			} 
		?>		
		firstInstance.repaintEverything();	
		$(window).resize(function() {
			firstInstance.repaintEverything();	
		});
	});
});	
</script>
<script type='text/javascript' src='/js/jquery.jsPlumb-1.4.1-all-min.js'></script>	
<?php include LIB.'/views/footer.php' ?>