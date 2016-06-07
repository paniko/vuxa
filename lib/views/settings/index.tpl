<?php include LIB.'/views/head.php' ?>

<div class="container-fluid">
	<div class="masthead">
		<h3 class="muted">
			<?= APPNAME; ?>
		</h3>
		<?php include UP.DS.'includes' . DS . 'menu.inc.php'; ?>
		<!-- /.navbar -->
	</div>
	<div class="row-fluid">

		<?php 
		if (isset($errors))
		{
			?>
			<div class="alert">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
				<ul>
				<?php 
				foreach ($errors as $e)
				{
					echo '<li>' . $e . '</li>';
				}
				?>
				</ul>
			</div>	
			<?php 
		}

		if (isset($saveError))
		{
			echo "<h2>Error saving data. Please try again.</h2>" . $saveError;
		}
		?>

		<form action="/settings/save" method="post">

			<p>
				<label for="first_name">Content Row Filter:</label> <input
					value="<?php if(isset($userData)) echo $userData['content_row_filter']; ?>"
					type="text" id="content_row_filter" name="content_row_filter" />
			</p>

			<p>
				<label for="separation_char">Separation Char:</label> <input
					value="<?php if(isset($userData)) echo $userData['separation_char']; ?>"
					type="text" id="separation_char" name="separation_char" />
			</p>

			<p>
				<label for="date_position">Date Position:</label> <input
					value="<?php if(isset($userData)) echo $userData['date_position']; ?>"
					type="text" id="date_position" name="date_position" />
			</p>

			<p>
				<label for="time_position"> Time Position:</label><input
					value="<?php if(isset($userData)) echo $userData['time_position']; ?>"
					type="text" id="time_position" name="time_position" />
			</p>

			<input class="btn btn-primary" type="submit" name="settingsFormSubmit" value="Save" />
		</form>

	</div>
<?php include LIB.'views/footer.php' ?>