<?php include LIB.'/views/head.php' ?>
<script type="text/javascript">
	$(function(){
		$('.masthead').hide().slideDown("fast");
	});
</script>
<div class="container-fluid">
	<div class="masthead">
		<h3 class="muted">
			<?= APPNAME; ?>
		</h3>
		<?php include UP.DS.'includes' . DS . 'menu.inc.php'; ?>
		<!-- /.navbar -->
	</div>
	<h3><?= $title; ?></h3>
	<div class="row-fluid">
		<div class="well well-large span12">
			<div class="span4"></div>
			<div class="span4">
				<h3><?= $error; ?></h3>
			</div>
			<div class="span4"></div>
		</div>
	</div>
	<?php include LIB.'/views/footer.php' ?>