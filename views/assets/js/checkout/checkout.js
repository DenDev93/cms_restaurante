$(document).ready(function () {

	var TASA_USD = Number($("#tasaUsd").val()) || 36.75;

    /*=============================================
	Elegir el método de pago
	=============================================*/
	
    const $paymentOptions = $(".payment-option[data-method]");
    const $selectedPaymentMethod = $("#selectedPaymentMethod");

    $paymentOptions.on("click", function () {

    	$paymentOptions.removeClass("active");
    	$(this).addClass("active");

    	const method = $(this).data("method");
    	$selectedPaymentMethod.val(method);

    	if(method == "cash"){

    		$("#currencySection").show();

    	}else{

    		$("#currencySection").hide();

    		if($("#selectedCurrency").val() == "US$"){

    			$(".payment-option[data-currency='C$']").click();
    		}
    	}

    })

    /*=============================================
	Elegir la moneda de pago (efectivo)
	=============================================*/

    $(document).on("click",".payment-option[data-currency]",function(){

    	$(".payment-option[data-currency]").removeClass("active");
    	$(this).addClass("active");

    	const currency = $(this).data("currency");
    	$("#selectedCurrency").val(currency);
    	$("#paymentSymbol").html(currency);
    	$("#changeSymbol").html(currency);

    	const totalPagar = Number($("#totalCordobas").val());

    	if(currency == "US$"){

    		$("#paymentAmount").val((totalPagar/TASA_USD).toFixed(2));

    	}else{

    		$("#paymentAmount").val(totalPagar.toFixed(2));
    	}

    	calcularVuelto();

    })

    /*=============================================
	Calcular el vuelto
	=============================================*/

    let recibidoEditado = false;

    function calcularVuelto(){

    	const currency = $("#selectedCurrency").val();
    	const recibido = Number($("#paymentAmount").val());
    	const totalPagar = Number($("#totalCordobas").val());
    	const totalMoneda = currency == "US$" ? totalPagar/TASA_USD : totalPagar;

    	let vuelto = recibido - totalMoneda;

    	if(vuelto < 0){

    		vuelto = 0;
    	}

    	$("#paymentChange").val(vuelto.toFixed(2));
    	$("#changeAmount").val(vuelto.toFixed(2));

    	if(currency == "US$"){

    		$("#changeEquivalence").html("Equivalente: C$ "+(vuelto*TASA_USD).toFixed(2));

    	}else{

    		$("#changeEquivalence").html("Equivalente: US$ "+(vuelto/TASA_USD).toFixed(2));
    	}

    }

    $(document).on("input","#paymentAmount",function(){

    	recibidoEditado = true;
    	calcularVuelto();

    });

    /*=============================================
	Actualizar desglose de la cuenta
	=============================================*/

    function actualizarDesglose(tip){

    	const subtotal = Number($("#subtotalValue").html());
    	const tax = Number($("#taxValue").html());
    	const totalPagar = subtotal + tax + tip;

    	$("#modalSubtotal").html("C$ "+subtotal.toFixed(2));
    	$("#modalTax").html("C$ "+tax.toFixed(2));
    	$("#modalTip").html("C$ "+tip.toFixed(2));
    	$("#modalTotalPagar").html("C$ "+totalPagar.toFixed(2));

    	$("#totalCordobas").val(totalPagar.toFixed(2));

    	return totalPagar;
    }

    /*=============================================
	Cambiar la propina manualmente
	=============================================*/

    $(document).on("input","#paymentTip",function(){

    	const tip = Math.max(0, Number($(this).val()) || 0);

    	$(this).val(tip.toFixed(2));

    	const totalPagar = actualizarDesglose(tip);

    	if(!recibidoEditado){

    		const currency = $("#selectedCurrency").val();
    		$("#paymentAmount").val(currency == "US$" ? (totalPagar/TASA_USD).toFixed(2) : totalPagar.toFixed(2));

    	}

    	calcularVuelto();

    })

    /*=============================================
	Cargar valores de propina y totales
	=============================================*/
	
    setTimeout(function(){

    	let subtotal = Number($("#subtotalValue").html());
    	let total = Number($("#totalValue").html());
    	let tip = Number(subtotal)*Number($("#tipSystem").val());
    	let totalPagar = actualizarDesglose(tip);

    	$("#paymentTip").val(tip.toFixed(2));

    	if(!recibidoEditado){

    		$("#paymentAmount").val(totalPagar.toFixed(2));

    	}

    	calcularVuelto();

    },500)

    /*=============================================
	Click a procesar el pago
	=============================================*/

    $(document).on("click", "#processPaymentBtn", function(){

    	const currency = $("#selectedCurrency").val();
    	const totalPagar = Number($("#totalCordobas").val());
    	const totalMoneda = currency == "US$" ? totalPagar/TASA_USD : totalPagar;
    	const recibido = Number($("#paymentAmount").val());
    	const vuelto = Number($("#changeAmount").val());

    	if (recibido <= 0 || isNaN(recibido)) {
            fncToastr("error", "Valida el monto recibido");
            return;
        }  

        if (recibido < totalMoneda - 0.001) {
            fncToastr("error", "El monto recibido es menor al total a pagar");
            return;
        }

        const $btn = $(this);

        const idOrder = $("#transactionOrder").attr("idOrder");

        const boucherWin = window.open("", "_blank");

        $btn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        $btn.prop("disabled", true);

        var data = new FormData();
        data.append("subtotal",parseFloat($("#subtotalValue").html()));
        data.append("tax",parseFloat($("#taxValue").html()));
        data.append("total",parseFloat($("#totalValue").html()));
        data.append("tip",parseFloat($("#paymentTip").val()));
        data.append("idOrder",idOrder);
        data.append("idTable",$("#transactionOrder").attr("idTable"));
        data.append("method", $("#selectedPaymentMethod").val());
        data.append("received",recibido.toFixed(2));
        data.append("change",vuelto.toFixed(2));
        data.append("currency",currency);
        data.append("token", localStorage.getItem("tokenAdmin"));

        $.ajax({
            url:"/ajax/checkout.ajax.php",
            method: "POST",
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response){
            	
            	if(response == 200){

            	 	$btn.html("Procesar el Pago");
            	 	$btn.prop("disabled", false);

            	 	const modalEl = document.getElementById("myCheckout");
            	 	const modal = bootstrap.Modal.getInstance(modalEl);
            	 	if (modal) {
                        modal.hide();
                    }

                    boucherWin.location.href = "/boucher?idOrder="+idOrder;

                    fncSweetAlert("success",
                        `Pago de ${currency} ${recibido.toFixed(2)} procesado correctamente via ${$("#selectedPaymentMethod").val().toUpperCase()}! El boucher se abrió en una nueva pestaña`,setTimeout(()=>window.location="/",1500)
                    );

            	}else{

            		if(boucherWin){

            			boucherWin.close();
            		}

            		fncToastr("error","Error al procesar el pago");  
            	}

            },
            error: function() {
            	$btn.html("Procesar el Pago");
            	$btn.prop("disabled", false);
            	fncToastr("error","Error de conexión con el servidor");
            }

        })

    })

});