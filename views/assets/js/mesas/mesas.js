$(function () {

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