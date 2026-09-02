<?php 

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelController{

	public function manageExcel(){

		if(isset($_FILES['archivo_excel'])){

			echo '<script>

				fncMatPreloader("on");
			    fncSweetAlert("loading", "Procesando...", "");

			</script>';

			$archivo = $_FILES['archivo_excel']['tmp_name'];

			$spreadsheet = IOFactory::load($archivo);
			$hoja = $spreadsheet->getActiveSheet();
			$filas = $hoja->toArray();
			
			$columns = array_filter($filas[0]);
			$countData = 0;
			
			for ($i = 1; $i < count($filas); $i++) {
		
				$url = $_POST["titleTable"]."?token=".$_SESSION["admin"]->token_admin."&table=admins&suffix=admin";
				$method = "POST";
				$fields = array_combine($columns, array_slice($filas[$i], 0, count($columns)));
				
				$saveData = CurlController::request($url,$method,$fields);

				if($saveData->status == 200){

					$countData++;

					if($countData == count($filas)-1){

						echo '

						<script>

							fncMatPreloader("off");
							fncFormatInputs();
						    fncSweetAlert("success","Los datos han sido creados con éxito",setTimeout(()=>location.reload(),1250));	

						</script>

						';
					}
				}
			}
		
		}

	}
}