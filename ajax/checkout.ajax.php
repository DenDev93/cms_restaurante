<?php

require_once "../controllers/curl.controller.php";

class CheckoutController{

	public $subtotal;
    public $tax;
    public $total;
    public $tip;
    public $idOrder;
    public $idTable;
    public $method;
    public $received;
    public $change;
    public $currency;
    public $token;

    public function manageOrder(){

    	/*=============================================
		Actualizar la mesa
		=============================================*/ 
		$url = "tables?id=".$this->idTable."&nameId=id_table&token=".$this->token."&table=admins&suffix=admin";
		$method = "PUT";
		$fields = array(
			"status_table" => "pagando"
		);

		$fields = http_build_query($fields);

		$updateTable = CurlController::request($url,$method,$fields);

		if($updateTable->status == 200){

	    	/*=============================================
			Actualizar los items de venta
			=============================================*/ 

			$url = "sales?linkTo=id_order_sale,status_sale&equalTo=".$this->idOrder.",Pendiente";
			$method = "GET";
			$fields = array();

			$getSales = CurlController::request($url,$method,$fields);

			if($getSales->status == 200){

				$sales = $getSales->results;

				$countSale = 0;

				foreach ($sales as $key => $value) {

					$url = "sales?id=".$value->id_sale."&nameId=id_sale&token=".$this->token."&table=admins&suffix=admin";
					$method = "PUT";
					$fields = array(
						"status_sale" => "Completada"
					);

					$fields = http_build_query($fields);

					$updateSale = CurlController::request($url,$method,$fields);

					if($updateSale->status == 200){

						$countSale++;

						if($countSale == count($sales)){

							/*=============================================
							Actualizar la orden
							=============================================*/ 
							$url = "orders?id=".$this->idOrder."&nameId=id_order&token=".$this->token."&table=admins&suffix=admin";
							$method = "PUT";
							$fields = array(
								"subtotal_order" => $this->subtotal,
								"tax_order" => $this->tax,
								"tip_order" => $this->tip,
								"total_order" => $this->total,
								"method_order" => $this->method,
								"received_order" => $this->received,
								"change_order" => $this->change,
								"currency_order" => $this->currency,
								"status_order" => "Completada",
								"process_order" => "Entregada"
							);

							$fields = http_build_query($fields);

							$updateOrder = CurlController::request($url,$method,$fields);

							if($updateOrder->status == 200){

								/*=============================================
								Actualizar la mesa
								=============================================*/ 
								$url = "tables?id=".$this->idTable."&nameId=id_table&token=".$this->token."&table=admins&suffix=admin";
								$method = "PUT";
								$fields = array(
									"status_table" => "libre"
								);

								$fields = http_build_query($fields);

								$updateTable = CurlController::request($url,$method,$fields);

								if($updateTable->status == 200){

									echo 200;
								}

							}else{

								echo "error";
							}


						}
					}else{

						echo "error";
					}

				}

			}else{

				echo "error";
			}

		}else{

			echo "error";
		}
    }

}

/*=============================================
Variables POST
=============================================*/ 

if(isset($_POST["method"])){

	$ajax = new CheckoutController();
	$ajax -> subtotal = $_POST["subtotal"];
    $ajax -> tax = $_POST["tax"];
    $ajax -> total = $_POST["total"];
    $ajax -> tip = $_POST["tip"];
    $ajax -> idOrder = $_POST["idOrder"];
    $ajax -> idTable = $_POST["idTable"];
    $ajax -> method = $_POST["method"];
    $ajax -> received = $_POST["received"];
    $ajax -> change = $_POST["change"];
    $ajax -> currency = $_POST["currency"];
    $ajax -> token = $_POST["token"];
    $ajax -> manageOrder(); 
}

