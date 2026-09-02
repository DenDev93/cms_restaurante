$(function () {

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