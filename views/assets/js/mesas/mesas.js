$(function () {

	/*=============================================
	Helpers del editor de mesas
	=============================================*/

	let editPeople = 4;

	function renderPeopleVisual(people) {

		editPeople = people;

		$("#edit_people_value").text(people);
		$("#edit_capacity_badge").text(people + (people === 1 ? " persona" : " personas"));

		let html = "";

		for (let i = 0; i < people; i++) {

			html += '<span class="person-chip person-chip-filled"><i class="fa-solid fa-person"></i></span>';
		}

		$("#edit_people_visual").html(html);

		$("#edit_capacity_badge").addClass("animate-pop");
		setTimeout(() => $("#edit_capacity_badge").removeClass("animate-pop"), 300);
	}

	function parseIconToPreview(html) {

		return html || '<i class="fa-solid fa-chair"></i>';
	}

	/*=============================================
	Abrir modal para editar mesa
	=============================================*/

	$(document).on("click", ".edit-table-btn", function () {

		let btn = $(this);

		$("#edit_id_table").val(btn.data("id"));
		$("#edit_title_table").val(btn.data("title"));
		$("#edit_icon_table").val(btn.data("icon"));

		let status = btn.data("status");
		$(`input[name="edit_status_radio"][value="${status}"]`).prop("checked", true);
		$("#edit_old_status").val(status);

		$("#edit_id_office_table").val(btn.data("office"));

		let people = parseInt(btn.data("people")) || 0;

		/* Si la mesa no tiene capacidad definida, usamos 4 por defecto */
		if (people < 1) people = 4;

		renderPeopleVisual(people);

		$("#edit_icon_preview").html(parseIconToPreview(btn.data("icon")));

		$("#modalEditTable").modal("show");
	});

	/*=============================================
	Stepper de capacidad
	=============================================*/

	$(document).on("click", "#btnPlusPeople", function () {

		if (editPeople < 20) renderPeopleVisual(editPeople + 1);
	});

	$(document).on("click", "#btnMinusPeople", function () {

		if (editPeople > 1) renderPeopleVisual(editPeople - 1);
	});

	/*=============================================
	Selector rápido de icono
	=============================================*/

	$(document).on("click", ".icon-option", function () {

		let icon = $(this).data("icon");

		$("#edit_icon_table").val(icon);
		$("#edit_icon_preview").html(icon);

		$(".icon-option").removeClass("active");
		$(this).addClass("active");
	});

	$(document).on("input", "#edit_icon_table", function () {

		$("#edit_icon_preview").html(parseIconToPreview($(this).val()));
	});

	/*=============================================
	Guardar cambios de la mesa
	=============================================*/

	$(document).on("submit", "#formEditTable", function (e) {

		e.preventDefault();
		e.stopPropagation();

		if (this.checkValidity() === false) {

			this.classList.add("was-validated");
			return;
		}

		let data = new FormData();
		data.append("id_table", $("#edit_id_table").val());
		data.append("title_table", $("#edit_title_table").val().trim());
		data.append("icon_table", $("#edit_icon_table").val());
		data.append("people_table", editPeople);
		data.append("status_table", $('input[name="edit_status_radio"]:checked').val());
		data.append("id_office_table", $("#edit_id_office_table").val());
		data.append("tokenEdit", localStorage.getItem("tokenAdmin"));

		$.ajax({

			url: "/ajax/mesas.ajax.php",
			method: "POST",
			data: data,
			contentType: false,
			cache: false,
			processData: false,
			success: function (response) {

				if (response == 200) {

					fncSweetAlert("success", "Mesa actualizada con éxito", setTimeout(() => window.location = "/mesas", 1250));

				} else if (response == 303) {

					fncSweetAlert("error", "El token ha expirado, inicia sesión nuevamente", setTimeout(() => window.location = "/logout", 1000));

				} else {

					fncSweetAlert("error", "No se pudo actualizar la mesa, intenta nuevamente", "");
				}
			}
		});
	});

	/*=============================================
	Eliminar mesa
	=============================================*/

	$(document).on("click", "#btnDeleteTable", function () {

		let idTable = $("#edit_id_table").val();

		fncSweetAlert("confirm", "¿Está seguro de eliminar esta mesa?").then(resp => {

			if (resp) {

				let data = new FormData();
				data.append("idTableDelete", idTable);
				data.append("tokenDelete", localStorage.getItem("tokenAdmin"));

				$.ajax({

					url: "/ajax/mesas.ajax.php",
					method: "POST",
					data: data,
					contentType: false,
					cache: false,
					processData: false,
					success: function (response) {

						if (response == 200) {

							$("#modalEditTable").modal("hide");
							fncSweetAlert("success", "Mesa eliminada con éxito", setTimeout(() => window.location = "/mesas", 1250));

						} else if (response == 303) {

							fncSweetAlert("error", "El token ha expirado, inicia sesión nuevamente", setTimeout(() => window.location = "/logout", 1000));

						} else {

							fncSweetAlert("error", "No se pudo eliminar la mesa, intenta nuevamente", "");
						}
					}
				});
			}
		});
	});

	/*=============================================
	Agregar nueva mesa
	=============================================*/

	$(document).on("submit", "#formAddTable", function(e){

		e.preventDefault();
		e.stopPropagation();

		if(this.checkValidity() === false){

			this.classList.add("was-validated");
			return;

		}

		var data = new FormData();
		data.append("title_table",$("#title_table").val());
		data.append("icon_table",$("#icon_table").val());
		data.append("people_table",$("#people_table").val());
		data.append("status_table",$("#status_table").val());
		data.append("id_office_table",$("#id_office_table").val());
		data.append("token", localStorage.getItem("tokenAdmin"));

		$.ajax({

			url:"/ajax/mesas.ajax.php",
			method: "POST",
			data: data,
			contentType: false,
			cache: false,
			processData: false,
			success: function(response){

				if(response == 200){

					fncSweetAlert("success", "Mesa agregada con éxito", setTimeout(()=>window.location="/mesas",1250));

				}else if(response == 303){

					fncSweetAlert("error", "El token ha expirado, inicia sesión nuevamente", setTimeout(()=>window.location="/logout",1000));

				}else{

					fncSweetAlert("error", "No se pudo agregar la mesa, intenta nuevamente", "");
				}

			}

		})

	});

	/*=============================================
  	Actualizar el tiempo de los comensales en tiempo real
  	=============================================*/
  
  	let timeInfo = $(".time-info");

  	if(timeInfo.length > 0){

  		timeInfo.each((i)=>{

  			const fechaInicio = new Date($(timeInfo[i]).attr("startTime"));
  			const fechaFin = new Date($(timeInfo[i]).attr("endTime"));

  			function actualizarDiferencia() {

  				const ahora = new Date();

  				let diffMs; // milisegundos
		        let totalMin; // minutos totales
		        let horas;
		        let minutos;

		        if (ahora >= fechaFin) {

		        	diffMs = ahora - fechaInicio; // milisegundos

		        }else{

		        	diffMs = fechaFin - fechaInicio;
		        	
		        }

		        totalMin = Math.floor(diffMs / 60000);
	        	horas = Math.floor(totalMin / 60);
      			minutos = totalMin % 60;

      			let salida = "";

      			if (horas > 0) {
		          salida += horas + "h ";
		        }

		        salida += minutos + "m";

		        $(timeInfo[i]).html(salida);

  			}

  			actualizarDiferencia();
  			let intervalo = setInterval(actualizarDiferencia, 60000);

  		})

  	}


})