
<?php

if(isset($_GET["transactionOrder"])){

  $categories = getCategories();
  $foods = getProducts($categories);

  $sales = array();

  /*=============================================
  Buscar orden de acuerdo al Id
  =============================================*/

  $url = "relations?rel=orders,tables&type=order,table&linkTo=transaction_order&equalTo=".$_GET["transactionOrder"];
  $method = "GET";
  $fields = array();

  $getOrder = CurlController::request($url,$method,$fields);

  if($getOrder->status == 200){

    $idOrder = $getOrder->results[0]->id_order;
    $transactionOrder = $getOrder->results[0]->transaction_order;
    $dateOrder = $getOrder->results[0]->date_order;
    $noteOrder = $getOrder->results[0]->note_order;
    $processOrder = $getOrder->results[0]->process_order;
    $titleTable = $getOrder->results[0]->title_table;
    $idTable = $getOrder->results[0]->id_table;

    /*=============================================
    Capturar items de ventas
    =============================================*/

    $url = "relations?rel=sales,foods&type=sale,food&linkTo=id_order_sale&equalTo=".$idOrder;
    $method = "GET";
    $fields = array();

    $getSales = CurlController::request($url,$method,$fields);

    if($getSales->status == 200){

      $sales = $getSales->results;
    
    }

  }

}else if(isset($_GET["idTable"])){

  $categories = getCategories();
  $foods = getProducts($categories);
  $titleTable = isset($_GET["titleTable"]) ? $_GET["titleTable"] : "";
  $idTable = $_GET["idTable"];

  $sales = array();

  /*=============================================
  Buscar orden abierta para esta mesa
  =============================================*/

  $url = "orders?linkTo=id_table_order,id_office_order,status_order&equalTo=".$_GET["idTable"].",".$_SESSION["admin"]->id_office_admin.",Pendiente";
  $method = "GET";
  $fields = array();

  $getOrder = CurlController::request($url,$method,$fields);

  if($getOrder->status == 404){

    /*=============================================
    Crear la orden
    =============================================*/

    $transactionOrder = TemplateController::genNums();

    $url = "orders?token=".$_SESSION["admin"]->token_admin."&table=admins&suffix=admin";
    $method = "POST";
    $fields = array(
      "transaction_order" => $transactionOrder,
      "id_table_order" => $_GET["idTable"],
      "id_admin_order" => $_SESSION["admin"]->id_admin,
      "id_office_order" => $_SESSION["admin"]->id_office_admin,
      "status_order" => "Pendiente",
      "process_order" => "Ordenando",
      "date_order" => date("Y-m-d H:m:i"),
      "date_created_order" => date("Y-m-d")
    );

    $createOrder = CurlController::request($url,$method,$fields);

    if($createOrder->status == 200){

      $idOrder = $createOrder->results->lastId;
      $dateOrder = $fields["date_order"];
      $noteOrder = "";
      $processOrder = $fields["process_order"];

      /*=============================================
      Actualizar la mesa
      =============================================*/

      $url = "tables?id=".$_GET["idTable"]."&nameId=id_table&token=".$_SESSION["admin"]->token_admin."&table=admins&suffix=admin";
      $method = "PUT";
      $fields = array(
        "status_table" => "ocupada"
      );

      $fields = http_build_query($fields);

      $updateTable = CurlController::request($url,$method,$fields);

    }

  }else{

    $idOrder = $getOrder->results[0]->id_order;
    $transactionOrder = $getOrder->results[0]->transaction_order;
    $dateOrder = $getOrder->results[0]->date_order;
    $noteOrder = $getOrder->results[0]->note_order;
    $processOrder = $getOrder->results[0]->process_order;

    /*=============================================
    Capturar items de ventas
    =============================================*/

    $url = "relations?rel=sales,foods&type=sale,food&linkTo=id_order_sale&equalTo=".$idOrder;
    $method = "GET";
    $fields = array();

    $getSales = CurlController::request($url,$method,$fields);

    if($getSales->status == 200){

      $sales = $getSales->results;
    
    }


  }

  
}else{

  echo '<script>
  window.location = "/welcome";
  </script>';

}

/*=============================================
  Traemos las categorías
=============================================*/

function getCategories(){

  $url = "categories?orderBy=order_category&orderMode=ASC&linkTo=status_category&equalTo=1";
  $method = "GET";
  $fields = array();

  $getCategories = CurlController::request($url,$method,$fields);

  if($getCategories->status == 200){

    return $getCategories->results;
    

  }else{

    echo '<script>
    window.location = "/welcome";
    </script>';
  
  }

}

/*=============================================
Traemos los productos
=============================================*/

function getProducts($categories){

  $url = "relations?rel=foods,categories&type=food,category&linkTo=id_office_food&equalTo=".$_SESSION["admin"]->id_office_admin;
  $method = "GET";
  $fields = array();

  $getFoods = CurlController::request($url,$method,$fields);

  if($getFoods->status == 200){

    $foods = $getFoods->results;

    foreach ($categories as $key => $value) {

      foreach ($foods as $index => $item) {

        if($value->id_category == $item->id_category_food){

          $value->foods[$index] = $item;
        
        }

      }

    }

    return $foods;

  }

}


?>

<style>
  
:root{

  --color-primario: <?php echo $admin->color_admin ?> !important; 
}

</style>


<link rel="stylesheet" href="/views/assets/css/pos/pos.css">

<div class="container-fluid py-3 p-lg-4 pos-container">
        
  <!-- POS Header -->
  <?php include "modules/header/header.php"; ?>

  <div class="row">
    <!-- Menu Section -->
    <div class="col-lg-8">
      <div class="menu-section">
        
        <!-- Category Tabs -->
        <?php include "modules/categories/categories.php"; ?>
        

        <!-- Menu Items Grid -->
        <div class="menu-items-grid">
          
          <?php include "modules/foods/foods.php"; ?>

        </div>
      </div>
    </div>

    <!-- Order Summary Sidebar -->
    <div class="col-lg-4">
       <?php include "modules/panel/panel.php"; ?>
       <?php include "views/modules/modals/checkout.php"; ?>
    </div>
  </div>

</div>

<!-- POS JavaScript -->
<script src="/views/assets/js/pos/pos.js"></script>

