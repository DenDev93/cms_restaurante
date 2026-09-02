<?php 

$url = "pages?orderBy=order_page&orderMode=ASC";
$method = "GET";
$fields = array();

$pages  = CurlController::request($url,$method,$fields);

if($pages->status == 200){

	$pages = $pages->results;

	$childPages = array_filter($pages, function($page){
		return $page->parent_id_page > 0;
	});

}else{
	$pages = array();
}

?>

<div class="bg-white shadow border-end" id="sidebar-wrapper">

	<div class="sidebar-heading bg-white text-dark my-2">
		<?php echo $admin->symbol_admin ?>
		<span class="menu-text"><?php echo $admin->title_admin ?></span>
	</div>

	<hr class="mt-0 borderDashboard">

	<ul class="list-group list-group-flush" id="sortable">

		<?php if (!empty($pages)): ?>

			<?php foreach ($pages as $key => $value): ?>

				<?php if (isset($_SESSION["admin"])):  ?>
				
					
					<?php if ($_SESSION["admin"]->rol_admin == "superadmin" || 
					          $_SESSION["admin"]->rol_admin == "admin" || 
					          $_SESSION["admin"]->rol_admin == "editor" &&
					          isset(json_decode(urldecode($_SESSION["admin"]->permissions_admin), true)[$value->url_page]) &&
					          strtolower(json_decode(urldecode($_SESSION["admin"]->permissions_admin), true)[$value->url_page])== "on"):  ?>

						<?php if ($value->parent_id_page == 0): ?>
							
							<li class="list-group-item list-group-item-action position-relative" idPage="<?php echo base64_encode($value->id_page) ?>">

								<?php if ($value->type_page == "submenu"){ ?>

									<button class="bg-transparent text-dark border-0 p-0" data-bs-toggle="collapse" data-bs-target="#submenu<?php echo $key ?>">
										
										<i class="<?php echo $value->icon_page ?> textColor"></i> 
								 		<span class="menu-text"><?php echo $value->title_page ?></span>

								 		<span class="position-absolute rounded bg-transparent" style="right:5px; top:15px">
							 			
									 			<span class="text-muted rounded m-0 p-0 border-0">
									 			<i class="bi bi-chevron-down"></i>	
									 			</span>

							 			</span>

									</button>

									<div class="collapse pt-3" id="submenu<?php echo $key ?>">
										
										<ul class="list-group list-group-flush sub_sortable">
											
											<?php foreach ($childPages as $index => $item): ?>

												<?php if ($item->parent_id_page == $value->id_page): ?>

													<li class="list-group-item list-group-item-action position-relative" idPage="<?php echo base64_encode($item->id_page) ?>">

														<?php if ($item->type_page  == "external_link" || $item->type_page == "internal_link"): ?>

															<a class="bg-transparent text-dark" href="<?php echo urldecode($item->url_page) ?>" <?php if ($item->type_page == "external_link"): ?>  target="_blank" <?php endif ?>>

																<i class="<?php echo $item->icon_page ?> textColor"></i> 
														 		<span class="menu-text"><?php echo $item->title_page ?></span>

														 	</a>

														<?php else: ?>

															<a class="bg-transparent text-dark" href="/<?php echo $item->url_page ?>">

																<i class="<?php echo $item->icon_page ?> textColor"></i> 
														 		<span class="menu-text"><?php echo $item->title_page ?></span>

														 	</a>

														<?php endif ?>

														<?php if ($_SESSION["admin"]->rol_admin == "superadmin"): ?>

													 		<span class="position-absolute border rounded bg-white btnPages" style="right:5px; top:15px">
													 			
													 			<span class="btn btn-sm text-muted rounded m-0 p-0 border-0 sub_handle" style="cursor:move">
													 				<i class="bi bi-arrows-move m-1"></i>	
													 			</span>

													 			<button type="button" class="btn btn-sm text-muted rounded m-0 p-0 border-0 myPage" page='<?php echo json_encode($item) ?>'>
													 				<i class="bi bi-pencil-square m-1"></i>
													 			</button>

													 			<button type="button" class="btn btn-sm text-muted rounded m-0 p-0 border-0 deletePage" idPage=<?php echo base64_encode($item->id_page) ?>>
													 				<i class="bi bi-trash m-1"></i>
													 			</button>

													 		</span>

													 	<?php endif ?>

													</li>
													
												<?php endif ?>
												
											<?php endforeach ?>
										</ul>
									</div>
									

								<?php }else if ($value->type_page == "external_link" || $value->type_page == "internal_link"){ ?>

									<a class="bg-transparent text-dark" href="<?php echo urldecode($value->url_page) ?>" <?php if ($value->type_page == "external_link"): ?>  target="_blank" <?php endif ?>>

										<i class="<?php echo $value->icon_page ?> textColor"></i> 
								 		<span class="menu-text"><?php echo $value->title_page ?></span>

								 	</a>

								<?php }else{ ?>

									<a class="bg-transparent text-dark" href="/<?php echo $value->url_page ?>">

										<i class="<?php echo $value->icon_page ?> textColor"></i> 
								 		<span class="menu-text"><?php echo $value->title_page ?></span>

								 	</a>
									
								<?php } ?>
				
							 	<?php if ($_SESSION["admin"]->rol_admin == "superadmin"): ?>

							 		<span class="position-absolute border rounded bg-white btnPages" style="right:5px; top:15px">
							 			
							 			<span class="btn btn-sm text-muted rounded m-0 p-0 border-0 handle" style="cursor:move">
							 				<i class="bi bi-arrows-move m-1"></i>	
							 			</span>

							 			<button type="button" class="btn btn-sm text-muted rounded m-0 p-0 border-0 myPage" page='<?php echo json_encode($value) ?>'>
							 				<i class="bi bi-pencil-square m-1"></i>
							 			</button>

							 			<button type="button" class="btn btn-sm text-muted rounded m-0 p-0 border-0 deletePage" idPage=<?php echo base64_encode($value->id_page) ?>>
							 				<i class="bi bi-trash m-1"></i>
							 			</button>

							 		</span>

							 	<?php endif ?>

							</li>

						<?php endif ?>

					<?php endif ?>
				
				<?php else: ?>

					<?php if ($value->initial_page == 1): ?>

						<li class="list-group-item list-group-item-action position-relative">
							
							<a class="bg-transparent text-dark" href="/<?php echo $value->url_page ?>">

								<i class="<?php echo $value->icon_page ?> textColor"></i> 
						 		<span class="menu-text"><?php echo $value->title_page ?></span>

						 	</a>

						</li>
						
					<?php endif ?>

				<?php endif ?>
				
			<?php endforeach ?>
			
		<?php endif ?>

	</ul>

	<?php if (isset($_SESSION["admin"]) && $_SESSION["admin"]->rol_admin == "superadmin"): ?>

		<hr class="borderDashboard">

		<button class="btn btn-default border rounded btn-sm ms-3 menu-text mt-2 myPage">Agregar Página</button>
		
	<?php endif ?>

</div>