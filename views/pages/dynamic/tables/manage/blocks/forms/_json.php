<?php if ($module->columns[$i]->type_column == "json" || $module->columns[$i]->type_column == "json_ingredients" || $module->columns[$i]->type_column == "json_products"): ?>

	<!--====================================
	Validamos si el formulario ya viene con información guardada en base de datos
	======================================-->

	<?php if (!empty($data) && $data[$module->columns[$i]->title_column] != null): $arrayObj = new ArrayObject(json_decode(urldecode($data[$module->columns[$i]->title_column])));?>

		<?php if (!empty($arrayObj) && $arrayObj->count() > 0): ?>

			<?php foreach ($arrayObj as $key => $value): ?>

				<!--====================================
				Preguntamos si el tipo de formulario es de ingredientes
				======================================-->

				<?php if ($module->columns[$i]->type_column == "json_ingredients" || $module->columns[$i]->type_column == "json_products"): ?>

					<div class="rounded p-2 border mb-3 jsonGroup <?php echo $module->columns[$i]->title_column ?>" position="<?php echo $key ?>_">

						<?php

							$jsonIngredients = ["descripcion","sku","medida","cantidad"];

						?>

						<?php foreach ($jsonIngredients as $index => $item): ?>

							<div class="itemsJson">

								<div class="row row-cols-1 row-cols-sm-2 itemJson">

									<div class="col">
									
										<div class="form-floating mb-3">
											
											<input 
											type="text"
											class="form-control rounded <?php echo $key ?>_propertyJson <?php echo $module->columns[$i]->title_column ?>"
											id="<?php echo $key ?>_<?php echo $item ?>_propertyJson"
											onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')"
											value="<?php echo $item ?>"
											readonly
											>

											<label for="<?php echo $key ?>_<?php echo $item ?>_propertyJson">Propiedad</label>

										</div>

									</div>

									<div class="col">
										
										<div class="form-floating mb-3">

											<?php if ($item == "descripcion"){ ?>

												<?php

												$url = explode("_",$module->columns[$i]->type_column)[1];
												$method ="GET";
												$fields = array();

												$getIngredients = CurlController::request($url,$method,$fields);

												if($getIngredients->status == 200){

													$ingredients = $getIngredients->results;

												}else{

													$ingredients = array();
												}

												if(explode("_",$module->columns[$i]->type_column)[1] == "ingredients"){

													$suffix = "ingredient";

												}else{

													$suffix = "product";
												}



												?>

												<select class="form-select rounded position-relative <?php echo $key ?>_valueJson <?php echo $module->columns[$i]->title_column ?> changeIngredient"
													onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')">
													
													<option value="">Seleccione</option>

													<?php if (!empty($ingredients)): ?>

														<?php foreach (json_decode(json_encode($ingredients),true) as $num => $elem):  ?>

															<option value="<?php echo $elem["title_".$suffix] ?>^<?php echo $elem["sku_".$suffix] ?>^<?php echo $elem["type_".$suffix] ?>"
																<?php if (urldecode(explode("^",json_decode(json_encode($arrayObj[$key]),true)[$item])[0]) == urldecode($elem["title_".$suffix])): ?>
																selected	
																<?php endif ?>
															>
																<?php echo urldecode($elem["title_".$suffix]) ?>		
															</option>
															
														<?php endforeach ?>
														
													<?php endif ?>
												</select>

											<?php }else if($item == "sku"){ ?>

												<input 
												type="text"
												class="form-control rounded position-relative <?php echo $key ?>_valueJson <?php echo $module->columns[$i]->title_column ?> sku"
												id="<?php echo $key ?>_sku_valueJson"
												onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')"
												value="<?php echo json_decode(json_encode($arrayObj[$key]),true)[$item] ?>"
												readonly>

												<label for="<?php echo $key ?>_sku_valueJson">Valor</label>

											<?php }else if($item == "medida"){ ?>

												<input 
												type="text"
												class="form-control rounded position-relative <?php echo $key ?>_valueJson <?php echo $module->columns[$i]->title_column ?> medida"
												id="<?php echo $key ?>_medida_valueJson"
												onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')"
												value="<?php echo json_decode(json_encode($arrayObj[$key]),true)[$item] ?>"
												readonly
												>

												<label for="<?php echo $key ?>_medida_valueJson">Valor</label>

											<?php }else{ ?>

												<input 
												type="text"
												class="form-control rounded position-relative <?php echo $key ?>_valueJson <?php echo $module->columns[$i]->title_column ?>"
												id="<?php echo $key ?>_<?php echo $item ?>_valueJson"
												onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')"
												value="<?php echo json_decode(json_encode($arrayObj[$key]),true)[$item] ?>"
												>

												<label for="<?php echo $key ?>_<?php echo $item ?>_valueJson">Valor</label>

												
											<?php } ?>


										</div>
										
									</div>

								</div>
								

							</div>

						<?php endforeach ?>
						
						<button type="button" class="btn btn-sm btn-default border rounded  float-end" onclick="removeJsonGroup('<?php echo $module->columns[$i]->title_column ?>','<?php echo $key ?>_',event)">
							<small>Remove Group</small>
						</button>
						<div class="clearfix"></div>	

					</div>

				<!--====================================
				Preguntamos si el tipo de formulario es JSON normal
				======================================-->

				<?php else: ?>

					<div class="rounded p-2 border mb-3 jsonGroup <?php echo $module->columns[$i]->title_column ?>" position="<?php echo $key ?>_">

						<?php foreach ($value as $index => $item): ?>
			
							<div class="itemsJson">

								<div class="row row-cols-1 row-cols-sm-2 itemJson">

									<div class="col">
									
										<div class="form-floating mb-3">
											
											<input 
											type="text"
											class="form-control rounded <?php echo $key ?>_propertyJson <?php echo $module->columns[$i]->title_column ?>"
											id="<?php echo $key ?>_propertyJson"
											value="<?php echo $index ?>"
											onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')" 
											>

											<label for="<?php echo $key ?>_propertyJson">Propiedad</label>

										</div>

									</div>

									<div class="col">
										
										<div class="form-floating mb-3">
											
											<input 
											type="text"
											class="form-control rounded position-relative <?php echo $key ?>_valueJson <?php echo $module->columns[$i]->title_column ?>"
											id="<?php echo $key ?>_valueJson"
											value="<?php echo htmlspecialchars($item) ?>" 
											onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')"
											>

											<label for="<?php echo $key ?>_valueJson">Valor</label>

											<button type="button" class="btn btn-sm position-absolute" style="top:0; right:0;" onclick="removeJson('<?php echo $module->columns[$i]->title_column ?>', '_<?php echo array_search($index,array_keys(json_decode(json_encode($value),true))) ?>',event)">
												<i class="bi bi-x"></i>
											</button>

										</div>
										
									</div>

								</div>

							</div>

						<?php endforeach ?>

						<button type="button" class="btn btn-sm btn-default backColor rounded addJson float-start">
							<small>Add Item</small>
						</button>
						<button type="button" class="btn btn-sm btn-default border rounded  float-end" onclick="removeJsonGroup('<?php echo $module->columns[$i]->title_column ?>','<?php echo $key ?>_',event)">
							<small>Remove Group</small>
						</button>
						<div class="clearfix"></div>

					</div>

				<?php endif ?>

			<?php endforeach ?>	


		<?php else: ?>

			<div class="rounded p-2 border mb-3 jsonGroup <?php echo $module->columns[$i]->title_column ?>" position="0_" titleColumn="">
				
				<div class="itemsJson">

					<div class="row row-cols-1 row-cols-sm-2 itemJson">

						<div class="col">
						
							<div class="form-floating mb-3">
								
								<input 
								type="text"
								class="form-control rounded  0_propertyJson <?php echo $module->columns[$i]->title_column ?>"
								id="0_propertyJson"
								onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')"
								>

								<label for="0_propertyJson">Propiedad</label>

							</div>

						</div>

						<div class="col">
							
							<div class="form-floating mb-3">
								
								<input 
								type="text"
								class="form-control rounded position-relative 0_valueJson <?php echo $module->columns[$i]->title_column ?>"
								id="0_valueJson"
								onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')"
								>

								<label for="0_valueJson">Valor</label>

								<button type="button" class="btn btn-sm position-absolute" style="top:0; right:0;" onclick="removeJson('<?php echo $module->columns[$i]->title_column ?>', '_0',event)">
									<i class="bi bi-x"></i>
								</button>

							</div>
							
						</div>

					</div>
					

				</div>

				<button type="button" class="btn btn-sm btn-default backColor rounded addJson float-start">
					<small>Add Item</small>
				</button>
				<button type="button" class="btn btn-sm btn-default border rounded  float-end" onclick="removeJsonGroup('<?php echo $module->columns[$i]->title_column ?>','0_',event)">
					<small>Remove Group</small>
				</button>
				<div class="clearfix"></div>

			</div>

		<?php endif ?>	

	<!--====================================
	Mostramos el formulario vacío
	======================================-->

	<?php else: ?>	

		<!--====================================
		Preguntamos si el tipo de formulario es de ingredientes
		======================================-->

		<?php if ($module->columns[$i]->type_column == "json_ingredients" || $module->columns[$i]->type_column == "json_products"): ?>

			<div class="rounded p-2 border mb-3 jsonGroup <?php echo $module->columns[$i]->title_column ?>" position="0_">

				<?php

					$jsonIngredients = ["descripcion","sku","medida","cantidad"];

				?>

				<?php foreach ($jsonIngredients as $key => $value): ?>

					<div class="itemsJson">

						<div class="row row-cols-1 row-cols-sm-2 itemJson">

							<div class="col">
							
								<div class="form-floating mb-3">
									
									<input 
									type="text"
									class="form-control rounded 0_propertyJson <?php echo $module->columns[$i]->title_column ?>"
									id="0_<?php echo $value ?>_propertyJson"
									onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')"
									value="<?php echo $value ?>"
									readonly
									>

									<label for="0_<?php echo $value ?>_propertyJson">Propiedad</label>

								</div>

							</div>

							<div class="col">
								
								<div class="form-floating mb-3">

									<?php if ($value == "descripcion"){ ?>

										<?php

										$url = explode("_",$module->columns[$i]->type_column)[1];
										$method ="GET";
										$fields = array();

										$getIngredients = CurlController::request($url,$method,$fields);

										if($getIngredients->status == 200){

											$ingredients = $getIngredients->results;

										}else{

											$ingredients = array();
										}

										if(explode("_",$module->columns[$i]->type_column)[1] == "ingredients"){

											$suffix = "ingredient";

										}else{

											$suffix = "product";
										}

										?>

										<select class="form-select rounded position-relative 0_valueJson <?php echo $module->columns[$i]->title_column ?> changeIngredient" onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')">
											
											<option value="">Seleccione</option>

											<?php if (!empty($ingredients)): ?>

												<?php foreach (json_decode(json_encode($ingredients),true) as $num => $elem):  ?>

													<option value="<?php echo $elem["title_".$suffix] ?>^<?php echo $elem["sku_".$suffix] ?>^<?php echo $elem["type_".$suffix] ?>"><?php echo urldecode($elem["title_".$suffix]) ?></option>
													
												<?php endforeach ?>
												
											<?php endif ?>
										</select>

									<?php }else if($value == "sku"){ ?>

										<input 
										type="text"
										class="form-control rounded position-relative 0_valueJson <?php echo $module->columns[$i]->title_column ?> sku"
										id="0_sku_valueJson"
										onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')"
										readonly>

										<label for="0_sku_valueJson">Valor</label>

									<?php }else if($value == "medida"){ ?>

										<input 
										type="text"
										class="form-control rounded position-relative 0_valueJson <?php echo $module->columns[$i]->title_column ?> medida"
										id="0_medida_valueJson"
										onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')"
										readonly
										>

										<label for="0_medida_valueJson">Valor</label>

									<?php }else{ ?>

										<input 
										type="text"
										class="form-control rounded position-relative 0_valueJson <?php echo $module->columns[$i]->title_column ?>"
										id="0_<?php echo $value ?>_valueJson"
										onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')">

										<label for="0_<?php echo $value ?>_valueJson">Valor</label>

										
									<?php } ?>


								</div>
								
							</div>

						</div>
						

					</div>
					
				<?php endforeach ?>

				<button type="button" class="btn btn-sm btn-default border rounded float-end" onclick="removeJsonGroup('<?php echo $module->columns[$i]->title_column ?>','0_',event)">
					<small>Remove Group</small>
				</button>

				<div class="clearfix"></div>

			</div>

		
		<!--====================================
		Preguntamos si el tipo de formulario es JSON normal
		======================================-->

		<?php else: ?>

			<div class="rounded p-2 border mb-3 jsonGroup <?php echo $module->columns[$i]->title_column ?>" position="0_">
				
				<div class="itemsJson">

					<div class="row row-cols-1 row-cols-sm-2 itemJson">

						<div class="col">
						
							<div class="form-floating mb-3">
								
								<input 
								type="text"
								class="form-control rounded 0_propertyJson <?php echo $module->columns[$i]->title_column ?>"
								id="0_propertyJson"
								onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')"
								>

								<label for="0_propertyJson">Propiedad</label>

							</div>

						</div>

						<div class="col">
							
							<div class="form-floating mb-3">
								
								<input 
								type="text"
								class="form-control rounded position-relative 0_valueJson <?php echo $module->columns[$i]->title_column ?>"
								id="0_valueJson"
								onchange="changeItemJson('<?php echo $module->columns[$i]->title_column ?>')">

								<label for="0_valueJson">Valor</label>

								<button type="button" class="btn btn-sm position-absolute" style="top:0; right:0;" onclick="removeJson('<?php echo $module->columns[$i]->title_column ?>', '_0',event)">
									<i class="bi bi-x"></i>
								</button>

							</div>
							
						</div>

					</div>
					

				</div>

				<button type="button" class="btn btn-sm btn-default backColor rounded addJson float-start">
					<small>Add Item</small>
				</button>
				<button type="button" class="btn btn-sm btn-default border rounded float-end" onclick="removeJsonGroup('<?php echo $module->columns[$i]->title_column ?>','0_',event)">
					<small>Remove Group</small>
				</button>
				<div class="clearfix"></div>

			</div>

		<?php endif ?>

	<?php endif ?>

	<button type="button" class="btn btn-sm btn-default backColor rounded addJsonGroup float-end">
		<small>Add Group</small>
	</button>

	<?php if (!empty($data)): ?>

		<input type="hidden" name="<?php echo $module->columns[$i]->title_column ?>" id="<?php echo $module->columns[$i]->title_column ?>" value='<?php echo urldecode($data[$module->columns[$i]->title_column]) ?>'>

	<?php else: ?>

		<input type="hidden" name="<?php echo $module->columns[$i]->title_column ?>" id="<?php echo $module->columns[$i]->title_column ?>" value='[]'>

	<?php endif ?>	

<?php endif ?>