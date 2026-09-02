<div class="order-summary">
  <div class="order-summary-header">
    <h4 class="mb-1" id="transactionOrder" idOrder="<?php echo $idOrder ?>" idTable="<?php echo $idTable ?>" processOrder="<?php echo $processOrder ?>">Orden # <?php echo $transactionOrder ?></h4>
    <p class="text-center p-0"><?php echo TemplateController::formatDate(4,$dateOrder) ?></p>
    <?php if ($processOrder == "Ordenando"): ?>
      <span class="badge bg-secondary w-100">Pendiente</span>
    <?php endif ?>
    <?php if ($processOrder == "Preparando"): ?>
      <span class="badge bg-warning text-dark w-100">En cocina</span>
    <?php endif ?>
    <?php if ($processOrder == "Entregada"): ?>
      <span class="badge bg-success w-100">Servida</span>
    <?php endif ?>
  </div>

  <div class="small text-center bg-gray rounded px-3 py-1 mb-3">Mesero: <?php echo explode("@",$_SESSION["admin"]->email_admin)[0]  ?> </div>

  <?php if ($processOrder != "Entregada"): ?>
  
    <div class="order-items" id="order-items-active">

      <?php if (!empty($sales)): ?>

        <?php foreach ($sales as $key => $value): ?>

          <?php if ($value->process_sale == "Pendiente"): ?>

            <div class="order-item" 
            data-process="Pendiente"
            data-id="<?php echo $value->id_food_sale ?>"
            data-name="<?php echo urldecode($value->title_food) ?>"
            data-qty="<?php echo $value->qty_sale ?>"
            data-price="<?php echo $value->subtotal_sale ?>" >
              <span class="order-item-name"><?php echo urldecode($value->title_food) ?></span>
              <div class="order-item-meta">
                <span class="order-item-price"><?php echo fncMoney($value->subtotal_sale) ?></span>
                <span class="badge bg-secondary align-self-center">Pendiente</span>
              </div>
              <div class="order-item-controls">
                <button class="quantity-btn decrease-qty">
                  <i class="bi bi-dash"></i>
                </button>
                <span class="quantity-display">x<?php echo $value->qty_sale ?></span>
                <button class="quantity-btn increase-qty">
                  <i class="bi bi-plus"></i>
                </button>
                <button class="quantity-btn ms-2 remove-item" style="background:#dc3545; border-color:#dc3545; color:white">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>

          <?php endif ?>

          <?php if ($value->process_sale == "Preparando"): ?>

            <div class="order-item" 
            data-process="Preparando"
            data-name="<?php echo urldecode($value->title_food) ?>">
              <span class="order-item-name"><?php echo urldecode($value->title_food) ?></span>
              <div class="order-item-meta">
                <span class="order-item-price"><?php echo fncMoney($value->subtotal_sale) ?></span>
                <span class="badge bg-warning text-dark align-self-center">En cocina</span>
              </div>
              <div class="order-item-controls">
                <span class="quantity-display">x<?php echo $value->qty_sale ?></span>
              </div>
            </div>

          <?php endif ?>

          <?php if ($value->process_sale == "Lista"): ?>

            <div class="order-item" 
            data-process="Lista"
            data-name="<?php echo urldecode($value->title_food) ?>">
              <span class="order-item-name"><?php echo urldecode($value->title_food) ?></span>
              <div class="order-item-meta">
                <span class="order-item-price"><?php echo fncMoney($value->subtotal_sale) ?></span>
                <span class="badge bg-info align-self-center">Lista</span>
              </div>
              <div class="order-item-controls">
                <span class="quantity-display">x<?php echo $value->qty_sale ?></span>
                <button class="ms-2 bg-info border-0 p-0 rounded serve-item px-1" idSale="<?php echo $value->id_sale ?>">
                  <i class="fa-solid fa-utensils"></i> Servir
                </button>
              </div>
            </div>

          <?php endif ?>

        <?php endforeach ?>

      <?php else: ?>

        <div class="empty-order">
          <i class="bi bi-cart3"></i>
          <p>No hay items añadidos</p>
        </div>
        
      <?php endif ?>
      
    </div>

    <div class="order-notes">
      <textarea class="form-control" id="note_order" placeholder="Adicionar notas a esta orden..." rows="3"><?php echo $noteOrder ? $noteOrder : null ?></textarea>
    </div>

    <div class="order-actions">
      <button class="btn btn-success btn-lg w-100 mb-2" id="submit-order" idOrder="<?php echo $idOrder ?>">
        <i class="bi bi-check-circle"></i> Enviar Orden
      </button>

      <?php if ($processOrder == "Ordenando"): ?>
        <button class="btn btn-dark w-100" id="clear-order">
          <i class="bi bi-trash"></i> Eliminar todos los items
        </button>
      <?php endif ?>
    </div>
  <?php endif ?>

  <div class="order-items" id="order-items-finish">
    
    <?php if (!empty($sales)): ?>

      <?php foreach ($sales as $key => $value): ?>

        <?php if ($value->process_sale == "Entregada"): ?>

          <div class="order-item-finish" 
          data-id="<?php echo $value->id_food_sale ?>"
          data-name="<?php echo urldecode($value->title_food) ?>"
          data-qty="<?php echo $value->qty_sale ?>"
          data-price="<?php echo $value->subtotal_sale ?>">

            <span class="order-item-name"><?php echo urldecode($value->title_food) ?></span>

            <div class="order-item-meta">
              <span class="order-item-price"><?php echo fncMoney($value->subtotal_sale) ?></span>
              <span class="badge bg-success align-self-center">Entregada</span>
            </div>

            <div class="order-item-controls">
              <span class="quantity-display">x<?php echo $value->qty_sale ?></span>
              <button class="ms-2 bg-success border-0 p-0 rounded  px-1">
                  <i class="fa-solid fa-check"></i>
              </button>
            </div>

          </div>

        <?php endif ?>

      <?php endforeach ?>
    <?php endif ?>
  </div>

  <div class="order-total">
    <div class="d-flex justify-content-between">
      <span>Subtotal:</span>
      <span>C$ <span id="subtotalValue">0.00</span></span>
    </div>
    <div class="d-flex justify-content-between">
      <span>IVA (<?php echo $tax*100 ?>%):</span>
      <span>C$ <span id="taxValue">0.00</span></span>
    </div>
    <hr>
    <div class="d-flex justify-content-between total-line">
      <strong>Total:</strong>
      <strong>C$ <span id="totalValue">0.00</span></strong>
    </div>
  </div>

  <?php if ($processOrder == "Entregada"): ?>

   <div class="order-payment mt-3">
    
    <a href="/boucher?idOrder=<?php echo $idOrder ?>" target="_blank" class="btn btn-success btn-lg w-100 mb-2">
      <i class="bi bi-printer"></i> Imprimir Boucher
    </a>

  </div>
    
  <?php else: ?>

   <div class="order-payment mt-3">
    
    <button class="btn backColor btn-lg w-100 mb-2" data-bs-toggle="modal" data-bs-target="#myCheckout">
      <i class="fa-solid fa-cash-register"></i> Pagar Orden    
    </button>

  </div>
    
  <?php endif ?>

 

</div>