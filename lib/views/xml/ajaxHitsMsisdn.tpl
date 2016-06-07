<?php include LIB.'/views/head.php' ?>
<link rel="stylesheet" href="/js/morris.js-0.4.3/morris.css">
<script src="/js/raphael-min.js"></script>
<script src="/js/morris.js-0.4.3/morris.min.js"></script>
<link rel="stylesheet" href="/js/slider/css/slider.css">
<script src="/js/slider/js/bootstrap-slider.js"></script>
<link rel="stylesheet" href="/js/bootstrap-select/bootstrap-select.css">
<script	src="/js/bootstrap-select/bootstrap-select.js"></script>
<div id="waiting" class="modal show fade text-center" style="top:30%;height:300px">
		<img src="/img/ajax-loader.gif" style="position: absolute; top:120px;left:260px" />
		<p style="font-family: 'Oswald';position:absolute; width:100%;top:150px">Waiting...</p>
</div>
<div class="container-fluid">
	<div class="masthead">
		<?php include UP . DS . 'includes' . DS . 'menu.inc.php'; ?>
		<!-- /.navbar -->
	</div>
		<div id="toolbar" class="row-fluid">
			<div class="span12 well">
				<form id="searchForm" class="form-inline" method="POST" action="/xml/ajaxHitsMsisdn/<?= $idmenu; ?>">
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
					<select multiple id="listCos" name="cosid[]" class="selectpicker span1"><option value='-1'>All</option></select>
					<button id="submitForm" name="FormFilter" type="submit" class="btn btn-primary">Start</button>
					<?php if($toolExport) {?>
					<span id="threshold" class="pull-right">
						<label> Threshold:</label>
	       				<input type="text" value="" data-slider-min="0" data-slider-max="<?= $dailyLimit; ?>" data-slider-step="5" data-slider-value="[<?= $dailyMin; ?>,<?= $dailyMax?>]" id="sl2" />
	       			</span>
	       			<?php } ?>					
				</form>
				<?php if($toolExport) {?>
				<div class="pull-right	">
					<form id="formExport" class="form-inline" method="POST" action="/xml/menu2pdf/<?= $idmenu; ?>">
						<input type="hidden" name="date1" value="<?= !empty($userData['date1']) ? $userData['date1'] : (!empty($data['date1']) ? $data['date1'] : date('Y-m-d')); ?>"/>
						<input type="hidden" name="date2" value="<?= !empty($userData['date2']) ? $userData['date1'] : (!empty($data['date2']) ? $data['date2'] : date('Y-m-d')); ?>"/>
						<?php 
							if(!empty($data['cosid'])){
								foreach ($data['cosid'] as $cosid){
						?>
						<input type="hidden" name="cosid[]" value="<?= $cosid; ?>"/>
						<?php 
								}
							}
						?>						<input type="hidden" id="dailyMin" name="dailyMin" value="<?= $dailyMin; ?>" />
						<input type="hidden" id="dailyMax" name="dailyMax" value="<?= $dailyMax; ?>" />
						<input type="hidden" name="typeExport" value="msisdn">
						<input type="hidden" id="outExport" name="outExport" value="">
<!-- 						<div class="btn-group"> -->
<!-- 							<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Export <span class="caret"></span></a> -->
<!-- 							<ul class="dropdown-menu"> -->
<!-- 								<li><a id="exportPDF" href="#">PDF</a></li> -->
<!-- 								<li><a id="exportCSV" href="#">CSV</a></li> -->
<!-- 								<li><a id="exportXLS" href="#">XLS</a></li> -->
<!-- 							</ul> -->
<!-- 						</div> -->
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
	
	<div id="menu"></div>
</div>
<script type="text/javascript">
$(function() {
	var selectCos = $('.selectpicker');
	selectCos.selectpicker();
	//export
	$('.btnExport').click(function(){
		var out = $(this).attr('data-export');
		$('#outExport').val(out);
		$('#formExport').prop('action','/xml/export/<?= $idmenu; ?>');
		$('#formExport').submit();
	});
	$('#waiting').modal('show');
	$.ajax({
		url:'/xml/processAjaxMsisdn/<?= $idmenu; ?>',
		method: 'post'
	})
	.done(function(data){
		$('#menu').empty().append(data);
		$('#waiting').modal('hide');
	});
	$('#datetimepicker1').datepicker();
	$('#datetimepicker2').datepicker();


	//exportPDF
	$('#exportPDF').click(function(){
		$('#formExport').prop('action','/xml/menu2pdf/<?= $idmenu; ?>');
		$('#formExport').submit();
	});
	//exportCSV
	$('#exportCSV').click(function(){
		var isChecked = $('.chkItem:checked').length;
		if(isChecked==0){
			$('#formExport').prop('action','/xml/menu2csv/<?= $idmenu; ?>');
			$('#formExport').submit();
		}
		else{
			$('#formExport').prop('action','/xml/menu2csvMsisdn/<?= $idmenu; ?>');
			var outStrEvents='';
			$('.chkItem:checked').each(function(){
				outStrEvents += $(this).parent().parent().find('.badge').prop('id');
				outStrEvents += '|';
			});
			outStrEvents = outStrEvents.substr(0,outStrEvents.length-1);
			$('#chkMsisdn').val(outStrEvents);
			$('#formExport').submit();
		}
	});
	//exportXLS
	$('#exportXLS').click(function(){
		var isChecked = $('.chkItem:checked').length;
		if(isChecked==0){
			$('#formExport').prop('action','/xml/menu2xls/<?= $idmenu; ?>');
			$('#formExport').submit();
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
			$('#formExport').submit();
		}
	});	
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
	<?php }
            }
           }
          ?>
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
<script type='text/javascript' src='/js/jquery.jsPlumb-1.4.1-all-min.js'></script>	
<?php include LIB.'/views/footer.php' ?>