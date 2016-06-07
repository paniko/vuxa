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
					echo "<td class='name ";
					//add class for cosid
					if(!empty($menuData['cos']) && (isset($userData['cosid']) && $userData['cosid']!=-1)){
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
$(function(){
	jsPlumb.bind("ready",function() {
		renderConnector();
	});
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
				url: '/slider/store/msisdn',
				method: 'post',
				data: {'threshold': range}
			});
		}
	});
	<?php 
		if(isset($_SESSION['msisdn']['threshold'])){
	?>
		var setThreshold = new Array (
			<?= $_SESSION['msisdn']['threshold'][0]; ?>,
			<?= $_SESSION['msisdn']['threshold'][1]; ?>		
				);
		sl.setValue(setThreshold);
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
	<?php 
		if(isset($userData['cosid']) && !in_array("-1",$userData['cosid'])) { ?>	
			$('.no_cos').each(function(){
				var nocos = $('.no_cos').parent();
				nocos.addClass('nohighlight');
				nocos.find('input').prop('disabled','disabled');
			});
	<?php
			foreach($userData['cosid'] as $cos) {
				if ($cos!=-1) { 
	?>
					$('.cos_<?= $cos; ?>').each(function(){
						$(this).addClass('highlight');
					});
	<?php 
				}
			}
		}
	?>
});
</script>