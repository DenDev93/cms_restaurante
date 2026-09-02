<div class="row mb-4">
  <div class="col-12">
    <div class="pos-header">
      <h1 class="table-title"><?php echo urldecode($titleTable) ?></h1>
      <div class="header-actions">
        <a href="/ordenes" class="btn btn-outline-light btn-sm me-2 rounded">
          <i class="bi bi-clock-history"></i> Historial de órdenes
        </a>
        <a href="/" class="btn btn-outline-light btn-sm me-2 rounded">
          <i class="bi bi-arrow-left"></i> Regresar a Mesas
        </a>
        <button class="btn btn-outline-light btn-sm rounded deleteOrder" idOrder="<?php echo $idOrder ?>" processOrder="<?php echo $processOrder ?>" idTable="<?php echo $idTable ?>">
            <i class="bi bi-receipt"></i> Eliminar Orden
        </button>
      </div>
    </div>
  </div>
</div>