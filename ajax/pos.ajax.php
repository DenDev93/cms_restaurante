<?php

require_once "../controllers/curl.controller.php";

class PosController{

	/*=============================================
	Administrar items de venta
	=============================================*/

	public $items_sale;
	public $id_admin;
	public $id_office;
	public $token;

	public function manageSales(){

		/*=============================================
		Traemos las ventas si están pendientes de envío a cocina
		=============================================*/

		$url = "sales?linkTo=id_order_sale,process_sale&equalTo=".json_decode($this->items_sale)[0]->order.",Pendiente";
		$method = "GET";
		$fields = array();

		$getSales = CurlController::request($url,$method,$fields);

		if($getSales->status == 200){

			$sales = $getSales->results;

			foreach ($sales as $key => $value) {
				
				/*=============================================
				Eliminar todos los items de ventas si están pendientes de envío
				=============================================*/

				$url = "sales?id=".$value->id_sale."&nameId=id_sale&token=".$this->token."&table=admins&suffix=admin";
				$method = "DELETE";
				$fields = array();

				$deleteSales = CurlController::request($url,$method,$fields);	
			
			}

		}

		/*=============================================
		Creamos nuevamente los pedidos
		=============================================*/

		$itemsDecoded = json_decode($this->items_sale);

		$hasItems = false;
		$totalItems = 0;

		foreach ($itemsDecoded as $key => $value) {
			
			if(isset($value->id)){

				$hasItems = true;
				$totalItems++;

			}

		}

		if($hasItems){

			$countSales = 0;

			foreach ($itemsDecoded as $key => $value) {

				if(!isset($value->id)){

					continue;

				}

				$url = "sales?token=".$this->token."&table=admins&suffix=admin";
				$method = "POST";
				$fields = array(
					"id_order_sale" => $value->order,
					"id_food_sale" => $value->id,
					"qty_sale" => $value->quantity,
					"subtotal_sale" => $value->price*$value->quantity,
					"status_sale" => "Pendiente",
					"id_admin_sale" => $this->id_admin,
					"id_office_sale" => $this->id_office,
					"process_sale" => "Pendiente",
					"date_created_sale" => date("Y-m-d")
				);

				$createSale = CurlController::request($url,$method,$fields);

				if($createSale->status == 200){

					$countSales++;

					if($countSales == $totalItems){

						/*=============================================
						Actualizamos la orden
						=============================================*/
						
						$url = "orders?id=".$itemsDecoded[0]->order."&nameId=id_order&token=".$this->token."&table=admins&suffix=admin";
						$method = "PUT";
						$fields = array(
							"note_order" => $itemsDecoded[0]->note
						);

						$fields = http_build_query($fields);

  						$updateOrder = CurlController::request($url,$method,$fields);

					}		

				}

			}
		
		}else{

			/*=============================================
			Actualizamos la orden
			=============================================*/
			
			$url = "orders?id=".$itemsDecoded[0]->order."&nameId=id_order&token=".$this->token."&table=admins&suffix=admin";
			$method = "PUT";
			$fields = array(
				"note_order" => $itemsDecoded[0]->note
			);

			$fields = http_build_query($fields);

			$updateOrder = CurlController::request($url,$method,$fields);

		}
	
	}

	/*=============================================
	Actualizar proceso de la orden
	=============================================*/

	public $id_order;
	public $process_order;

	public function updateOrder(){

		/*=============================================
		Si la orden se envía a cocina, actualizar los items pendientes
		=============================================*/

		if($this->process_order == "Preparando"){

			$url = "sales?linkTo=id_order_sale&equalTo=".$this->id_order;
			$method = "GET";
			$fields = array();

			$getSales = CurlController::request($url,$method,$fields);

			if($getSales->status == 200){

				foreach ($getSales->results as $key => $value) {
					
					if($value->process_sale == "Pendiente"){

						$url = "sales?id=".$value->id_sale."&nameId=id_sale&token=".$this->token."&table=admins&suffix=admin";
						$method = "PUT";
						$fields = array(
							"process_sale" => "Preparando"
						);

						$fields = http_build_query($fields);

						CurlController::request($url,$method,$fields);

					}

				}

			}

		}

		/*=============================================
		Actualizamos la orden
		=============================================*/
		$url = "orders?id=".$this->id_order."&nameId=id_order&token=".$this->token."&table=admins&suffix=admin";
		$method = "PUT";
		$fields = array(
			"process_order" => $this->process_order
		);

		$fields = http_build_query($fields);

		$updateOrder = CurlController::request($url,$method,$fields);

		if($updateOrder->status == 200){

			echo 200;

		}else{

			echo $updateOrder->status;

		}
	
	}

	/*=============================================
	Actualizar proceso de la venta
	=============================================*/

	public $id_sale;
	public $process_sale;

	public function updateSale(){

		/*=============================================
		Disminuir el stock de los insumos solo al servir el item
		=============================================*/

		if($this->process_sale == "Entregada"){

			$url = "relations?rel=sales,foods&type=sale,food&linkTo=id_sale&equalTo=".$this->id_sale;
	    $method = "GET";
	    $fields = array();

	    $getFood = CurlController::request($url,$method,$fields);

	    if($getFood->status == 200){

	    	$food = $getFood->results[0];

	    	$products = json_decode($food->products_food);

	    	if($products && is_array($products)){

		    	foreach ($products as $key => $value) {
	    		
	    		/*=============================================
				Traer productos por SKU
				=============================================*/

				$url = "products?linkTo=sku_product&equalTo=".$value->sku;
				$method = "GET";
				$fields = array();

				$getProduct = CurlController::request($url,$method,$fields);
				
				if($getProduct->status == 200){

					$product = $getProduct->results[0];

					/*=============================================
					Preguntar si es un producto terminado o tiene ingredientes
					=============================================*/

					$ingredients = json_decode(urldecode($product->ingredients_product));

					if($ingredients && is_array($ingredients) && count($ingredients) > 0){

						foreach (json_decode(urldecode($product->ingredients_product)) as $index => $item) {

							$url = "ingredients?linkTo=sku_ingredient&equalTo=".$item->sku;
							$method = "GET";
							$fields = array();

							$getIngredients = CurlController::request($url,$method,$fields);

							if($getIngredients->status == 200){

								$ingredient = $getIngredients->results[0];
								
								/*=============================================
								Ir a la tabla de compras para disminuir el stock
								=============================================*/

								$url = "purchases?linkTo=id_ingredient_purchase,id_office_purchase&equalTo=".$ingredient->id_ingredient.",".$this->id_office."&orderBy=id_purchase&orderMode=DESC&startAt=0&endAt=1";
								$method = "GET";
								$fields = array();

								$getPurchase = CurlController::request($url,$method,$fields);
								
								if($getPurchase->status == 200){

									$purchase = $getPurchase->results[0];

if($purchase->stock_purchase > 0){

								$url = "purchases?id=".$purchase->id_purchase."&nameId=id_purchase&token=".$this->token."&table=admins&suffix=admin";
								$method = "PUT";
								$fields = array(
									"stock_purchase" => $purchase->stock_purchase-$item->cantidad
								);

								$fields = http_build_query($fields);

								$updatePurchase = CurlController::request($url,$method,$fields);

							}
								
								}

							}

						}

					}else{

						/*=============================================
						Ir a la tabla de compras para disminuir el stock
						=============================================*/

						$url = "purchases?linkTo=id_product_purchase,id_office_purchase&equalTo=".$product->id_product.",".$this->id_office."&orderBy=id_purchase&orderMode=DESC&startAt=0&endAt=1";
						$method = "GET";
						$fields = array();

						$getPurchase = CurlController::request($url,$method,$fields);
						
						if($getPurchase->status == 200){

							$purchase = $getPurchase->results[0];

if($purchase->stock_purchase > 0){

							$url = "purchases?id=".$purchase->id_purchase."&nameId=id_purchase&token=".$this->token."&table=admins&suffix=admin";
							$method = "PUT";
							$fields = array(
								"stock_purchase" => $purchase->stock_purchase-$value->cantidad
							);

							$fields = http_build_query($fields);

							$updatePurchase = CurlController::request($url,$method,$fields);

						}
						
						}
					}

				}
	    	
	    	}

	    }

	    }

		}

		/*=============================================
		Actualizamos la venta
		=============================================*/

		$url = "sales?id=".$this->id_sale."&nameId=id_sale&token=".$this->token."&table=admins&suffix=admin";
		$method = "PUT";
		$fields = array(
			"process_sale" => $this->process_sale
		);

		$fields = http_build_query($fields);

		$updateSale = CurlController::request($url,$method,$fields);

		if($updateSale->status == 200){

			/*=============================================
			Si el item quedó listo, verificar si toda la orden está lista
			=============================================*/

			if($this->process_sale == "Lista"){

				$url = "sales?linkTo=id_sale&equalTo=".$this->id_sale;
				$method = "GET";
				$fields = array();

				$getSale = CurlController::request($url,$method,$fields);

				if($getSale->status == 200){

					$idOrder = $getSale->results[0]->id_order_sale;

					$url = "sales?linkTo=id_order_sale&equalTo=".$idOrder;
					$method = "GET";
					$fields = array();

					$getSalesOrder = CurlController::request($url,$method,$fields);

					if($getSalesOrder->status == 200){

						$allReady = true;

						foreach ($getSalesOrder->results as $key => $value) {
							
							if($value->process_sale == "Pendiente" || $value->process_sale == "Preparando"){

								$allReady = false;

							}

						}

						if($allReady){

							$url = "orders?id=".$idOrder."&nameId=id_order&token=".$this->token."&table=admins&suffix=admin";
							$method = "PUT";
							$fields = array(
								"process_order" => "Lista"
							);

							$fields = http_build_query($fields);

							CurlController::request($url,$method,$fields);

						}

					}

				}

			}

			echo 200;

		}else{

			echo $updateSale->status;

		}
	}

	/*=============================================
	Eliminar órden
	=============================================*/

	public $id_order_delete;
	public $id_table_delete;

	public function deleteOrder(){


		/*=============================================
		Traemos las ventas
		=============================================*/

		$url = "sales?linkTo=id_order_sale&equalTo=".$this->id_order_delete;
		$method = "GET";
		$fields = array();

		$getSales = CurlController::request($url,$method,$fields);

		if($getSales->status == 200){

			$sales = $getSales->results;

			$countSales = 0;

			foreach ($sales as $key => $value) {
				
				/*=============================================
				Eliminar todos los items de ventas
				=============================================*/

				$url = "sales?id=".$value->id_sale."&nameId=id_sale&token=".$this->token."&table=admins&suffix=admin";
				$method = "DELETE";
				$fields = array();

				$deleteSales = CurlController::request($url,$method,$fields);	

				if($deleteSales->status == 200){

					$countSales++;

					if($countSales == count($sales)){

						/*=============================================
						Eliminar la orden
						=============================================*/

						$url = "orders?id=".$this->id_order_delete."&nameId=id_order&token=".$this->token."&table=admins&suffix=admin";
						$method = "DELETE";
						$fields = array();

						$deleteOrder = CurlController::request($url,$method,$fields);	

						if($deleteOrder->status == 200){

							/*=============================================
							liberar la mesa
							=============================================*/

							$url = "tables?id=".$this->id_table_delete."&nameId=id_table&token=".$this->token."&table=admins&suffix=admin";
							$method = "PUT";
							$fields = array(
								"status_table" => "libre"
							);

							$fields = http_build_query($fields);

							$updateTable = CurlController::request($url,$method,$fields);

							if($updateTable->status == 200){

								echo 200;
							}
						}
					
					}
				
				}
			
			}

		}
	
	}


}

/*=============================================
Variables POST
=============================================*/ 

if(isset($_POST["items_sale"])){

	$ajax = new PosController();
	$ajax -> items_sale = $_POST["items_sale"];
	$ajax -> id_admin = base64_decode($_POST["id_admin"]);
	$ajax -> id_office = base64_decode($_POST["id_office"]);
	$ajax -> token = $_POST["token"];
	$ajax -> manageSales(); 

}

if(isset($_POST["id_order"])){

	$ajax = new PosController();
	$ajax -> id_order = $_POST["id_order"];
	$ajax -> process_order = $_POST["process_order"];
	$ajax -> token = $_POST["token"];
	$ajax -> updateOrder(); 

}

if(isset($_POST["id_sale"])){

	$ajax = new PosController();
	$ajax -> id_sale = $_POST["id_sale"];
	$ajax -> id_office = base64_decode($_POST["id_office"]);
	$ajax -> process_sale = $_POST["process_sale"];
	$ajax -> token = $_POST["token"];
	$ajax -> updateSale(); 

}

if(isset($_POST["id_order_delete"])){

	$ajax = new PosController();
	$ajax -> id_order_delete = $_POST["id_order_delete"];
	$ajax -> id_table_delete = $_POST["id_table_delete"];
	$ajax -> token = $_POST["token"];
	$ajax -> deleteOrder(); 

}

