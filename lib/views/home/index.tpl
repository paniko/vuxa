<?php include LIB.'/views/head.php' ?>
<script type="text/javascript">
	$(function(){
		$('.masthead').hide().slideDown("fast");
	});
</script>
<div class="container-fluid">
		<div class="masthead">
			<?php include UP.DS.'includes' . DS . 'menu.inc.php'; ?>
			<!-- /.navbar -->
		</div>
		<div class="row-fluid">
			<div class="well well-large span12">
					<div style="text-align: center; margin: 0 auto;">
						<h3><?php echo $title; ?></h3>
						<div class="row-fluid">
							<div class="span12" id="content">
								<img src="../img/Avens_logo.png" />
							</div>
						</div>
					</div>
			</div>
		</div>	
<?php include LIB.'/views/footer.php' ?>