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
			 <div class="span8" style="float:none;margin:0 auto;width:515px;">
				<form class="form-horizontal" action="/users/login" method="POST">
					<fieldset>
						<div class="control-group">
							<label class="control-label" for="username">Username</label>
							<div class="controls">
								<input type="text" name="username" id="username" required placeholder="insert your username..." />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="inputPassword">Password</label>
							<div class="controls">
								<input type="password" name="password" required id="inputPassword" placeholder="insert your password." />
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<button type="submit" class="btn-large btn-primary">Sign in</button>
							</div>
						</div>				
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
					</fieldset>
				</form>
			</div>
		</div>
	</div>
	<?php include LIB.'/views/footer.php' ?>