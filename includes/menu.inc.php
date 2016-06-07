<?php session_start();?>
<h3 class="titleHeader muted"><a href="/home"><img width="48px" src="/img/logo_vma.png" /></a> <?= APPNAME; ?></h3>
<div class="navbar">
	<div class="navbar-inner">
		<div class="container">
		<div class="nav-collapse collapse navbar-responsive-collapse">
			<ul class="nav">
<!-- 				<li><a href="/home">Home</a></li> -->
			<?php 
				if(isset($_SESSION['user'])){
			?>
				<!-- Menu a discesa per il menu Overview -->
				<li class="dropdown"><a data-toggle="dropdown"
					class="dropdown-toggle" href="#">MenuOverView <b class="caret"></b>
				</a>
					<ul class="dropdown-menu">
						<li><a href="/xml/upload">Load</a></li>
<!-- 						<li><a href="#">Caricamento Manuale</a></li> -->
<!-- 						<li><a href="#">Edit Precharged</a></li> -->
					</ul>
				</li>
				<!-- 				<li><a href="/settings">Settings</a></li> -->
				<?php if($menu) { ?>
				<li id="viewHits" class="dropdown info"><a data-toggle="dropdown"
					class="dropdown-toggle" href="#">View Hits <b class="caret"></b>
				</a>
					<ul class="dropdown-menu">
						<li <?= $view=='hits' ? 'class="active"' : ''; ?>><a href="/xml/process/<?= $idmenu; ?>">All Hits</a></li>
						<li <?= $view=='call' ? 'class="active"' : ''; ?>><a href="/xml/processCalls/<?= $idmenu; ?>">Hits x call</a></li>
						<li <?= $view=='msisdn' ? 'class="active"' : ''; ?>><a href="/xml/ajaxHitsMsisdn/<?= $idmenu; ?>">Hits x MSISDN</a></li>
					</ul>
				</li>
				<li id="report" class="dropdown info"><a data-toggle="dropdown" class="dropdown-toggle" href="#">Report <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a id="mnExportPDF" data-export="pdf" class='btnExport' href="#">PDF</a></li>
						<li><a id="mnExportCSV" data-export="csv" class='btnExport' href="#">CSV</a></li>
						<li><a id="mnExportXLS" data-export="xls" class='btnExport' href="#">XLS</a></li>
					</ul>
				</li>
				<!-- 
				<li id="viewPath" class="dropdown info"><a data-toggle="dropdown"
					class="dropdown-toggle" href="#">View Path<b class="caret"></b>
				</a>
					<ul class="dropdown-menu">
						<li><a id="criticalPath" href="#">Critical Path</a>
						</li>
						<li><a href="#">Call Duration Path</a>
						</li>
					</ul>
				</li>
				 -->
				<?php } ?>
			</ul>
			<ul class="nav pull-right">
				<li class="dropdown">
					<a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class=" icon-user"></i> Welcome, <?= ucfirst($_SESSION['user']['name']); ?> <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="/users/logout">Log out</a></li>
					</ul>
				</li>
			</ul>
			<?php 
				}
			?>
			</div>
		</div>
	</div>
</div>
<?php 
	if($view!='home' && $view!='login' && $view!='restricted'){
?>
	<ul class="breadcrumb">
		<li><?= isset($nameMenu) ? $nameMenu : ($view=='upload' ? 'Load Menu' : ''); ?> <span class="divider">/</span></li>
		<li class="active">
		<?php 
			switch ($view){
				case 'hits':
						echo 'View Hits';
						break;
				case 'call':
					echo 'View Calls';
					break;
				case 'msisdn':
					echo 'View MSISDN';
					break;					
			}
		?>
		</li>
	</ul>
<?php } ?>