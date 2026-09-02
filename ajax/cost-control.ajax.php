<?php

require_once "../controllers/curl.controller.php";

class CostControlController{

	public $products_food;
	public $id_office_food;
	public $utility;

	public function updatePriceFood(){

		$totalCost = 0;
		

		foreach (json_decode($this->products_food) as $key => $value) {

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

				if(count(json_decode(urldecode($product->ingredients_product))) > 0){
				
					foreach (json_decode(urldecode($product->ingredients_product)) as $index => $item) {

						/*=============================================
						Traer ingredientes por SKU
						=============================================*/

						$url = "ingredients?linkTo=sku_ingredient&equalTo=".$item->sku;
						$method = "GET";
						$fields = array();

						$getIngredients = CurlController::request($url,$method,$fields);

						if($getIngredients->status == 200){

							$ingredient = $getIngredients->results[0];
							
							/*=============================================
							Buscar compra de los ingredientes
							=============================================*/

							$url = "purchases?linkTo=id_ingredient_purchase,id_office_purchase&equalTo=".$ingredient->id_ingredient.",".$this->id_office_food."&select=unit_cost_purchase,stock_purchase";
							$method = "GET";
							$fields = array();

							$getPurchase = CurlController::request($url,$method,$fields);
							
							if($getPurchase->status == 200){

								$purchase = $getPurchase->results;

								foreach ($purchase as $num => $elem) {

									if($elem->stock_purchase > 0){

										if($elem->unit_cost_purchase > 0){

											$ingredientCost = $elem->unit_cost_purchase*$item->cantidad;

											$totalCost += $ingredientCost;

										}else{

											echo "error";
										}

										break;

									}

								}

							}else{

								echo "error";
							}

						}
					
					}

				}else{

					/*=============================================
					Buscar compra de productos terminados
					=============================================*/

					$url = "purchases?linkTo=id_product_purchase,id_office_purchase&equalTo=".$product->id_product.",".$this->id_office_food."&select=unit_cost_purchase,stock_purchase";
					$method = "GET";
					$fields = array();

					$getPurchase = CurlController::request($url,$method,$fields);
								
					if($getPurchase->status == 200){

					    $purchase = $getPurchase->results;

						foreach ($purchase as $num => $elem) {

							if($elem->stock_purchase > 0){

								if($elem->unit_cost_purchase > 0){

									$productCost = $elem->unit_cost_purchase*$value->cantidad;

									$totalCost += $productCost;

								}else{

									echo "error";
								}

								break;

							}

						}

					}else{

						echo "error";
					}

				}


			}



		}

		/*=============================================
		Calcular costo total
		=============================================*/

		if($totalCost == 0){

			echo "error";
		
		}else{

			$price = $totalCost + ($totalCost*($this->utility/100));

			$cost = array(

				"totalCost" => number_format($totalCost,2),
				"price" => number_format($price,2)

			);

			echo json_encode($cost);
		}

	}

}

/*=============================================
Variables POST
=============================================*/ 

if(isset($_POST["products_food"])){

	$ajax = new CostControlController();
	$ajax -> products_food = $_POST["products_food"];
	$ajax -> id_office_food = $_POST["id_office_food"];
	$ajax -> utility = $_POST["utility"];
	$ajax -> updatePriceFood(); 

}
