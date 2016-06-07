<?php include LIB.'/views/head.php' ?>

<div class="container-fluid">
		<div class="masthead">
			<h3 class="muted"><?= APPNAME; ?></h3>
		<?php include UP.DS.'includes' . DS . 'menu.inc.php'; ?>
			<!-- /.navbar -->
		</div>
		<h3><?php echo $title; ?></h3>
		<div class="row-fluid">
			<div class="well well-large span12">
				<div class="span4"></div>
				<div class="span4">
					<div style="text-align: center; margin: 0 auto;">
						<div class="row-fluid">
							<div class="alert alert-success">
								<?php echo $message; ?>
							</div>
						</div>
					</div>
				</div>
				<div class="span4"></div>
			</div>
		</div>	
<?php include LIB.'/views/footer.php' ?>