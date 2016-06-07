<?php
	defined('_LOGIN') or die('Restricted access');
	include LIB.'/views/head.php';
	header('Content-type: text/html; charset=utf-8');
 ?>
<link
	rel="stylesheet"
	href="/js/jquery-fileupload/css/jquery.fileupload-ui.css">
<script
	type="text/javascript"
	src="/js/datatables/media/js/jquery.dataTables.js"></script>
<script
	type="text/javascript" src="/js/datatables/media/js/DT_bootstrap.js"></script>
<style type="text/css" title="currentStyle">
@import "/js/datatables/media/css/DT_bootstrap.css";

table.table {
	font-size: 1em;
}

thead tr {
	background-color: #FFF;
}

.even {
	background-color: #EEE;
}

/* @import "css/jquery-ui.css"; */
table.table thead .sorting,table.table thead .sorting_asc,table.table thead .sorting_desc,table.table thead .sorting_asc_disabled,table.table thead .sorting_desc_disabled
	{
	cursor: pointer;
	*cursor: hand;
}

.dropdown-menu>li>a {
	cursor: pointer;
}

table.table thead .sorting {
	background: url('js/datatables/media/images/sort_both.png') no-repeat
		center right;
}

table.table thead .sorting_asc {
	background: url('js/datatables/media/images/sort_asc.png') no-repeat
		center right;
}

table.table thead .sorting_desc {
	background: url('js/datatables/media/images/sort_desc.png') no-repeat
		center right;
}

table.table thead .sorting_asc_disabled {
	background: url('js/datatables/media/images/sort_asc_disabled.png')
		no-repeat center right;
}

table.table thead .sorting_desc_disabled {
	background: url('js/datatables/media/images/sort_desc_disabled.png')
		no-repeat center right;
}

table.table tr.row_selected td {
	background-color: #D8E8FF !important;
}
table.table td.note{
	text-align: justify;
}
</style>
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h5 id="myModalLabel">Insert note</h5>
  </div>
  <div class="modal-body container-fluid">
    <form id="frmNote" class="row-fluid">
    	<label>Note:</label>
    	<textarea id="note" name ="note" rows="3" class="span12"></textarea>
    </form>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button id="btnSaveNote" class="btn btn-primary">Save</button>
  </div>
</div>

<div id="myModalDelete" class="modal hide fade">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Delete Menu Overview</h3>
  </div>
  <div class="modal-body">
    <p id="txtConfirm">Are you sure?</p>
    <input id="idMenuOverviewSelected" type="hidden" val="" /> 
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button id="btnModalDelete" data-dismiss="modal" aria-hidden="true" class="btn btn-primary">Delete</button>
  </div>
</div>
<div class="container-fluid">
	<div class="masthead">
		<?php include UP.DS.'includes' . DS . 'menu.inc.php'; ?>
		<!-- /.navbar -->
		<h5>
			<?= $title ?>
		</h5>
	</div>
	<div class="row-fluid">
		<!-- The fileinput-button span is used to style the file input field as button -->
		<!-- 
		<span id="noteItem">
			<label>Note:</label>
			<textarea class="span12" id="note"></textarea>
			<br>
		</span>
		 -->
		<span id="btnUpload" class="btn btn-primary fileinput-button"> <i
			class="icon-plus icon-white"></i> <span>Select files...</span> <!-- The file input field used as target for the file upload widget -->
			<input id="fileupload" type="file" name="files[]" multiple>
		</span> 
		<a id="btnLoad" style="display: none" class="btn btn-success"><i class="icon-upload icon-white"></i> Load XML</a>
		<a id="btnDefault"	style="display: none" class="btn btn-success"><i class="icon-ok-sign icon-white"></i> Set Default</a>
		<a id="btnNote"	style="display: none" class="btn btn-success" href="#myModal" data-toggle="modal"><i class="icon-edit icon-white"></i> Insert Note</a>
		<br><br>
		<!-- The global progress bar -->
		<div id="progress" class="progress progress-success progress-striped">
			<div class="bar"></div>
		</div>
		<!-- The container for the uploaded files -->
		<div id="files"></div>
		<br>
	</div>
	<div class="row-fluid">
		<a id="btnViewMenu" class="btn btn-success" style="display: none"> <i
			class="icon-eye-open icon-white"></i> <span>View Menu</span>
		</a>

	</div>
	<div class="row-fluid">
		<table id="tbXML"
			class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th width='20'>Default</th>
					<th width='150'>Name</th>
					<th width='150'>Filename</th>
					<th>Note</th>
					<th width='20'>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if(!empty($xmlLoaded)){
					foreach($xmlLoaded as $row){
						echo "<tr id='".$row['idMenuOverview']."'>";
						echo "<td class='set_default'>";
						if($row['defaultXML']==1){
							echo "<span id='default'><i class='icon-ok'></i></span>";
						}
						echo "</td>";
						echo "<td class='name'>".$row['name']."</td>";
						echo "<td>".$row['filename']."</td>";
						echo "<td class='note'>".$row['note']."</td>";
						echo "<td class='text-center'><a class='btnDelete' href='#myModalDelete' data-toggle='modal'><i class='icon-trash'></i></a></td>";
						echo "</tr>";
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<script>
/*jslint unparam: true */
/*global window, $ */
$(function () {
	$('#tbXML').dataTable();
	$('.btnDelete').click(function(){
		var idMenu = $(this).parent().parent().prop('id');
		var nameMenu = $(this).parent().parent().find('.name').text();
		$('#idMenuOverviewSelected').val(idMenu);
		$("#txtConfirm").empty().append('Are you sure delete menu: '+nameMenu+'?');
	});	
	$('#btnModalDelete').click(function(){
		var idMenu = $('#idMenuOverviewSelected').val();
		$.ajax({
			url: '/xml/delete/'+idMenu
		})
		.done(function(){
			var idRow = $('#idMenuOverviewSelected').val();
			$('#'+idRow).remove();
			$('#btnLoad').hide();
			$('#btnDefault').hide();
			$('#btnNote').hide();		
			$('#btnUpload').show();
		});
	});
	$('#btnSaveNote').click(function(){
		var idMenu  = $('.row_selected').prop('id');
		var note = $('#note').val();
		$.ajax({
			url: '/xml/addnote/'+idMenu,
			data:{'note': note},
			method:'post'
		})
		.done(function(){
			$('#myModal').modal('hide');
			$('.row_selected').find('.note').empty().append(note);
		});
	}); 
	$('#btnNote').click(function(){
		var txtNote  = $('.row_selected').find('.note').text();
		var nameMenu = $('.row_selected').find('.name').text();
		$('#note').val(txtNote);
		$('#myModalLabel').empty().append('Insert note to ' + nameMenu);
	});
	$('#btnLoad').click(function(){
		var idMenu = $('.row_selected').prop('id');
		window.location.href = '/xml/viewdb/'+idMenu;
		return false;
	});
	$('#btnDefault').click(function(){
		var idMenu = $('.row_selected').prop('id');
		$.ajax({
			url: '/xml/defaultXML/'+idMenu
		});
		$('#default').remove();
		var outDefault = "<span id='default'><i class='icon-ok'></i></span>";
		$('.row_selected').find('.set_default').append(outDefault);

	});
	$("#tbXML tbody").on("click", "tr", function(event) {
		var tableEmpty = $(this).find('td:first').hasClass('dataTables_empty');
		//var checkbox = $(this).find('input:checkbox');
		if ( $(this).hasClass('row_selected') ) {
			$(this).removeClass('row_selected');
			$('#btnLoad').hide();
			$('#btnDefault').hide();
			$('#btnNote').hide();		
			$('#btnUpload').show();
			//$('#noteItem').show();
		}
		else {//select row
			if(!tableEmpty){
				$('table tr.row_selected').removeClass('row_selected');
	            $(this).addClass('row_selected');
	            $('#btnLoad').show();
	            $('#btnDefault').show();
	            var note = $(this).find('.note').text();
	            if(note!=""){
	            	$('#btnNote').empty().append('<i class="icon-edit icon-white"></i> Edit Note');
	            }
	            else{
	            	$('#btnNote').empty().append('<i class="icon-pencil icon-white"></i> Insert Note');
	            }
	            $('#btnNote').show();
	            $('#btnUpload').hide();
	            //$('#noteItem').hide();
			}
        }
    });
    
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = '../js/jquery-fileupload/server/php/';
    var xmlfile="";
    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo('#files');
                /*
                var txtNote = $('#note').val();
                $.ajax({
					url: '/xml/save/'+file.name,
					method:'post',
					async:false,
					data:{'note': txtNote}
                });
                */
                window.location.href='/xml/save/'+file.name;
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar').css(
                'width',
                progress + '%'
            );
        }
    });
    
});
</script>

	<script type="text/javascript"
		src="../js/jquery-fileupload/js/vendor/jquery.ui.widget.js"></script>
	<script type="text/javascript"
		src="../js/jquery-fileupload/js/jquery.iframe-transport.js"></script>
	<script type="text/javascript"
		src="../js/jquery-fileupload/js/jquery.fileupload.js"></script>

	<?php include LIB.'/views/footer.php' ?>