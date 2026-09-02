<?php

$idOrder = "";
$transactionOrder = "";
$dateOrder = "";
$noteOrder = "";
$processOrder = "";
$titleTable = "";
$idTable = "";
$sales = array();

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
      "date_order" => date("Y-m-d H:i:s"),
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

    }else{

      echo '<script>
      alert("Su sesión ha expirado, vuelva a iniciar sesión");
      window.location = "/login";
      </script>';

    }

  }else if($getOrder->status == 200){

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

    return array();

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

  foreach ($categories as $key => $value) {

    $value->foods = array();

  }

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

  }else{

    return array();

  }

}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="mobile-web-app-capable" content="yes">
  <title>POS - <?php echo urldecode($titleTable) ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
  :root{
    --color-primario: <?php echo $admin->color_admin ?> !important; 
  }
  </style>

  <link rel="stylesheet" href="/views/assets/css/pos/pos.css?v=<?php echo time() ?>">
  <link rel="stylesheet" href="/views/assets/css/checkout/checkout.css">
</head>
<body class="pos-body">
  <div class="pos-app" id="posApp">
    
    <!-- POS Header -->
    <?php include "modules/header/header.php"; ?>

    <div class="pos-main">
      
      <!-- MENU SECTION (Left) -->
      <aside class="pos-menu" id="posMenu" role="complementary" aria-label="Menú de productos">
        
        <!-- Category Tabs -->
        <nav class="category-tabs-container" role="tablist" aria-label="Categorías del menú">
          <?php include "modules/categories/categories.php"; ?>
        </nav>

        <!-- Menu Items Grid -->
        <div class="menu-items-container" id="menuItemsContainer">
          <?php include "modules/foods/foods.php"; ?>
        </div>

      </aside>

      <!-- ORDER PANEL (Right) -->
      <aside class="pos-order" id="posOrder" role="complementary" aria-label="Resumen de la orden">
        <?php include "modules/panel/panel.php"; ?>
        <?php include "views/modules/modals/checkout.php"; ?>
      </aside>

    </div>

    <!-- Floating Action Buttons (Mobile) -->
    <div class="pos-fab-container" id="posFabContainer" aria-hidden="true">
      <button class="pos-fab" id="toggleMenuFab" aria-label="Mostrar menú" aria-expanded="false">
        <svg class="fab-icon-menu" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor"/>
          <path d="M9 9h6M9 15h6M9 21h4" stroke="currentColor" stroke-width="2"/>
        </svg>
        <svg class="fab-icon-close" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none">
          <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2"/>
          <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2"/>
        </svg>
      </button>
      
      <button class="pos-fab pos-fab-order" id="toggleOrderFab" aria-label="Ver orden" aria-expanded="false">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" stroke="currentColor"/>
          <line x1="3" y1="6" x2="21" y2="6" stroke="currentColor"/>
          <path d="M16 10a4 4 0 0 1-8 0" stroke="currentColor"/>
        </svg>
        <span class="fab-badge" id="orderItemCount" style="display:none">0</span>
      </button>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="pos-overlay" id="posOverlay" aria-hidden="true"></div>

  </div>

  <!-- Checkout Modal (from checkout.php) -->
  <?php include "views/modules/modals/checkout.php"; ?>

  <!-- Toast Container -->
  <div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true"></div>

  <!-- Keyboard Shortcuts Help Modal -->
  <div class="modal fade" id="shortcutsModal" tabindex="-1" aria-labelledby="shortcutsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="shortcutsModalLabel">Atajos de Teclado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <dl class="row shortcuts-list">
            <dt class="col-4"><kbd>F1</kbd></dt><dd class="col-8">Abrir esta ayuda</dd>
            <dt class="col-4"><kbd>F2</kbd></dt><dd class="col-8">Enviar orden a cocina</dd>
            <dt class="col-4"><kbd>F3</kbd></dt><dd class="col-8">Abrir checkout / Pagar</dd>
            <dt class="col-4"><kbd>F4</kbd></dt><dd class="col-8">Limpiar orden actual</dd>
            <dt class="col-4"><kbd>Escape</kbd></dt><dd class="col-8">Cerrar modales / Volver</dd>
            <dt class="col-4"><kbd>Enter</kbd></dt><dd class="col-8">Confirmar en modales</dd>
            <dt class="col-4"><kbd>↑/↓</kbd></dt><dd class="col-8">Navegar items de orden</dd>
            <dt class="col-4"><kbd>+/-</kbd></dt><dd class="col-8">Ajustar cantidad (item seleccionado)</dd>
            <dt class="col-4"><kbd>Delete</kbd></dt><dd class="col-8">Eliminar item seleccionado</dd>
            <dt class="col-4"><kbd>1-9</kbd></dt><dd class="col-8">Cambiar a categoría 1-9</dd>
          </dl>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>

  <!-- Scripts -->
  <script src="/views/assets/js/pos/pos.js?v=<?php echo time() ?>"></script>
  <script src="/views/assets/js/checkout/checkout.js?v=<?php echo time() ?>"></script>
</body>
</html>