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
	<h3><?= $title; ?></h3>
	<div class="row-fluid">
		<div class="well well-large span12 text-center">
			<img src="/img/access_denied.jpg" />
		</div>
	</div>
	<?php include LIB.'/views/footer.php' ?>