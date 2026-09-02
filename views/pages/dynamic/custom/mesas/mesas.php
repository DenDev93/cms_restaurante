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

/*=============================================
Traer Sucursales para el formulario de nueva mesa
=============================================*/

$url = "offices?select=id_office,title_office";
$method = "GET";
$fields = array();

$getOffices = CurlController::request($url,$method,$fields);

if($getOffices !== null && $getOffices->status == 200){

	$offices = $getOffices->results;

}else{

	$offices = array();
}

?>

<?php if(!empty($tables)): ?>

<link rel="stylesheet" href="/views/assets/css/mesas/mesas.css" >

<div class="col-12 mb-3 position-relative">
	
	<div class="card rounded">
		
		<div class="card-header d-flex mesas-header justify-content-between align-items-center">
	      <h3 class="card-title">Gestión de Mesas</h3>
	      <div class="d-flex mesas-header-actions align-items-center">
	        <button class="btn btn-success btn-sm rounded px-3 py-2 me-2" data-bs-toggle="modal" data-bs-target="#modalAddTable">
	          <i class="fa-solid fa-plus me-1"></i> Agregar Mesa
	        </button>
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
			              <span class="d-flex align-items-center gap-1">
			              	<button type="button"
			              		class="btn btn-sm btn-edit-table table-status edit-table-btn"
			              		title="Editar mesa"
			              		data-id="<?php echo $value->id_table ?>"
			              		data-title="<?php echo htmlspecialchars(urldecode($value->title_table), ENT_QUOTES) ?>"
			              		data-icon="<?php echo htmlspecialchars(urldecode($value->icon_table), ENT_QUOTES) ?>"
			              		data-people="<?php echo $value->people_table ?>"
			              		data-status="<?php echo $value->status_table ?>"
			              		data-office="<?php echo $value->id_office_table ?>">
			              		<i class="fa-solid fa-gear"></i>
			              	</button>
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
			              </span>
			            </div>
			            <div class="table-info">
			              <p class="seats-info seats-people">Personas: <?php echo $value->people_table ?></p>

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

<!--====================================
Modal para agregar nueva mesa
====================================-->

<div class="modal fade" id="modalAddTable" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded">
      <form method="POST" class="needs-validation" novalidate id="formAddTable">

        <div class="modal-header border-0">
          <h5 class="modal-title">Agregar Nueva Mesa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="form-group mb-3">
            <label for="title_table" class="form-label">Nombre de la Mesa</label>
            <input type="text" class="form-control rounded" id="title_table" name="title_table" placeholder="Ej: Mesa 8" required>
          </div>

          <div class="form-group mb-3">
            <label for="icon_table" class="form-label">Icono (HTML)</label>
            <textarea class="form-control rounded" id="icon_table" name="icon_table" rows="2" placeholder='Ej: <i class="fa-solid fa-chair"></i>'></textarea>
          </div>

          <div class="form-group mb-3">
            <label for="people_table" class="form-label">Personas</label>
            <input type="number" class="form-control rounded" id="people_table" name="people_table" value="4" min="1" required>
          </div>

          <div class="form-group mb-3">
            <label for="status_table" class="form-label">Estado</label>
            <select class="form-select rounded select2" id="status_table" name="status_table">
              <option value="libre" selected>libre</option>
              <option value="ocupada">ocupada</option>
              <option value="pagando">pagando</option>
              <option value="reservada">reservada</option>
            </select>
          </div>

          <div class="form-group mb-3">
            <label for="id_office_table" class="form-label">Sucursal</label>
            <select class="form-select rounded select2" id="id_office_table" name="id_office_table" <?php if ($_SESSION["admin"]->id_office_admin > 0): ?> disabled <?php endif ?>>
              <?php foreach ($offices as $office): ?>
                <?php $selectedOffice = ($_SESSION["admin"]->id_office_admin > 0 && $office->id_office == $_SESSION["admin"]->id_office_admin) ? "selected" : ""; ?>
                <option value="<?php echo $office->id_office ?>" <?php echo $selectedOffice ?>><?php echo urldecode($office->title_office) ?></option>
              <?php endforeach ?>
            </select>
            <?php if ($_SESSION["admin"]->id_office_admin > 0): ?>
              <input type="hidden" name="id_office_table" value="<?php echo $_SESSION["admin"]->id_office_admin ?>">
            <?php endif ?>
          </div>

        </div>

        <div class="modal-footer border-0">
          <button type="button" class="btn btn-dark rounded" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success rounded">Guardar Mesa</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!--====================================
Modal para editar mesa
====================================-->
<input type="hidden" id="adminIsSuper" value="<?php echo $_SESSION["admin"]->rol_admin == "superadmin" ? 1 : 0 ?>">

<div class="modal fade" id="modalEditTable" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded edit-table-modal">
      <form method="POST" class="needs-validation" novalidate id="formEditTable">

        <div class="modal-header border-0">
          <h5 class="modal-title d-flex align-items-center gap-2">
            <i class="fa-solid fa-sliders"></i> Editar Mesa
            <span class="badge bg-dark rounded-pill edit-badge-id ms-1">#</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" id="edit_id_table" name="id_table">
          <input type="hidden" id="edit_old_status" name="old_status">

          <div class="row g-3">

            <div class="col-md-7">

              <div class="form-group mb-3">
                <label for="edit_title_table" class="form-label">Nombre de la Mesa</label>
                <div class="input-group">
                  <span class="input-group-text rounded-start bg-white"><i class="fa-solid fa-signature"></i></span>
                  <input type="text" class="form-control rounded-end" id="edit_title_table" name="title_table" required>
                </div>
              </div>

              <div class="form-group mb-3">
                <label class="form-label d-block">Estado de la Mesa</label>
                <div class="btn-group w-100 edit-status-group" role="group">
                  <input type="radio" class="btn-check" name="edit_status_radio" id="edit-status-libre" value="libre">
                  <label class="btn edit-status-btn edit-status-libre" for="edit-status-libre"><i class="fa-solid fa-circle me-1"></i>Libre</label>
                  <input type="radio" class="btn-check" name="edit_status_radio" id="edit-status-ocupada" value="ocupada">
                  <label class="btn edit-status-btn edit-status-ocupada" for="edit-status-ocupada"><i class="fa-solid fa-circle me-1"></i>Ocupada</label>
                  <input type="radio" class="btn-check" name="edit_status_radio" id="edit-status-pagando" value="pagando">
                  <label class="btn edit-status-btn edit-status-pagando" for="edit-status-pagando"><i class="fa-solid fa-circle me-1"></i>Pagando</label>
                  <input type="radio" class="btn-check" name="edit_status_radio" id="edit-status-reservada" value="reservada">
                  <label class="btn edit-status-btn edit-status-reservada" for="edit-status-reservada"><i class="fa-solid fa-circle me-1"></i>Reservada</label>
                </div>
              </div>

              <div class="form-group mb-3">
                <label class="form-label d-block">Icono de la Mesa</label>
                <div class="input-group mb-2">
                  <span class="input-group-text rounded-start bg-white px-3 icon-preview" id="edit_icon_preview"><i class="fa-solid fa-chair"></i></span>
                  <input type="text" class="form-control rounded-end" id="edit_icon_table" name="icon_table" placeholder='Ej: <i class="fa-solid fa-chair"></i>'>
                </div>
                <div class="edit-icon-quick">
                  <button type="button" class="btn btn-sm icon-option" data-icon='<i class="fa-solid fa-chair"></i>'><i class="fa-solid fa-chair"></i></button>
                  <button type="button" class="btn btn-sm icon-option" data-icon='<i class="fa-solid fa-couch"></i>'><i class="fa-solid fa-couch"></i></button>
                  <button type="button" class="btn btn-sm icon-option" data-icon='<i class="fa-solid fa-people-group"></i>'><i class="fa-solid fa-people-group"></i></button>
                  <button type="button" class="btn btn-sm icon-option" data-icon='<i class="fa-solid fa-utensils"></i>'><i class="fa-solid fa-utensils"></i></button>
                  <button type="button" class="btn btn-sm icon-option" data-icon='<i class="fa-solid fa-martini-glass"></i>'><i class="fa-solid fa-martini-glass"></i></button>
                  <button type="button" class="btn btn-sm icon-option" data-icon='<i class="fa-solid fa-wine-glass"></i>'><i class="fa-solid fa-wine-glass"></i></button>
                  <button type="button" class="btn btn-sm icon-option" data-icon='<i class="fa-solid fa-cake-candles"></i>'><i class="fa-solid fa-cake-candles"></i></button>
                  <button type="button" class="btn btn-sm icon-option" data-icon='<i class="fa-solid fa-glass-water"></i>'><i class="fa-solid fa-glass-water"></i></button>
                </div>
              </div>

              <div class="form-group mb-3">
                <label for="edit_id_office_table" class="form-label">Sucursal</label>
                <select class="form-select rounded select2" id="edit_id_office_table" name="id_office_table">
                  <?php foreach ($offices as $office): ?>
                    <option value="<?php echo $office->id_office ?>"><?php echo urldecode($office->title_office) ?></option>
                  <?php endforeach ?>
                </select>
              </div>

            </div>

            <div class="col-md-5">

              <div class="edit-capacity-card rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="edit-capacity-title"><i class="fa-solid fa-users me-1"></i> Capacidad</span>
                  <span class="badge rounded-pill edit-capacity-badge" id="edit_capacity_badge">4 personas</span>
                </div>
                <div class="d-flex align-items-center justify-content-center gap-3">
                  <button type="button" class="btn btn-circle-capacity btn-minus" id="btnMinusPeople"><i class="fa-solid fa-minus"></i></button>
                  <div class="capacity-number text-center">
                    <span id="edit_people_value">4</span>
                    <small>personas</small>
                  </div>
                  <button type="button" class="btn btn-circle-capacity btn-plus" id="btnPlusPeople"><i class="fa-solid fa-plus"></i></button>
                </div>
                <div class="edit-people-visual d-flex flex-wrap justify-content-center gap-1 mt-3" id="edit_people_visual"></div>
              </div>

              <div class="alert alert-warning rounded d-none mt-3 mb-0 edit-capacity-warning">
                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                <span id="edit_capacity_warning">Reducir la capacidad a menos de los comensales actuales puede quitar asientos en uso.</span>
              </div>

            </div>

          </div>

        </div>

        <div class="modal-footer border-0">
          <button type="button" class="btn btn-danger rounded me-auto" id="btnDeleteTable"><i class="fa-solid fa-trash-can me-1"></i> Eliminar</button>
          <button type="button" class="btn btn-dark rounded" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success rounded"><i class="fa-solid fa-floppy-disk me-1"></i> Guardar Cambios</button>
        </div>

      </form>
    </div>
  </div>
</div>

<?php else: ?>

	<div class="col-12 text-center py-4">
		<p class="text-muted mb-0">No hay mesas registradas para esta sucursal.</p>
	</div>

<?php endif ?>