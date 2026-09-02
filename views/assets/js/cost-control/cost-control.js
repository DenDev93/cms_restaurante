/*=============================================
Sacar el costo unitario de la compra y el stock
=============================================*/

$(document).on("change",".changePurchase", function(){

	var qty_purchase = $("#qty_purchase").val();
	var cost_purchase = $("#cost_purchase").val();

	$("#unit_cost_purchase").val((Number(cost_purchase)/Number(qty_purchase)).toFixed(2));

	$("#stock_purchase").val(qty_purchase);

})

/*=============================================
Sacar el costo del producto de acuerdo al margen de utilidad
=============================================*/

$(document).on("change",".changeUtilityFood", function(){

	fncSweetAlert("loading", "Costeando producto...", "");

	var products_food = $("#products_food").val();
	var id_office_food = $("#id_office_food").val();
	var utility = $(this).val();

	var data = new FormData();
	data.append("products_food",products_food);
	data.append("id_office_food",id_office_food);
	data.append("utility",utility);

	$.ajax({
		url:"/ajax/cost-control.ajax.php",
		method: "POST",
		data: data,
		contentType: false,
		cache: false,
		processData: false,
		success: function (response){

			fncSweetAlert("close", "", "");
			
			if(response == "error"){

				fncToastr("error","Existe un producto sin compra o sin costo unitario");

				return;

			}else{

				$("#cost_food").val(JSON.parse(response).totalCost);
				$("#price_food").val(JSON.parse(response).price);
			}

		}

	})
})

