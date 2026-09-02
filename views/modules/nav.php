<?php

if(isset($_GET["office"])){

	$officeParts = explode("_",$_GET["office"]);

	if(isset($officeParts[0])){

		$_SESSION["admin"]->id_office_admin = $officeParts[0];

	}

	if(isset($officeParts[1])){

		$_SESSION["admin"]->title_office = $officeParts[1];

	}
	
}

?>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom d-lg-flex justify-content-lg-between">
					
	<div>
		<button class="ms-2 btn btn-default border-0" id="menu-toggle">
			<i class="bi bi-layout-sidebar"></i>
		</button>
	</div>

	<div class="d-flex">

		<div class="py-2 px-1">

			<?php if (isset($_SESSION["admin"]->id_office_admin) && $_SESSION["admin"]->id_office_admin > 0): ?>
			
			<a href="#myOffices" data-bs-toggle="modal" class="badge badge-default backColor small rounded px-3 py-2">
				<?php echo urldecode(isset($_SESSION["admin"]->title_office) ? $_SESSION["admin"]->title_office : "Sucursal ".$_SESSION["admin"]->id_office_admin) ?>
			</a>

			<?php else: ?>

				<a href="#myOffices" data-bs-toggle="modal" class="badge badge-default backColor small rounded px-3 py-2">
					Multi-Sucursal
				</a>

			<?php endif ?>

		</div>

		<div class="py-2 px-1">
			
			<a href="#" class="text-dark border rounded p-1" id="darkModeToggle">
				<i class="bi bi-sun"></i>
			</a>

		</div>

		<div class="p-2">
				
			<a href="#myProfile" data-bs-toggle="modal" style="color:inherit;">
				<i class="bi bi-person-circle"></i>
				<?php echo explode("@",$_SESSION["admin"]->email_admin)[0] ?>
			</a>

		</div>

		<div class="p-2 mx-2">
			
			<a href="/logout" class="text-dark">				
				<i class="bi bi-box-arrow-right"></i>
			</a>

		</div>

	</div>

</nav>

<?php 

if(!isset($_SESSION["admin"]->phone_office)){

	include "views/modules/modals/offices.php";

}

?>