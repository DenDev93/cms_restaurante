<?php

/*=============================================
Traer comandas
=============================================*/

$url = "relations?rel=orders,admins,tables&type=order,admin,table&linkTo=id_office_order,process_order&equalTo=".$_SESSION["admin"]->id_office_admin.",Preparando&orderBy=date_order&orderMode=ASC";
$method = "GET";
$fields = array();

$getOrders = CurlController::request($url,$method,$fields);

if($getOrders->status == 200){

  $orders = $getOrders->results;

}else{

	$orders = array();
}

?>

<style>
  
:root{

  --color-primario: <?php echo $admin->color_admin ?> !important; 
}

</style>


<link rel="stylesheet" href="/views/assets/css/pos/pos.css">

<?php if (!empty($orders)): ?>

	<?php foreach ($orders as $key => $value): ?>

		<?php 

		/*=============================================
	    Capturar items de ventas
	    =============================================*/

	    $url = "relations?rel=sales,foods&type=sale,food&linkTo=id_order_sale&equalTo=".$value->id_order;
	    $method = "GET";
	    $fields = array();

	    $getSales = CurlController::request($url,$method,$fields);

	    if($getSales->status == 200){

	    	$sales = $getSales->results;

	    }else{

	    	$sales = array();
	    }

	    /*=============================================
	    Contar estados de los items
	    =============================================*/

	    $countLista = 0;
	    $countEntregada = 0;

	    foreach ($sales as $index => $item) {

	    	if($item->process_sale == "Lista"){

	    		$countLista++;
	    	}

	    	if($item->process_sale == "Entregada"){

	    		$countEntregada++;
	    	}
	    }

	    $todosServidos = !empty($sales) && $countEntregada == count($sales);

		?>
	
		<div class="col-12 col-lg-6 col-xl-3 mb-3 position-relative">
			
			<div class="card rounded">
				
				<div class="card-header">
					
						<h3 class="card-title text-center mt-1"><?php echo urldecode($value->title_table) ?></h3>

						<?php if ($todosServidos): ?>

							<span class="badge bg-success w-100">Servida a la mesa</span>

						<?php elseif ($countLista > 0): ?>

							<span class="badge bg-info w-100"><?php echo $countLista ?> item(s) listo(s)</span>

						<?php else: ?>

							<span class="badge bg-warning text-dark w-100">En preparación</span>

						<?php endif ?>

				</div>

				<div class="card-body">

					<div class="order-summary-header">
						
						<h4 id="transactionOrder pb-0" idorder="1">Orden # <?php echo $value->transaction_order ?></h4>
						<p class="text-center p-0"><?php echo TemplateController::formatDate(4,$value->date_order) ?></p>

					</div>

					<div class="small text-center bg-gray rounded px-3 py-1 mb-3">Vendedor: <?php echo explode("@",$value->email_admin)[0] ?> </div>

					<div class="order-items" id="order-items">
						
						<?php if (!empty($sales)): ?>

							<?php foreach ($sales as $index => $item): ?>

								<div class="order-item">
									
									<div class="order-item-header">
										
										<span class="order-item-name"><?php echo urldecode($item->title_food) ?></span>
										
										<div class="order-item-controls">
											
											<span class="quantity-display">x<?php echo $item->qty_sale ?></span>

											<?php if ($item->process_sale == "Preparando"): ?>

												<button class="ms-2 bg-light border-0 p-0 rounded changeProcessItem px-1" idSale="<?php echo $item->id_sale ?>">
													<div class="spinner-border spinner-border-sm"></div>
												</button>

											<?php endif ?>

											<?php if ($item->process_sale == "Lista"): ?>

												<button class="ms-2 bg-info border-0 p-0 rounded  px-1">
													<i class="fa-solid fa-check"></i>
												</button>

											<?php endif ?>

											<?php if ($item->process_sale == "Entregada"): ?>

												<button class="ms-2 bg-success border-0 p-0 rounded  px-1">
													<i class="fa-solid fa-check"></i>
												</button>

											<?php endif ?>

										</div>


									</div>

								</div>


							<?php endforeach ?>

						<?php endif ?>	


					</div>

					<div class="order-notes">
						<textarea class="form-control" id="note_order" rows="3" readonly><?php echo $value->note_order ?></textarea>
					</div>

					<div class="order-actions">
						<a href="/comandas?idOrder=<?php echo $value->id_order ?>" target="_blank" class="btn btn-success btn-lg w-100 mb-2">
							<i class="bi bi-printer"></i> Imprimir Comanda
						</a>


					</div>



				</div>

			</div>

		</div>

	<?php endforeach ?>

<?php else: ?>

	<div class="col-12">
		<div class="mt-4 p-5 bg-white border text-dark rounded">
			<h1><i class="fa-solid fa-kitchen-set"></i> No tienes comandas para preparar</h1>
			<p>Organiza la cocina...</p>
		</div>
	</div>
	
<?php endif ?>

<script src="/views/assets/js/pos/pos.js"></script>