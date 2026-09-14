<?php

$url = "offices?select=id_office,title_office";
$method = "GET";
$fields = array();

$getOffices = CurlController::request($url,$method,$fields);

if($getOffices->status == 200){

	$offices = $getOffices->results;

}else{

	$offices = array();
}


?>


<!-- The Modal -->
<div class="modal fade" id="myOffices">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded">


    	<form method="GET" id="formOffice">
	    	<!-- Modal Header -->
	    	<div class="modal-header">
	    		<h4 class="modal-title">Elegir Sucursal</h4>
	    		<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
	    	</div>

	    	<!-- Modal body -->
	    	<div class="modal-body">

	    		<div class="form-group mb-3">

	    			<select class="form-select rounded" name="office">
	    				<option>Elige Sucursal</option>
	    				<?php if (!empty($offices)): ?>

	    					<?php foreach ($offices as $key => $value): ?>

	    						<option value="<?php echo urldecode($value->id_office) ?>_<?php echo urldecode($value->title_office) ?>"><?php echo urldecode($value->title_office) ?></option>

	    					<?php endforeach ?>

	    					<?php if ($_SESSION["admin"]->id_office_admin > 0): ?>

	    						<option value="0_Multi-Sucursal">Multi-Sucursal</option>

	    					<?php endif ?>	

	    				<?php endif ?>
	    			</select>
	    		</div>
	    	</div>

	    	<!-- Modal footer -->
	    	<div class="modal-footer d-flex justify-content-between">
	    		<div><button type="button" class="btn btn-dark rounded" data-bs-dismiss="modal">Cerrar</button></div>
	    		<div><button type="submit" class="btn btn-default backColor rounded" >Guardar</button></div>
	    	</div>

    	</form>

    </div>
  </div>
</div>

<!--=============================================
Cambiar de sucursal sin ensuciar la URL
(no deja ?office=... en el historial)
===============================================-->
<script>
(function () {
	var form = document.getElementById('formOffice');
	if (!form) return;

	form.addEventListener('submit', function (e) {
		var sel = form.querySelector('select[name="office"]');
		if (!sel || !sel.value) return;

		e.preventDefault();
		var value = encodeURIComponent(sel.value);

		// Guardar la oficina en sesión (GET a la URL actual sin navegar)
		fetch('?' + new URLSearchParams({ office: sel.value }).toString(), { credentials: 'include' })
			.then(function () {
				// Limpiar cualquier query/hash previo de la URL (sin añadir al historial)
				var clean = window.location.origin + window.location.pathname;
				window.location.replace(clean);
			})
			.catch(function () {
				window.location.replace(window.location.origin + window.location.pathname);
			});
	});
})();
</script>
