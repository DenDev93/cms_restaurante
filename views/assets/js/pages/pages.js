/*=============================================
Abrir ventana modal de páginas
=============================================*/

$(document).on("click",".myPage",function(){

	$("#myPage").modal("show");

	var page = $(this).attr("page");
	
	$("#myPage").on('shown.bs.modal',function(){

		$("input[name='id_page']").remove();

		if(page != undefined){
			
			console.log("page", page);

			/*=============================================
			Editar Página
			=============================================*/

			$("#title_page").before(`

				<input type="hidden" id="id_page" name="id_page" value="${btoa(JSON.parse(page).id_page)}">

			`)

			$("#title_page").val(JSON.parse(page).title_page);
			$("#url_page").val(JSON.parse(page).url_page);
			$("#icon_page").val(JSON.parse(page).icon_page);
			$("#type_page").val(JSON.parse(page).type_page);
			$("#menu_type_page").val(JSON.parse(page).menu_type_page)

			if(JSON.parse(page).menu_type_page == 2){

				$("#parent_id").show();
				$("#parent_id_page").val(JSON.parse(page).parent_id_page);

			}else{

				$("#parent_id").hide();
			}
		

		}else{

			$("#title_page").val('');
			$("#url_page").val('');
			$("#icon_page").val('');
			$("#type_page").val('');
			$("#menu_type_page").val('');
			$("#parent_id_page").val(0);
		}

	})

})

/*=============================================
Cambiar orden de páginas
=============================================*/

$("#sortable").sortable({
	placeholder: 'sort-highlight',
	handle: '.handle',
	forcePlaceholderSize: true,
	zIndex:999999,
	out: function(event,ui){
		
		var listPage = $("#sortable li");
		var countList = 0;

		listPage.each((i)=>{

			var idPage = $(listPage[i]).attr("idPage");
			var index = i+1;

			var data = new FormData();
			data.append("idPage",idPage);
			data.append("index", index);
			data.append("token", localStorage.getItem("tokenAdmin"));

			$.ajax({

				url:"/ajax/pages.ajax.php",
				method:"POST",
				data:data,
				contentType:false,
				cache:false,
				processData:false,
				success: function(response){
					
					if(response == 200){

						countList++;

						if(countList == listPage.length){

							fncToastr("success", "El orden del menú ha sido actualizado con éxito");
						}
					}

				}

			})

		})

	}

})

/*=============================================
Cambiar orden de subpáginas 
=============================================*/

$(".sub_sortable").sortable({
	placeholder: 'sort-highlight',
	handle: '.sub_handle',
	forcePlaceholderSize: true,
	zIndex:999999,
	out: function(event,ui){
		
		var listPage = $("#sortable li");
		var countList = 0;

		listPage.each((i)=>{

			var idPage = $(listPage[i]).attr("idPage");
			var index = i+1;

			var data = new FormData();
			data.append("idPage",idPage);
			data.append("index", index);
			data.append("token", localStorage.getItem("tokenAdmin"));

			$.ajax({

				url:"/ajax/pages.ajax.php",
				method:"POST",
				data:data,
				contentType:false,
				cache:false,
				processData:false,
				success: function(response){
					
					if(response == 200){

						countList++;

						if(countList == listPage.length){

							fncToastr("success", "El orden del menú ha sido actualizado con éxito");
						}
					}

				}

			})

		})

	}

})


/*=============================================
Eliminar una página
=============================================*/

$(document).on("click",".deletePage",function(){

	var idPage = $(this).attr("idPage");
	
	if(atob(idPage) == 1 || atob(idPage) == 2){

		fncToastr("error", "Esta página no se puede borrar");
		return;
	}

	fncSweetAlert("confirm", "¿Está seguro de borrar esta página?", "").then(resp=>{

		if(resp){
			
			var data = new FormData();
			data.append("idPageDelete",idPage);
			data.append("token", localStorage.getItem("tokenAdmin"));

			$.ajax({

				url:"/ajax/pages.ajax.php",
				method:"POST",
				data:data,
				contentType:false,
				cache:false,
				processData:false,
				success: function(response){
					
					if(response == 200){

						fncSweetAlert("success","La página ha sido eliminada con éxito",setTimeout(()=>location.reload(),1250));
					
					}else{

						fncToastr("error", "ERROR: La página tiene módulos vinculados");
					}
				}

			})

		}
	})

})

/*=============================================
Aparecer páginas padres para seleccionar
=============================================*/

function changeMenuType(e){

	if(e.value == 2){

		$("#parent_id").show();
	
	}else{

		$("#parent_id").hide();

	}
}