$(function(){

  /*=============================================
  Estado global del POS
  =============================================*/

  let orderItems = {};
  let orderTotal = 0;
  let selectedItem = null;
  let goContinue = false;
  let isMobile = window.innerWidth <= 768;

  const processOrder = $("#transactionOrder").attr("processOrder");
  const taxRate = Number($("#taxSystem").val()) || 0.15;
  const tipRate = Number($("#tipSystem").val()) || 0.1;
  const tasaUsd = Number($("#tasaUsd").val()) || 36.75;

  if (processOrder != "Entregada") {
    goContinue = true;
  }

  /*=============================================
  Cargar items de venta existentes
  =============================================*/

  if ($(".order-item").length > 0) {
    const order_item = $(".order-item");
    order_item.each((i) => {
      if ($(order_item[i]).attr("data-process") == "Pendiente") {
        orderItems[$(order_item[i]).data("id")] = {
          id: $(order_item[i]).data("id"),
          order: $("#transactionOrder").attr("idOrder"),
          name: $(order_item[i]).data("name"),
          price: Number($(order_item[i]).data("price")) / Number($(order_item[i]).data("qty")),
          quantity: $(order_item[i]).data("qty")
        };
      }
    });
  }

  updateTotals();
  updateOrderBadge();

  /*=============================================
  Toggle mobile menu
  =============================================*/

  const $posMenu = $("#posMenu");
  const $posOrder = $("#posOrder");
  const $posOverlay = $("#posOverlay");

  function openMenu() {
    $posMenu.addClass("open");
    $posOverlay.addClass("active");
  }

  function closeMenu() {
    $posMenu.removeClass("open");
    $posOverlay.removeClass("active");
  }

  function openOrder() {
    $posOrder.addClass("open");
    $posOverlay.addClass("active");
  }

  function closeOrder() {
    $posOrder.removeClass("open");
    $posOverlay.removeClass("active");
  }

  function closeAll() {
    closeMenu();
    closeOrder();
  }

  $posOverlay.on("click", closeAll);

  $("#toggleMenuFab").on("click", function() {
    if ($posMenu.hasClass("open")) {
      closeMenu();
    } else {
      openMenu();
    }
  });

  $("#toggleOrderFab").on("click", function() {
    if ($posOrder.hasClass("open")) {
      closeOrder();
    } else {
      openOrder();
    }
  });

  /*=============================================
  Capturar notas
  =============================================*/

  $(document).on("change", "#note_order", function() {
    updateOrderDisplay();
    fncToastr("success", "Notas Modificadas");
  });

  /*=============================================
  Tabular categorías
  =============================================*/

  $(document).on("click", ".category-tab", function() {
    $(".category-tab").removeClass("active");
    $(".menu-category").removeClass("active");
    $(this).addClass("active");
    const category = $(this).data("category");
    $("#" + category).addClass("active");

    if (isMobile) {
      closeMenu();
    }
  });

  /*=============================================
  Click para agregar item del menú
  =============================================*/

  if (goContinue) {
    $(document).on("click", ".menu-item", function() {
      const itemId = $(this).data("item");
      const itemName = $(this).data("name") || $(this).find(".menu-item-name").text();
      const itemPrice = parseFloat($(this).data("price"));
      addToOrder(itemId, itemName, itemPrice);
    });
  }

  /*=============================================
  Función para agregar item a la orden
  =============================================*/

  function addToOrder(itemId, itemName, itemPrice) {
    if (!goContinue) return;

    if (orderItems[itemId]) {
      orderItems[itemId].quantity += 1;
    } else {
      orderItems[itemId] = {
        name: itemName,
        price: itemPrice,
        quantity: 1
      };
    }

    updateOrderDisplay();
    fncToastr("success", itemName + " adicionad@ a la orden");

    if (isMobile) {
      openOrder();
    }
  }

  /*=============================================
  Función para renderizar el pedido
  =============================================*/

  function updateOrderDisplay() {
    const $container = $('#order-items-active');
    let html = '';

    if (Object.keys(orderItems).length === 0) {
      html = `
        <div class="empty-order">
          <i class="bi bi-cart3"></i>
          <p>No hay items añadidos</p>
        </div>`;
      updateTotals();
      $("#note_order").val("");
      return;
    }

    $.each(orderItems, function(itemId, item) {
      const totalPrice = item.price * item.quantity;
      html += `
        <div class="order-item" data-id="${itemId}" data-process="Pendiente">
          <div class="order-item-header">
            <span class="order-item-name">${escapeHtml(item.name)}</span>
            <span class="order-item-price">${formatMoney(totalPrice)}</span>
          </div>
          <div class="order-item-controls">
            <button class="quantity-btn decrease-qty" data-id="${itemId}">
              <i class="bi bi-dash"></i>
            </button>
            <span class="quantity-display">x${item.quantity}</span>
            <button class="quantity-btn increase-qty" data-id="${itemId}">
              <i class="bi bi-plus"></i>
            </button>
            <button class="quantity-btn ms-2 remove-item" data-id="${itemId}" style="background:#dc3545;border-color:#dc3545;color:white">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>`;
    });

    $container.html(html);
    updateTotals();
    updateOrderBadge();

    /*=============================================
    Actualizar items de ventas via AJAX
    =============================================*/

    const arrayItems = [];
    $.each(orderItems, function(itemId, item) {
      arrayItems.push({
        id: itemId,
        order: $("#transactionOrder").attr("idOrder"),
        name: item.name,
        price: item.price,
        quantity: item.quantity,
        note: $("#note_order").val()
      });
    });

    const data = new FormData();
    data.append("items_sale", JSON.stringify(arrayItems));
    data.append("id_admin", $("#idAdmin").val());
    data.append("id_office", $("#idOffice").val());
    data.append("token", localStorage.getItem("tokenAdmin"));

    $.ajax({
      url: "/ajax/pos.ajax.php",
      method: "POST",
      data: data,
      contentType: false,
      cache: false,
      processData: false,
      success: function(response) {
        if (response == 401 || response == 400) {
          fncSweetAlert("error", "El token ha expirado, inicia sesión nuevamente", setTimeout(() => window.location = "/logout", 1000));
        }
      }
    });
  }

  /*=============================================
  Actualizar totales
  =============================================*/

  function updateTotals() {
    let subtotal = 0;

    $.each(orderItems, function(_id, item) {
      subtotal += item.price * item.quantity;
    });

    const orderItemFinish = $(".order-item-finish");
    if (orderItemFinish.length > 0) {
      orderItemFinish.each((i) => {
        subtotal += Number($(orderItemFinish[i]).attr("data-price"));
      });
    }

    const tax = subtotal * taxRate;
    const total = subtotal + tax;

    $('#subtotalValue').html(subtotal.toFixed(2));
    $('#taxValue').html(tax.toFixed(2));
    $('#totalValue').html(total.toFixed(2));

    orderTotal = total;

    /* Actualizar checkout modal si existe */
    updateCheckoutDisplay(subtotal, tax, total);
  }

  /*=============================================
  Actualizar display del checkout
  =============================================*/

  function updateCheckoutDisplay(subtotal, tax, total) {
    const tip = subtotal * tipRate;
    const totalPagar = subtotal + tax + tip;

    if ($('#modalSubtotal').length) {
      $('#modalSubtotal').html('C$ ' + subtotal.toFixed(2));
      $('#modalTax').html('C$ ' + tax.toFixed(2));
      $('#modalTip').html('C$ ' + tip.toFixed(2));
      $('#modalTotalPagar').html('C$ ' + totalPagar.toFixed(2));
      $('#totalCordobas').val(totalPagar.toFixed(2));

      if (!$('#paymentAmount').val() || Number($('#paymentAmount').val()) === 0) {
        $('#paymentAmount').val(totalPagar.toFixed(2));
        calcularVuelto();
      }
    }
  }

  /*=============================================
  Actualizar badge de items en FAB
  =============================================*/

  function updateOrderBadge() {
    const count = Object.keys(orderItems).length;
    const $badge = $('#orderItemCount');
    if (count > 0) {
      $badge.text(count);
      $badge.css('display', 'flex');
    } else {
      $badge.css('display', 'none');
    }
  }

  /*=============================================
  Incrementar cantidad
  =============================================*/

  $(document).on("click", ".increase-qty", function(e) {
    e.preventDefault();
    const itemId = $(this).data("id");
    if (orderItems[itemId]) {
      orderItems[itemId].quantity += 1;
      updateOrderDisplay();
    }
  });

  /*=============================================
  Disminuir cantidad
  =============================================*/

  $(document).on('click', '.decrease-qty', function(e) {
    e.preventDefault();
    const itemId = $(this).data("id");
    if (!orderItems[itemId]) return;

    if (orderItems[itemId].quantity > 1) {
      orderItems[itemId].quantity -= 1;
      updateOrderDisplay();
    } else {
      fncSweetAlert("confirm", "¿Está seguro de remover este item?", "").then(resp => {
        if (resp) {
          delete orderItems[itemId];
          updateOrderDisplay();
        }
      });
    }
  });

  /*=============================================
  Remover Item
  =============================================*/

  $(document).on("click", ".remove-item", function(e) {
    e.preventDefault();
    const itemId = $(this).data("id");
    fncSweetAlert("confirm", "¿Está seguro de remover este item?", "").then(resp => {
      if (resp) {
        if (orderItems[itemId]) {
          delete orderItems[itemId];
          updateOrderDisplay();
          fncToastr("success", "Item removido de la orden");
        }
      }
    });
  });

  /*=============================================
  Limpiar el pedido
  =============================================*/

  $('#clear-order').on('click', function() {
    if (Object.keys(orderItems).length === 0) return;
    fncSweetAlert("confirm", "¿Borrar todos los items de este pedido?", "").then(resp => {
      if (resp) {
        clearOrder();
      }
    });
  });

  function clearOrder() {
    orderItems = {};
    updateOrderDisplay();
    $('#note_order').val('');
    fncToastr("success", "Orden limpiada");
  }

  /*=============================================
  Enviar pedido a cocina
  =============================================*/

  $("#submit-order").on("click", function() {
    const btn = $(this);
    const idOrder = btn.attr("idOrder");

    if (Object.keys(orderItems).length === 0) {
      fncToastr("error", "Por favor adiciona items a la orden");
      return;
    }

    fncSweetAlert("confirm", "¿Enviar esta orden a la cocina?", "").then(resp => {
      if (resp) {
        btn.html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
        btn.prop("disabled", true);

        const data = new FormData();
        data.append("id_order", idOrder);
        data.append("process_order", "Preparando");
        data.append("token", localStorage.getItem("tokenAdmin"));

        $.ajax({
          url: "/ajax/pos.ajax.php",
          method: "POST",
          data: data,
          contentType: false,
          cache: false,
          processData: false,
          success: function(response) {
            btn.html('<i class="bi bi-check-circle"></i> Enviar Orden');
            btn.prop("disabled", false);

            if (response == 200) {
              fncToastr("success", "Orden enviada a la cocina");
              setTimeout(() => location.reload(), 600);
            } else if (response == 401 || response == 400) {
              fncSweetAlert("error", "El token ha expirado, inicia sesión nuevamente", setTimeout(() => window.location = "/logout", 1000));
            } else {
              fncToastr("error", "No se pudo enviar la orden a la cocina, intente nuevamente");
            }
          },
          error: function() {
            btn.html('<i class="bi bi-check-circle"></i> Enviar Orden');
            btn.prop("disabled", false);
            fncToastr("error", "Error de conexión con el servidor");
          }
        });
      }
    });
  });

  /*=============================================
  Cambiar estado del pedido
  =============================================*/

  $(document).on("click", ".changeProcessItem", function() {
    const elem = $(this);
    const data = new FormData();
    data.append("id_sale", $(this).attr("idSale"));
    data.append("process_sale", "Lista");
    data.append("id_office", $("#idOffice").val());
    data.append("token", localStorage.getItem("tokenAdmin"));

    $.ajax({
      url: "/ajax/pos.ajax.php",
      method: "POST",
      data: data,
      contentType: false,
      cache: false,
      processData: false,
      success: function(response) {
        if (response == 200) {
          elem.removeClass("bg-light");
          elem.addClass("bg-info");
          elem.html('<i class="fa-solid fa-check"></i>');
          fncToastr("success", "Item listo para entregar");
        } else {
          fncToastr("error", `Al plato le falta el insumo "${response}" para ser preparado`);
        }
      }
    });
  });

  /*=============================================
  Servir item en la mesa (mesero)
  =============================================*/

  $(document).on("click", ".serve-item", function() {
    const data = new FormData();
    data.append("id_sale", $(this).attr("idSale"));
    data.append("process_sale", "Entregada");
    data.append("id_office", $("#idOffice").val());
    data.append("token", localStorage.getItem("tokenAdmin"));

    $.ajax({
      url: "/ajax/pos.ajax.php",
      method: "POST",
      data: data,
      contentType: false,
      cache: false,
      processData: false,
      success: function(response) {
        if (response == 200) {
          fncToastr("success", "Item entregado en la mesa");
          setTimeout(() => location.reload(), 600);
        } else if (response == 401 || response == 400) {
          fncSweetAlert("error", "El token ha expirado, inicia sesión nuevamente", setTimeout(() => window.location = "/logout", 1000));
        } else {
          fncToastr("error", "No se pudo marcar el item como entregado");
        }
      }
    });
  });

  /*=============================================
  Eliminar Orden
  =============================================*/

  $(document).on("click", ".deleteOrder", function() {
    const state = $(this).attr("processOrder");

    if (state == "Entregada" || state == "Completada") {
      fncToastr("error", "Esta orden ya fue servida o cobrada, no se puede eliminar");
      return;
    }

    fncSweetAlert("confirm", "¿Está seguro de eliminar esta orden?", "").then(resp => {
      if (resp) {
        const data = new FormData();
        data.append("id_order_delete", $(this).attr("idOrder"));
        data.append("id_table_delete", $(this).attr("idTable"));
        data.append("token", localStorage.getItem("tokenAdmin"));

        $.ajax({
          url: "/ajax/pos.ajax.php",
          method: "POST",
          data: data,
          contentType: false,
          cache: false,
          processData: false,
          success: function(response) {
            if (response == 200) {
              fncSweetAlert("success", "Orden eliminada con éxito", setTimeout(function() { window.location = "/"; }, 1250));
            } else {
              fncToastr("error", "Error al eliminar la orden");
            }
          },
          error: function() {
            fncToastr("error", "Error de conexión con el servidor");
          }
        });
      }
    });
  });

  /*=============================================
  Atajos de teclado
  =============================================*/

  $(document).on("keydown", function(e) {
    if ($(".modal.show").length > 0) return;

    switch(e.key) {
      case "F2":
        e.preventDefault();
        if ($("#submit-order").length && Object.keys(orderItems).length > 0) {
          $("#submit-order").click();
        }
        break;
      case "F3":
        e.preventDefault();
        if ($('[data-bs-target="#myCheckout"]').length) {
          $('[data-bs-target="#myCheckout"]').click();
        }
        break;
      case "F4":
        e.preventDefault();
        if ($("#clear-order").length && Object.keys(orderItems).length > 0) {
          fncSweetAlert("confirm", "¿Borrar todos los items de este pedido?", "").then(resp => {
            if (resp) clearOrder();
          });
        }
        break;
      case "Escape":
        closeAll();
        break;
      case "Enter":
        if (selectedItem !== null) {
          addToOrder(selectedItem, orderItems[selectedItem].name, orderItems[selectedItem].price);
          selectedItem = null;
        }
        break;
    }

    if (e.key >= "1" && e.key <= "9" && !e.ctrlKey && !e.altKey) {
      const tabs = $(".category-tab");
      const idx = parseInt(e.key) - 1;
      if (idx < tabs.length) {
        tabs.eq(idx).click();
      }
    }
  });

  /*=============================================
  Soporte de scroll wheel en items del menú
  =============================================*/

  if ($('.menu-items-container').length) {
    let scrollTimeout;
    $('.menu-items-container').on('scroll', function() {
      const container = this;
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(function() {
        container.animate({ scrollTop: container.scrollTop() }, 100);
      }, 50);
    });
  }

  /*=============================================
  Resize handler
  =============================================*/

  $(window).resize(function() {
    isMobile = window.innerWidth <= 768;
    if (!isMobile) {
      closeMenu();
      closeOrder();
    }
  });

  /*=============================================
  Touch support for mobile
  =============================================*/

  let touchStartX = 0;
  let touchStartY = 0;

  document.addEventListener('touchstart', function(e) {
    touchStartX = e.touches[0].clientX;
    touchStartY = e.touches[0].clientY;
  }, { passive: true });

  document.addEventListener('touchend', function(e) {
    const touchEndX = e.changedTouches[0].clientX;
    const touchEndY = e.changedTouches[0].clientY;
    const deltaX = touchEndX - touchStartX;
    const deltaY = touchEndY - touchStartY;

    if (Math.abs(deltaX) > 50 && Math.abs(deltaX) > Math.abs(deltaY)) {
      if (deltaX < -50 && !isMobile) {
        closeOrder();
      } else if (deltaX > 50 && !isMobile) {
        closeMenu();
      }
    }
  }, { passive: true });

});

/*=============================================
  Utilidades
  =============================================*/

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function formatMoney(amount) {
  return 'C$ ' + Number(amount).toFixed(2);
}

function calcularVuelto() {
  const currency = $("#selectedCurrency").val();
  const recibido = Number($("#paymentAmount").val());
  const totalPagar = Number($("#totalCordobas").val());
  const totalMoneda = currency == "US$" ? totalPagar / tasaUsd : totalPagar;

  let vuelto = recibido - totalMoneda;
  if (vuelto < 0) vuelto = 0;

  $("#paymentChange").val(vuelto.toFixed(2));
  $("#changeAmount").val(vuelto.toFixed(2));

  if (currency == "US$") {
    $("#changeEquivalence").html("Equivalente: C$ " + (vuelto * tasaUsd).toFixed(2));
  } else {
    $("#changeEquivalence").html("Equivalente: US$ " + (vuelto / tasaUsd).toFixed(2));
  }
}