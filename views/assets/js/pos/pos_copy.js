$(function() {
    let orderItems = {};
    let orderTotal = 0;

    // -------- Tabs de categorías --------
    $(document).on('click', '.category-tab', function() {
      $('.category-tab').removeClass('active');
      $('.menu-category').removeClass('active');

      $(this).addClass('active');
      const category = $(this).data('category');
      $('#' + category).addClass('active');
    });

    // -------- Click en ítem del menú --------
    $(document).on('click', '.menu-item', function() {
      const itemId = $(this).data('item');
      const itemName = $(this).find('.menu-item-name').text();
      const itemPrice = parseFloat($(this).data('price'));
      addToOrder(itemId, itemName, itemPrice);
    });

    // -------- Agregar al pedido --------
    function addToOrder(itemId, itemName, itemPrice) {
      if (orderItems[itemId]) {
        orderItems[itemId].quantity += 1;
      } else {
        orderItems[itemId] = {
          name: itemName,
          price: itemPrice,
          quantity: 1,
          modifications: []
        };
      }
      updateOrderDisplay();
      showNotification(`${itemName} added to order`, 'success');
    }

    // -------- Render del pedido --------
    function updateOrderDisplay() {
      const $container = $('#order-items');

      if (Object.keys(orderItems).length === 0) {
        $container.html(`
        <div class="empty-order">
        <i class="bi bi-cart3"></i>
        <p>No items added yet</p>
        </div>
        `);
        updateTotals();
        return;
      }

      let html = '';
      $.each(orderItems, function(itemId, item) {
        html += `
        <div class="order-item" data-id="${itemId}">
        <div class="order-item-header">
        <span class="order-item-name">${item.name}</span>
        <div class="order-item-controls">
        <button class="quantity-btn decrease-qty">
        <i class="bi bi-dash"></i>
        </button>
        <span class="quantity-display">x${item.quantity}</span>
        <button class="quantity-btn increase-qty">
        <i class="bi bi-plus"></i>
        </button>
        <button class="quantity-btn ms-2 remove-item" style="background:#dc3545; border-color:#dc3545;">
        <i class="bi bi-trash"></i>
        </button>
        </div>
        </div>
        <div class="d-flex justify-content-between">
        <span class="order-item-price">$${(item.price * item.quantity).toFixed(2)}</span>
        </div>
        ${item.modifications.length ? `<div class="order-item-modifications">${item.modifications.join(', ')}</div>` : ''}
        </div>
        `;
      });

      $container.html(html);
      updateTotals();
    }

    // -------- Cantidades (delegación) --------
    $(document).on('click', '.increase-qty', function(e) {
      e.preventDefault();
      const itemId = $(this).closest('.order-item').data('id');
      if (orderItems[itemId]) {
        orderItems[itemId].quantity += 1;
        updateOrderDisplay();
      }
    });

    $(document).on('click', '.decrease-qty', function(e) {
      e.preventDefault();
      const itemId = $(this).closest('.order-item').data('id');
      if (!orderItems[itemId]) return;

      if (orderItems[itemId].quantity > 1) {
        orderItems[itemId].quantity -= 1;
      } else {
        delete orderItems[itemId];
      }
      updateOrderDisplay();
    });

    $(document).on('click', '.remove-item', function(e) {
      e.preventDefault();
      const itemId = $(this).closest('.order-item').data('id');
      if (orderItems[itemId]) {
        delete orderItems[itemId];
        updateOrderDisplay();
        showNotification('Item removed from order', 'info');
      }
    });

    // -------- Totales --------
    function updateTotals() {
      let subtotal = 0;
      $.each(orderItems, function(_id, item) {
        subtotal += item.price * item.quantity;
      });
      const tax = subtotal * 0.085; // 8.5% IVA
      const total = subtotal + tax;

      $('#subtotal').text(`$${subtotal.toFixed(2)}`);
      $('#tax').text(`$${tax.toFixed(2)}`);
      $('#total').text(`$${total.toFixed(2)}`);

      orderTotal = total;
    }

    // -------- Enviar pedido --------
    $('#submit-order').on('click', function() {
      if (Object.keys(orderItems).length === 0) {
        showNotification('Please add items to your order', 'warning');
        return;
      }
      if (confirm('Submit this order to the kitchen?')) {
        console.log('Order submitted:', orderItems);
        showNotification('Order submitted successfully!', 'success');
        setTimeout(() => clearOrder(), 1500);
      }
    });

    // -------- Limpiar pedido --------
    $('#clear-order').on('click', function() {
      if (Object.keys(orderItems).length === 0) return;
      if (confirm('Clear all items from this order?')) clearOrder();
    });

    function clearOrder() {
      orderItems = {};
      updateOrderDisplay();
      $('.order-notes textarea').val('');
      showNotification('Order cleared', 'info');
    }

    // -------- Notificaciones --------
    function showNotification(message, type = 'info') {
      const cls = (type === 'success') ? 'success' : (type === 'warning') ? 'warning' : 'info';
      const $n = $(`
      <div class="alert alert-${cls} alert-dismissible fade show position-fixed"
      style="top:20px; right:20px; z-index:9999; min-width:300px;">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      `);
      $('body').append($n);
      setTimeout(() => $n.remove(), 3000);
    }
  });