<?php 

class BooksController{

	public function manageBooks(){


		if(isset($_POST["id_table_book"])){

			echo '<script>

				fncMatPreloader("on");
			    fncSweetAlert("loading", "Procesando...", "");

			</script>';

			$date_book = DateTime::createFromFormat('d/m/Y', $_POST["date_book"])->format('Y-m-d');

			/*=============================================
			Buscar si la reserva existe
			=============================================*/

			$url = "books?linkTo=id_table_book,date_book,time_book,id_office_book&equalTo=".$_POST["id_table_book"].",".$date_book.",".$_POST["time_book"].",".$_SESSION["admin"]->id_office_admin."&select=id_book";
			$method = "GET";
			$fields = array();

			$getBook = CurlController::request($url,$method,$fields);

			if($getBook->status != 200){

				/*=============================================
				Creamos la reserva
				=============================================*/

				$num_book = TemplateController::genNums();

				$url = "books?token=".$_SESSION["admin"]->token_admin."&table=admins&suffix=admin";
				$method = "POST";
				$fields = array(
					"num_book" => $num_book,
					"id_table_book" => $_POST["id_table_book"],
					"date_book" => $date_book,
					"time_book" => $_POST["time_book"],
					"client_book" => trim($_POST["client_book"]),
					"email_book" => trim($_POST["email_book"]),
					"phone_book" => trim($_POST["phone_book"]),
					"description_book" => trim($_POST["description_book"]),
					"id_office_book" => $_SESSION["admin"]->id_office_admin,
					"date_created_book" => date("y-m-d")
				);

				$createBook = CurlController::request($url,$method,$fields);

				if($createBook->status == 200){

					echo'

					<script>

						fncMatPreloader("off");
						fncFormatInputs();
					    fncSweetAlert("success","La reserva ha sido creada con éxito",setTimeout(()=>location.reload(),1250));	

					</script>

					';
				}

			}else{

				echo'

				<script>

					fncMatPreloader("off");
					fncFormatInputs();
				    fncToastr("error","Error al reservar: La fecha y hora ya está reservada");	

				</script>

				';

			}

		}
	
	}

}