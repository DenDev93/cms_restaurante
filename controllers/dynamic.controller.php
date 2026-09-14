<?php 

class DynamicController{

	/*=============================================
	Gestión de datos dinámicos
	=============================================*/	

	public function manage(){

	if(isset($_POST["module"])){

		echo '<script>

			fncMatPreloader("on");
		    fncSweetAlert("loading", "Procesando...", "");

		</script>';

		$module = json_decode($_POST["module"]);

		$url_page = $module->url_page;
		$suffix = $module->suffix_module;

		/*=============================================
		Editar datos
		=============================================*/

		if(isset($_POST["idItem"])){

			/*=============================================
			Actualizar datos
			=============================================*/

			$url = $module->title_module."?id=".base64_decode($_POST["idItem"])."&nameId=id_".$suffix."&token=".$_SESSION["admin"]->token_admin."&table=admins&suffix=admin";
			$method = "PUT";
			$fields = "";

			foreach ($module->columns as $value) {

				if($value->type_column == "password" && !empty($_POST[$value->title_column])){

					$fields.= $value->title_column."=".crypt(trim($_POST[$value->title_column]),'$2a$07$azybxcags23425sdg23sdfhsd$')."&";

				}else if($value->type_column == "email"){

					$fields.= $value->title_column."=".trim($_POST[$value->title_column] ?? "")."&";

				}else{
				
					$fields.= $value->title_column."=".urlencode(trim($_POST[$value->title_column] ?? ""))."&";

				}

			}

			$fields = substr($fields,0,-1);

			$update = CurlController::request($url,$method,$fields);

			$this->respond($update, $url_page, "actualizado");

		}else{
	
			/*=============================================
			Crear datos
			=============================================*/

			$url = $module->title_module."?token=".$_SESSION["admin"]->token_admin."&table=admins&suffix=admin";
			$method = "POST";
			$fields = array();

			foreach ($module->columns as $value) {

				if($value->type_column == "password"){

					$fields[$value->title_column] = crypt(trim($_POST[$value->title_column] ?? ""),'$2a$07$azybxcags23425sdg23sdfhsd$');
				
				}else if($value->type_column == "email"){

					$fields[$value->title_column] = trim($_POST[$value->title_column] ?? "");
				}else{
				
					$fields[$value->title_column] = urlencode(trim($_POST[$value->title_column] ?? ""));

				}

			}

			$fields["date_created_".$suffix] = date("Y-m-d");

			$save = CurlController::request($url,$method,$fields);

			$this->respond($save, $url_page, "guardado");

		}

	}

}

/*=============================================
Responder según el resultado de la API
(evita que el preloader/alerta quede girando sin fin
y muestra el motivo del error cuando falla el guardado)
=============================================*/

private function respond($result, $url_page, $action){

	if($result->status == 200){

		echo '

			<script>

				fncMatPreloader("off");
				fncFormatInputs();
			    fncSweetAlert("success","El registro ha sido '.$action.' con éxito", setTimeout(()=>window.location="/'.$url_page.'",1000));

			</script>

		';

	}else if($result->status == 303){ /* token expirado */

		echo '

			<script>

				fncMatPreloader("off");
				fncFormatInputs();
			    fncSweetAlert("error","El token ha expirado, inicia sesión nuevamente", setTimeout(()=>window.location="/logout",1000));

			</script>

		';

	}else{

		/* Mostrar el motivo del error enviado por la API */
		$message = "Ocurrió un error al guardar el registro";

		if(is_string($result->results) && !empty($result->results)){

			$message = $result->results;

		}else if(is_array($result->results) || is_object($result->results)){

			$message = json_encode($result->results);
		}

		$message = json_encode($message);

		echo '

			<script>

				fncMatPreloader("off");
				fncFormatInputs();
			    fncSweetAlert("error", '.$message.', "");

			</script>

		';
	}
}

}
