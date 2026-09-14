<div class="order-summary">
  <div class="order-summary-header">
    <h4 id="transactionOrder" idOrder="<?php echo $idOrder ?>" idTable="<?php echo $idTable ?>" processOrder="<?php echo $processOrder ?>">Orden # <?php echo $transactionOrder ?></h4>
    <p class="order-date"><?php echo TemplateController::formatDate(4,$dateOrder) ?></p>
    <?php if ($processOrder == "Ordenando"): ?>
      <span class="status-badge status-pendiente">Pendiente</span>
    <?php endif; ?>
    <?php if ($processOrder == "Preparando"): ?>
      <span class="status-badge status-preparando">En cocina</span>
    <?php endif; ?>
    <?php if ($processOrder == "Entregada"): ?>
      <span class="status-badge status-entregada">Servida</span>
    <?php endif; ?>
  </div>

  <div class="small text-center mb-3" style="color:var(--color-text-secondary)">Mesero: <?php echo explode("@",$_SESSION["admin"]->email_admin)[0] ?></div>

  <?php if ($processOrder != "Entregada"): ?>
  
    <div class="order-items-list" id="order-items-active">
      <?php if (!empty($sales)): ?>
        <?php foreach ($sales as $key => $value): ?>
          <?php if ($value->process_sale == "Pendiente"): ?>
            <div class="order-item" data-process="Pendiente" data-id="<?php echo $value->id_food_sale ?>" data-name="<?php echo urldecode($value->title_food) ?>" data-qty="<?php echo $value->qty_sale ?>" data-price="<?php echo $value->subtotal_sale ?>">
              <div class="order-item-header">
                <span class="order-item-name"><?php echo urldecode($value->title_food) ?></span>
                <span class="order-item-price"><?php echo fncMoney($value->subtotal_sale) ?></span>
              </div>
              <div class="order-item-controls">
                <button class="quantity-btn decrease-qty"><i class="bi bi-dash"></i></button>
                <span class="quantity-display">x<?php echo $value->qty_sale ?></span>
                <button class="quantity-btn increase-qty"><i class="bi bi-plus"></i></button>
                <button class="quantity-btn ms-2 remove-item" style="background:#dc3545;border-color:#dc3545;color:white"><i class="bi bi-trash"></i></button>
              </div>
              <span class="badge-status badge-pendiente">Pendiente</span>
            </div>
          <?php endif; ?>

          <?php if ($value->process_sale == "Preparando"): ?>
            <div class="order-item" data-process="Preparando" data-name="<?php echo urldecode($value->title_food) ?>">
              <div class="order-item-header">
                <span class="order-item-name"><?php echo urldecode($value->title_food) ?></span>
                <span class="order-item-price"><?php echo fncMoney($value->subtotal_sale) ?></span>
              </div>
              <div class="order-item-controls">
                <span class="quantity-display">x<?php echo $value->qty_sale ?></span>
              </div>
              <span class="badge-status badge-preparando">En cocina</span>
            </div>
          <?php endif; ?>

          <?php if ($value->process_sale == "Lista"): ?>
            <div class="order-item" data-process="Lista" data-name="<?php echo urldecode($value->title_food) ?>">
              <div class="order-item-header">
                <span class="order-item-name"><?php echo urldecode($value->title_food) ?></span>
                <span class="order-item-price"><?php echo fncMoney($value->subtotal_sale) ?></span>
              </div>
              <div class="order-item-controls">
                <span class="quantity-display">x<?php echo $value->qty_sale ?></span>
                <button class="ms-2 bg-info border-0 p-0 rounded serve-item px-1" idSale="<?php echo $value->id_sale ?>" style="color:#fff;border:none;cursor:pointer;font-size:var(--font-size-xs)">Servir</button>
              </div>
              <span class="badge-status badge-lista">Lista</span>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-order">
          <i class="bi bi-cart3"></i>
          <p>No hay items añadidos</p>
        </div>
      <?php endif; ?>
    </div>

    <div class="order-notes">
      <textarea class="form-control" id="note_order" placeholder="Adicionar notas a esta orden..." rows="2"><?php echo $noteOrder ? $noteOrder : null ?></textarea>
    </div>

    <div class="order-actions">
      <button class="btn-order btn-order-primary" id="submit-order" idOrder="<?php echo $idOrder ?>">
        <i class="bi bi-check-circle"></i> Enviar Orden
      </button>
      <?php if ($processOrder == "Ordenando"): ?>
        <button class="btn-order btn-order-danger" id="clear-order">
          <i class="bi bi-trash"></i> Eliminar todos los items
        </button>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="order-items-finish">
    <?php if (!empty($sales)): ?>
      <?php foreach ($sales as $key => $value): ?>
        <?php if ($value->process_sale == "Entregada"): ?>
          <div class="order-item-finish" data-id="<?php echo $value->id_food_sale ?>" data-name="<?php echo urldecode($value->title_food) ?>">
            <span class="order-item-name"><?php echo urldecode($value->title_food) ?></span>
            <span class="badge-status badge-entregada">Entregada</span>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="order-total">
    <div class="total-line">
      <span>Subtotal</span>
      <span>C$ <span id="subtotalValue">0.00</span></span>
    </div>
    <div class="total-line">
      <span>IVA (<?php echo $tax*100 ?>%)</span>
      <span>C$ <span id="taxValue">0.00</span></span>
    </div>
    <div class="total-line total-final">
      <strong>Total</strong>
      <strong>C$ <span id="totalValue">0.00</span></strong>
    </div>
  </div>

  <?php if ($processOrder == "Entregada"): ?>
    <div class="order-payment mt-3">
      <a href="/boucher?idOrder=<?php echo $idOrder ?>" target="_blank" class="btn-payment">
        <i class="bi bi-printer"></i> Imprimir Boucher
      </a>
    </div>
  <?php else: ?>
    <div class="order-payment mt-3">
      <button class="btn-payment" data-bs-toggle="modal" data-bs-target="#myCheckout">
        <i class="fa-solid fa-cash-register"></i> Pagar Orden
      </button>
    </div>
  <?php endif; ?>
</div>