<?php

$tables = array();

if($_SESSION["admin"]->id_office_admin > 0){

	$url = "tables?linkTo=id_office_table&equalTo=".$_SESSION["admin"]->id_office_admin;
	$method = "GET";
  	$fields = array();

  	$getTables = CurlController::request($url,$method,$fields);
  	
  	if($getTables !== null && $getTables->status == 200){

		$tables = $getTables->results;
		$notFree = 0;

		foreach ($tables as $key => $value) {

			if($value->status_table != "libre"){

				$notFree++;
			}
			
		}

		/*=============================================
    	Traer Reservas
    	=============================================*/

    	$url = "books?linkTo=date_book,id_office_book&equalTo=".date("Y-m-d").",".$_SESSION["admin"]->id_office_admin;
    	$method = "GET";
    	$fields = array();

    	$getBooks = CurlController::request($url,$method,$fields);

    	if($getBooks !== null && $getBooks->status == 200){

	      $books = $getBooks->results;

	    }else{

	      $books = array();
	    }

  	}else{

  		$tables = array();	
  	}

}else{

	echo '<script>
    setTimeout(()=>{

        $("#myOffices").modal("show");

    },100);
  </script>';

}

?>

<?php if(!empty($tables)): ?>

<link rel="stylesheet" href="/views/assets/css/mesas/mesas.css" >

<div class="col-12 mb-3 position-relative">
	
	<div class="card rounded">
		
		<div class="card-header d-flex justify-content-between align-items-center">
	      <h3 class="card-title">Gestión de Mesas</h3>
	      <div class="d-flex align-items-center">
	        <i class="fa-solid fa-chair me-2"></i>
	        <span class="badge bg-secondary"><?php echo $notFree ?>/<?php echo count($tables) ?> Mesas ocupadas</span>
	      </div>
	    </div>


	    <div class="card-body">
	    	
	    	<div class="row mb-4">
	          <div class="col-12">
	            <div class="d-flex flex-wrap gap-3 justify-content-center">
	              <div class="d-flex align-items-center">
	                <div class="legend-dot bg-success me-2"></div>
	                <span class="small">Libre</span>
	              </div>
	              <div class="d-flex align-items-center">
	                <div class="legend-dot bg-warning me-2"></div>
	                <span class="small">Ocupada</span>
	              </div>
	              <div class="d-flex align-items-center">
	                <div class="legend-dot bg-info me-2"></div>
	                <span class="small">Pagando</span>
	              </div>
	              <div class="d-flex align-items-center">
	                <div class="legend-dot bg-purple me-2"></div>
	                <span class="small">Reservada</span>
	              </div>
	            </div>
	          </div>
	        </div>

	        <div class="row g-3">
	        	
	        	<?php 
				$time_book = null;
				foreach ($tables as $key => $value): ?>

	        		<?php

	        		/*=============================================
          			Cambiar estado de mesa reservada
          			=============================================*/

          			if (!empty($books)){

          				foreach ($books as $index => $item) {

          					$beforeTime = new DateTime($item->time_book);
          					$beforeTime->modify('-1 hour');
          					

          					$afterTime = new DateTime($item->time_book);
              				$afterTime->modify('+1 hour');
              				

              				if($item->id_table_book == $value->id_table &&
              				   date("H:i:s")  > $beforeTime->format('H:i:s') &&
              				   date("H:i:s") < $afterTime->format('H:i:s') &&
              				   $value->status_table == "libre"){

              					$value->status_table = "reservada";
              					$time_book = TemplateController::formatDate(6,$item->time_book);
              				}

          				}

          			}

	        		?>

	        		<div class="col-lg-4 col-md-6">
			          <div class="table-card
			          <?php if ($value->status_table == "libre"): ?>
			          	table-free
			          <?php endif ?>
			          <?php if ($value->status_table == "ocupada"): ?>
			          	table-occupied
			          <?php endif ?>
			          <?php if ($value->status_table == "pagando"): ?>
			          	table-waiting
			          <?php endif ?>
			          <?php if ($value->status_table == "reservada"): ?>
			          	table-reserved
			          <?php endif ?>
			           ">
			            <div class="table-header">
			              <h5 class="table-number">
			              	<span class="pe-1"><?php echo urldecode($value->icon_table) ?></span> 
			              	<?php echo urldecode($value->title_table) ?>
			              </h5>
			              <span class="table-status">
			                  <?php if ($value->status_table == "libre"): ?>
			                   Libre
			                 <?php endif ?>
			                 <?php if ($value->status_table == "ocupada"): ?>
			                   Ocupada
			                 <?php endif ?>
			                 <?php if ($value->status_table == "pagando"): ?>
			                   Pagando
			                 <?php endif ?>
			                 <?php if ($value->status_table == "reservada"): ?>
			                  Reservada
			                 <?php endif ?>

			                </span>
			            </div>
			            <div class="table-info">
			              <p class="seats-info">Personas: <?php echo $value->people_table ?></p>

<?php if ($value->status_table == "ocupada"): ?>

			              	<?php 

			              		$url = "orders?linkTo=id_table_order,status_order&equalTo=".$value->id_table.",Pendiente&select=transaction_order,date_order,id_order,process_order&startAt=0&endAt=1&orderBy=id_order&orderMode=DESC";
		
			              		$method = "GET";
                  				$fields = array();

                  				$getOrder = CurlController::request($url,$method,$fields);

                  				$orderState = "";
                  				$countLista = 0;
                  				$countEntregada = 0;
                  				$totalItems = 0;
                  				$orderTxn = "";
                  				$orderDate = "";

                  				if($getOrder->status == 200 && !empty($getOrder->results)){

                  					$orderState = $getOrder->results[0]->process_order;
                  					$orderTxn = $getOrder->results[0]->transaction_order;
                  					$orderDate = $getOrder->results[0]->date_order;

                  					$url = "relations?rel=sales,foods&type=sale,food&linkTo=id_order_sale&equalTo=".$getOrder->results[0]->id_order;
                  					$method = "GET";
                  					$fields = array();

                  					$getSales = CurlController::request($url,$method,$fields);

                  					if($getSales->status == 200){

                  						$totalItems = count($getSales->results);

                  						foreach ($getSales->results as $index => $item) {

                  							if($item->process_sale == "Lista"){

                  								$countLista++;
                  							}

                  							if($item->process_sale == "Entregada"){

                  								$countEntregada++;
                  							}
                  						}
                  					}
                  				}

			              	?>

<?php if ($orderTxn !== ""): ?>

			              	<div class="d-flex justify-content-between align-items-center mb-2">
			              		
			              		<span class="party-info">Orden # <?php echo $orderTxn ?></span>
			              		<span
			              		class="time-info" 
			              		index="<?php echo $key ?>"
			              		startTime="<?php echo $orderDate ?>"
			              		endTime="<?php echo date("Y-m-d H:i:s") ?>"></span>  

			              	</div>

<?php if ($orderState == "Pendiente" || $orderState == "Ordenando"): ?>
	              		<span class="badge bg-secondary w-100 mb-2">Pendiente</span>
	              	<?php endif ?>

	              	<?php if ($orderState == "Preparando" && $countLista == 0 && $countEntregada < 1): ?>
	              		<span class="badge bg-warning text-dark w-100 mb-2">En cocina</span>
	              	<?php endif ?>

	              	<?php if ($countLista > 0 && $countLista < $totalItems): ?>
	              		<span class="badge bg-info w-100 mb-2"><?php echo $countLista ?> item(s) listo(s)</span>
	              	<?php endif ?>

	              	<?php if ($countLista > 0 && $countLista == $totalItems): ?>
	              		<span class="badge bg-info w-100 mb-2">Listo para servir</span>
	              	<?php endif ?>

<?php if ($countEntregada > 0 && $totalItems > 0 && $countEntregada == $totalItems): ?>
	              		<span class="badge bg-success w-100 mb-2">Servida a la mesa</span>
	              	<?php endif ?>

			              <?php endif ?>

			              <?php endif ?>


			            </div>
			            <div class="table-actions">
			            	<?php if ($value->status_table == "libre"): ?>
			            		<a href="/pos?idTable=<?php echo $value->id_table ?>&titleTable=<?php echo $value->title_table ?>" class="btn btn-light btn-sm w-100 seat-guests-btn">
			            			Tomar mesa
			            		</a>
				            <?php endif ?>
				            <?php if ($value->status_table == "ocupada"): ?>
			            		<a href="/pos?idTable=<?php echo $value->id_table ?>&titleTable=<?php echo $value->title_table ?>" class="btn btn-light btn-sm w-100 seat-guests-btn">
			            			Ver Orden
			            		</a>
				            <?php endif ?>
				            <?php if ($value->status_table == "reservada"): ?>
								<a href="#" class="btn btn-light btn-sm w-100 seat-guests-btn">
			            			Tomar Reserva a las <?php echo $time_book ?>
			            		</a>
				            <?php endif ?>
				            <?php if ($value->status_table == "pagando"): ?>
								<button class="btn btn-info btn-sm w-100 rounded">
				                    <span class="position-relative" style="bottom:2px">En Proceso de Pago</span> <div class="spinner-border spinner-border-sm"></div>
			                  	</button>
				            <?php endif ?>

			            </div>
			          </div>
			        </div>

	        	<?php endforeach ?>



	        </div>

	    </div>

	</div>

</div>

<script src="/views/assets/js/mesas/mesas.js"></script>

<?php else: ?>

	<div class="col-12 text-center py-4">
		<p class="text-muted mb-0">No hay mesas registradas para esta sucursal.</p>
	</div>

<?php endif ?>