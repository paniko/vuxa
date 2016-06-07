<?php defined('_LOGIN') or die('Restricted access'); ?>
<?php include LIB.'/views/head.php' ?>
<link rel="stylesheet" href="/js/morris.js-0.4.3/morris.css">
<link rel="stylesheet" href="/js/slider/css/slider.css">
<script src="/js/slider/js/bootstrap-slider.js"></script>
<script src="/js/raphael-min.js"></script>
<script	src="/js/morris.js-0.4.3/morris.min.js"></script>
<link rel="stylesheet" href="/js/bootstrap-select/bootstrap-select.css">
<script	src="/js/bootstrap-select/bootstrap-select.js"></script>
<style>
.modal.fade, .modal.in {
    -webkit-transition: none;
    -moz-transition: none;
    -ms-transition: none;
    -o-transition: none;
    transition: none;
}​
.ui-widget-content {
	border: 1px solid #aaa;
	background: #fff url(images/ui-bg_flat_75_ffffff_40x100.png) 50% 50% repeat-x;
	color: #222;
}
.ui-slider-horizontal {
	height: .8em;
}
.ui-slider {
	position: relative;
	text-align: left;
}
.ui-slider-horizontal .ui-slider-range {
	top: 0;
	height: 100%;
}
.ui-slider .ui-slider-range {
	position: absolute;
	z-index: 1;
	font-size: .7em;
	display: block;
	border: 0;
	background-position: 0 0;
}
.ui-slider .ui-slider-handle {
	position: absolute;
	z-index: 2;
	width: 1.2em;
	height: 1.2em;
	cursor: default;
}
.ui-slider-horizontal .ui-slider-handle {
	top: -.3em;
	margin-left: -.6em;
}
.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
	border: 1px solid #d3d3d3;
	background: #e6e6e6 url(images/ui-bg_glass_75_e6e6e6_1x400.png) 50% 50% repeat-x;
	font-weight: 400;
	color: #555;
}
</style>
<div class="container-fluid">
	<div class="masthead">
		<?php include UP . DS . 'includes' . DS . 'menu.inc.php'; ?>
		<!-- /.navbar -->
	</div>
	<div id="toolbar" class="row-fluid">
		<div class="span12 well">
			<form id="searchForm" class="form-inline" method="POST"	action="/xml/process/<?= $idmenu; ?>">
				<label>Range Date:</label>
				<div id="datetimepicker1" data-date-format="yyyy-mm-dd"	class="input-append date" data-date="<?= !empty($data['date1']) ? $data['date1'] : (!empty($userData['date1']) ? $userData['date1'] : date('Y-m-d')); ?>" >
					<input id="date1" name="date1" type="text" placeholder="start date ..." 
					value="<?= !empty($data['date1']) ? $data['date1'] : (!empty($userData['date1']) ? $userData['date1'] : date('Y-m-d')); ?>" />
					<span class="add-on"><i class="icon-th"></i> </span>
				</div>
				<div id="datetimepicker2" data-date-format="yyyy-mm-dd" class="input-append date" data-date="<?= !empty($data['date2']) ? $data['date2'] : (!empty($userData['date2']) ? $userData['date2'] : date('Y-m-d'));  ?>" >
					<input id="date2" name="date2" type="text" placeholder="end date ..." 
					value="<?= !empty($data['date2']) ? $data['date2'] : (!empty($userData['date2']) ? $userData['date2'] : date('Y-m-d'));  ?>" />
					<span class="add-on"><i class="icon-th"></i> </span>
				</div>
				<label>Cos:</label> <select multiple id="listCos" name="cosid[]" class="selectpicker span1"><option value='-1'>All</option></select>
				<button id="submitForm" name="FormFilter" type="submit"	class="btn btn-primary">Start</button>
				<?php if($toolExport) {?>
				<span id="threshold" class="pull-right">
					<label> Threshold:</label>
       				<input type="text" value="" data-slider-min="0" data-slider-max="<?= $dailyLimit; ?>" data-slider-step="5" data-slider-value="[<?= $dailyMin; ?>,<?= $dailyMax?>]" id="sl2" />
       			</span>
       			<?php } ?>
			</form>

			<?php if($toolExport) {?>
			<div id="toolExport" class="pull-right">
				<form id="formExport" class="form-inline" method="POST"
					action="/xml/menu2pdf/<?= $idmenu; ?>">
					<input type="hidden" name="date1" value="<?= !empty($data['date1']) ? $data['date1'] : (!empty($userData['date1']) ? $userData['date1'] : date('Y-m-d')); ?>"/>
					<input type="hidden" name="date2" value="<?= !empty($data['date2']) ? $data['date2'] : (!empty($userData['date2']) ? $userData['date2'] : date('Y-m-d')); ?>"/>
					<?php 
						if(!empty($data['cosid'])){
							foreach ($data['cosid'] as $cosid){
					?>
					<input type="hidden" name="cosid[]" value="<?= $cosid; ?>"/>
					<?php 
							}
						}
					?>
					<input id="chkMsisdn" type="hidden" name="chkMsisdn" value="" />
					<input type="hidden" id="dailyMin" name="dailyMin" value="<?= $dailyMin; ?>" />
					<input type="hidden" id="dailyMax" name="dailyMax" value="<?= $dailyMax; ?>" />
					<input type="hidden" name="typeExport" value="hits">
					<input type="hidden" id="outExport" name="outExport" value="">
					<!-- 
					<div class="btn-group">
						<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Export <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a id="exportPDF" href="#">PDF</a></li>
							<li><a id="exportCSV" href="#">CSV</a></li>
							<li><a id="exportXLS" href="#">XLS</a></li>
						</ul>
					</div>
					
					<button id="exportPDF" type="submit" class="btn btn-primary">PDF</button>
					<a id="exportCSV" class="btn btn-primary">CSV</a>
					<a id="exportXLS" class="btn btn-primary">XLS</a>
					 -->
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
	<div id="menu">
		<?php
		if(isset($result)){
			$menuData = $result;
			$changeLevel = false;
			$levelPrev = 1;
			$count = 0;
			$links = array();
			foreach ($menuData['submenu'] as $submenu){
				if($submenu['level'] != $levelPrev || $count==0){
					if($count > 0){
						echo "</div>";
						echo "<hr/>";
					}
					echo '<div class="text-center">';
				}
				echo "<div id='menu_".$submenu['idMenu']."' class='w boxmenu'>";
				echo "<table class='table table-striped table-condensed'>";
				echo "<caption class='info'>".$submenu['name']."</caption>";
				echo "<tr>";
				echo "<th><input type='checkbox' class='chkToggle' data-toggle='tooltip' title='Click here for select all features' /></th>";
				echo "<th>Item</th>";
				echo "<th><span class='icon-th'></span></th>";
				echo "<th></th>";
				echo "</tr>";
				foreach ($menuData['menu'] as $menu){
					if($menu['idMenu'] == $submenu['idMenu']){
						echo "<tr id='menu_".$submenu['idMenu']."_".$menu['idItem']."' class='rowMenu'>";
						echo "<td><input type='checkbox' class='chkItem'/></td>";
						echo "<td class='name ";
						//add class for cosid
						if(!empty($menuData['cos']) && (isset($userData['cosid']) && !in_array("-1", $userData['cosid']))){
							$countcos=0;
							foreach ($menuData['cos'] as $cos){
								if ($cos["idItem"] == $menu['idItem']){
									echo "cos_".$cos['cosid']." ";
									$countcos++;
								}
							}
							if($countcos==0)
								echo "no_cos ";
						}
						echo "'>".$menu['label']."</td>";
						echo "<td>".$menu['keypress']."</td>";
						//$nextLevel = current($submenu);
						echo "<td class='view'>";
						if($events[$submenu['idMenu']][$menu['keypress']]!=0){
							echo "<a class='btnBadge'><span id='".$submenu['idMenu']."_".$menu['idItem']."_".$menu['keypress']."' class='badge'>".$events[$submenu['idMenu']][$menu['keypress']]."</span></a>";
						}
						else{
							echo "<a class='btnBadge' style='display:none'><span id='".$submenu['idMenu']."_".$menu['idItem']."_".$menu['keypress']."' class='badge'></span></a>";							
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
		}
?>
	</div>
	<div id="myModal" class="modal fade hide" style="width: 800px;height: 487px; margin-left: -400px;">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="titleGraph">Hourly statistics</h3>
		</div>
		<div class="modal-body">
		<div id="waiting" class="modal show text-center" style="top:21%;height:300px">
			<img src="/img/ajax-loader.gif" style="position: absolute; top:120px;left:260px" />
			<p style="font-family: 'Oswald';position:absolute; width:100%;top:150px">Waiting...</p>
		</div>
			<div id="analytics"></div>
		</div>
		<div class="modal-footer">
			<button id="btnCloseModal" class="btn" data-dismiss="modal"	aria-hidden="true">Close</button>
		</div>
	</div>
</div>
<script type="text/javascript">
var firstInstance = null;
function renderConnector(){
	firstInstance = jsPlumb.getInstance();
	
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
//			foreach($links as $link){
//				$temp = explode("_", $link);
//				echo '_addEndpoints("menu_'.$temp[0].'_'.$temp[1].'", ["TopCenter", "BottomCenter"], ["LeftMiddle", "RightMiddle"]);\n';
//			}
		if(!empty($links)){
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
		}
	?>		
	$(window).resize(function() {
		firstInstance.repaintEverything();	
	});
	firstInstance.repaintEverything();	
}
$(function() {
	jsPlumb.bind("ready",function() {
		renderConnector();
	});
	var selectCos = $('.selectpicker');
	selectCos.selectpicker();
	//export
	$('.btnExport').click(function(){
		var out = $(this).attr('data-export');
		var isChecked = $('.chkItem:checked').length;
		if(isChecked==0){
			$('#outExport').val(out);
			$('#formExport').prop('action','/xml/export/<?= $idmenu; ?>');
		}
		else{
			$('#formExport').prop('action','/xml/menu2xlsMsisdn/<?= $idmenu; ?>');
			var outStrEvents='';
			$('.chkItem:checked').each(function(){
				outStrEvents += $(this).parent().parent().find('.badge').prop('id');
				outStrEvents += '|';
			});
			outStrEvents = outStrEvents.substr(0,outStrEvents.length-1);
			$('#chkMsisdn').val(outStrEvents);
		}
		$('#formExport').submit();
	});
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
	var slideMouseDown = false;
	$('.slider-handle').mousedown(function() {
	      slideMouseDown = true;
	    });
	$('.slider-handle').on('mouseleave',function(){
		if(slideMouseDown == true)
		{
			var range = sl.getValue();
			$.ajax({
				url: '/slider/store/hits',
				method: 'post',
				data: {'threshold': range}
			});
		}
	});
	<?php 
			if(isset($_SESSION['hits']['threshold'])){
		?>
			var setThreshold = new Array();
			setThreshold[0] = <?= $_SESSION['hits']['threshold'][0]; ?>;
			setThreshold[1] = <?= $_SESSION['hits']['threshold'][1]; ?>;
			<?php if($toolExport) {?>
			sl.setValue(setThreshold);
			<?php } ?>
			$('.badge').each(function(){
				var numberView = $(this).text();
				var dailyMin = setThreshold[0] * days;
				var dailyMax = setThreshold[1] * days;

				if (numberView <= dailyMin) {
					classBadge = 'badge-important';
				} else if (numberView > dailyMin && numberView < dailyMax) {
					classBadge = 'badge-warning';
				} else {
					classBadge = 'badge-success';
				}			
				$(this).removeClass('badge-important badge-warning badge-success').addClass(classBadge);
			});
		<?php 
			}
			else{
		?>
			$('.badge').each(function(){
				var numberView = $(this).text();
				var dailyMin = <?= isset($dailyMin) ? $dailyMin : 0; ?> * days;
				var dailyMax = <?= isset($dailyMax) ? $dailyMax : 0; ?> * days;
				
				if (numberView <= dailyMin) {
					classBadge = 'badge-important';
				} else if (numberView > dailyMin && numberView < dailyMax) {
					classBadge = 'badge-warning';
				} else {
					classBadge = 'badge-success';
				}
				$(this).addClass(classBadge);
			});		
		<?php 				
			}
		?>
	$('.chkToggle').tooltip();
	$('.chkToggle').click(function(){
		var isChecked = $( this ).is( ':checked' );
		var chk = $(this).parent().parent().parent().find('.chkItem');
		if(!isChecked){
			$(this).prop('title','Click here for ckeck all features.');
			chk.each(function(){
				$(this).prop('checked', '');
			});			
		}
		else{
			$(this).prop('title','Click here for unselected all features.');
			chk.each(function(){
				$(this).prop('checked', 'checked');
			});
		}
		$('.chkToggle').tooltip('destroy');
		$('.chkToggle').tooltip();
	});
	$('.chkToggle').tooltip();
	
	<?php if(isset($userData['cosid']) && !in_array("-1",$userData['cosid'])) { ?>	
	$('.no_cos').each(function(){
		var nocos = $('.no_cos').parent();
		nocos.addClass('nohighlight');
		nocos.find('input').prop('disabled','disabled');
	});
        
        <?php 
    
          foreach($userData['cosid'] as $cos) {
          if ($cos!=-1) { ?>
	$('.cos_<?= $cos; ?>').each(function(){
		$(this).addClass('highlight');
	});
	<?php } }
           }
          ?>
	//histogram
	$('.badge').click(function(){
		var tmp = $(this).prop('id').split('_');
		var idMenu  = tmp[0];
		var click   = tmp[1];
		var keyItem = tmp[2];
		var name = $(this).parent().parent().parent().find('.name').text();
		$('#analytics').empty();
		$('#waiting').modal('show');
		/*
		$.ajax({
			url: '/calls/histogram/'+click,
			method: "get",
			cache: true
		}).always(function(data){
				$('#analytics').empty().append(data);
		});
		*/	
		$('#myModal').show("fast",function showDataHistogram(){
			$('#titleGraph').empty().append('Hourly statistics - feature: '+name);
			
			$.ajax({
				url: '/calls/histogram/'+click,
				method: "post",
				data: { 'idMenu' : idMenu,'keypress': keyItem }
			})
			.done(function(dataHistogram){
				//var points = $.parseJSON(dataHistogram);
				//console.log(points);
				Morris.Bar({
					  element: 'analytics',
					  data: dataHistogram,
					  xkey: 'y',
					  ykeys: ['a'],
					  labels: ['Clicks'],
					  xLabelMargin: 2,
					  xLabelsDiagonal: true
				});	
				$('#waiting').modal('hide');
			});

		});
		$('#myModal').modal();
			
	});
	//list cos
	$.ajax({
		url: "/cos/listcos/<?= $idmenu; ?>",
		method: "get",
		success: function(data){
			$('#listCos').empty().append(data);
			<?php 
				if(isset($userData['cosid'])) {
					$tmp="[";
					foreach ($userData['cosid'] as $cosid){
						$tmp .= $cosid.",";
					}
					$tmp = substr($tmp, 0,-1);
					$tmp.="]";

			?>
					selectCos.selectpicker('val',<?= $tmp; ?>);
			<?php 
				}
				else{
				?>
					selectCos.selectpicker('val','-1');
				<?php 	
				}
			?>
			selectCos.selectpicker('refresh');
			$('.bootstrap-select > .dropdown-menu').on('click','li a',function(event){
				var selectAll = $(this).find('.text:first').text();
				if( selectAll == "All"){
					console.log(selectAll);
					$('.selectpicker').val('-1');
					$('.selectpicker').selectpicker('refresh');
				}
				else{
					var valueSelect = $('.selectpicker').selectpicker('val')
					var index = valueSelect.indexOf('-1');
					if(index == 0){
						valueSelect = valueSelect.splice(index+1, 1);
						$('.selectpicker').selectpicker('val',valueSelect);
						$('.selectpicker').selectpicker('refresh');
					}
				}
			});				
			firstInstance.repaintEverything();
		}
	});

});	
</script>
<script	type='text/javascript' src='/js/jquery.jsPlumb-1.4.1-all-min.js'></script>
<?php include LIB.'/views/footer.php' ?>