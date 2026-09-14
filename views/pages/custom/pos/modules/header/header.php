<div class="row mb-4">
  <div class="col-12">
    <div class="pos-header">
      <h1 class="table-title"><?php echo urldecode($titleTable) ?></h1>
      <?php if ($processOrder != "Entregada"): ?>
        <span class="badge-live">En vivo</span>
      <?php endif; ?>
      <div class="header-actions">
        <a href="/ordenes" class="btn btn-outline-light btn-sm me-2 rounded">
          <i class="bi bi-clock-history"></i> Historial
        </a>
        <a href="/" class="btn btn-outline-light btn-sm me-2 rounded">
          <i class="bi bi-arrow-left"></i> Mesas
        </a>
        <?php if ($processOrder != "Entregada"): ?>
          <button class="btn btn-outline-light btn-sm rounded deleteOrder" idOrder="<?php echo $idOrder ?>" processOrder="<?php echo $processOrder ?>" idTable="<?php echo $idTable ?>">
            <i class="bi bi-receipt"></i> Cancelar
          </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>