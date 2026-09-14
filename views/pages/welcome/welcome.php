<div class="container-fluid">

	<div class="card rounded mb-3 p-3 d-flex flex-row align-items-center justify-content-between">

		<div>
			<h1 class="textColor mb-0">BIENVENIDO</h1>
			<h4 class="mb-0"><?php echo $admin->symbol_admin ?> <?php echo $admin->title_admin ?></h4>
		</div>

		<span class="badge badge-default backColor small rounded px-3 py-2"><?php echo date("d/m/Y") ?></span>

	</div>

	<div class="row">

		<?php include __DIR__ . "/../dynamic/custom/mesas/mesas.php" ?>

	</div>

</div>