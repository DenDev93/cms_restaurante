<?php

ob_start();
session_start();

/*=============================================
Capturar parámetros de la url
=============================================*/

$routesArray = explode("/", $_SERVER["REQUEST_URI"]);
array_shift($routesArray);
foreach ($routesArray as $key => $value) {
    $routesArray[$key] = explode("?",$value)[0];
}

/*=============================================
Validar si existe la base de datos con la tabla admins
=============================================*/

$url = "admins?startAt=0&endAt=1&orderBy=id_admin&orderMode=ASC";
$method = "GET";
$fields = array();

$adminTable = CurlController::request($url,$method,$fields);

if($adminTable->status == 404){
    $admin = null;
}else{
    $admin = $adminTable->results[0];
}

require_once "config/currency.php";
$tax = 0.15;
$tip = 0.1;

if (!isset($_SESSION["admin"])) {
    header("Location: /");
    exit;
}

/*=============================================
Traer datos de la orden
=============================================*/

$idOrder = "";
$transactionOrder = "";
$dateOrder = "";
$noteOrder = "";
$processOrder = "";
$titleTable = "";
$idTable = "";
$sales = array();

if(isset($_GET["transactionOrder"])){

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

        $url = "relations?rel=sales,foods&type=sale,food&linkTo=id_order_sale&equalTo=".$idOrder;
        $method = "GET";
        $fields = array();

        $getSales = CurlController::request($url,$method,$fields);
        if($getSales->status == 200){
            $sales = $getSales->results;
        }
    }

}else if(isset($_GET["idTable"])){

    $titleTable = isset($_GET["titleTable"]) ? $_GET["titleTable"] : "";
    $idTable = $_GET["idTable"];
    $sales = array();

    $url = "orders?linkTo=id_table_order,id_office_order,status_order&equalTo=".$_GET["idTable"].",".$_SESSION["admin"]->id_office_admin.",Pendiente";
    $method = "GET";
    $fields = array();

    $getOrder = CurlController::request($url,$method,$fields);

    if($getOrder->status == 404){
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
            $url = "tables?id=".$_GET["idTable"]."&nameId=id_table&token=".$_SESSION["admin"]->token_admin."&table=admins&suffix=admin";
            $method = "PUT";
            $fields = array("status_table" => "ocupada");
            $fields = http_build_query($fields);
            CurlController::request($url,$method,$fields);
        }else{
            echo '<script>alert("Su sesión ha expirado, vuelva a iniciar sesión");window.location = "/login";</script>';
        }
    }else if($getOrder->status == 200){
        $idOrder = $getOrder->results[0]->id_order;
        $transactionOrder = $getOrder->results[0]->transaction_order;
        $dateOrder = $getOrder->results[0]->date_order;
        $noteOrder = $getOrder->results[0]->note_order;
        $processOrder = $getOrder->results[0]->process_order;

        $url = "relations?rel=sales,foods&type=sale,food&linkTo=id_order_sale&equalTo=".$idOrder;
        $method = "GET";
        $fields = array();
        $getSales = CurlController::request($url,$method,$fields);
        if($getSales->status == 200){
            $sales = $getSales->results;
        }
    }
}else{
    echo '<script>window.location = "/welcome";</script>';
}

/*=============================================
Traer categorías y productos
=============================================*/

function getCategories(){
    $url = "categories?orderBy=order_category&orderMode=ASC&linkTo=status_category&equalTo=1";
    $method = "GET";
    $fields = array();
    $getCategories = CurlController::request($url,$method,$fields);
    return ($getCategories->status == 200) ? $getCategories->results : array();
}

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
    }
    return array();
}

$categories = getCategories();
$foods = getProducts($categories);

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
    <style>:root{--color-primario: <?php echo $admin->color_admin ?> !important;}</style>
    <link rel="stylesheet" href="/views/assets/css/pos/pos.css?v=<?php echo time() ?>">
    <link rel="stylesheet" href="/views/assets/css/checkout/checkout.css?v=<?php echo time() ?>">
</head>
<body class="pos-body">
    <div class="pos-app" id="posApp">
        <div class="pos-header">
            <h1 class="table-title"><?php echo urldecode($titleTable) ?></h1>
            <?php if ($processOrder != "Entregada"): ?><span class="badge-live">En vivo</span><?php endif; ?>
            <div class="header-actions">
                <a href="/ordenes" class="btn btn-outline-light btn-sm me-2 rounded"><i class="bi bi-clock-history"></i> Historial</a>
                <a href="/" class="btn btn-outline-light btn-sm me-2 rounded"><i class="bi bi-arrow-left"></i> Mesas</a>
                <?php if ($processOrder != "Entregada"): ?>
                    <button class="btn btn-outline-light btn-sm rounded deleteOrder" idOrder="<?php echo $idOrder ?>" processOrder="<?php echo $processOrder ?>" idTable="<?php echo $idTable ?>"><i class="bi bi-receipt"></i> Cancelar</button>
                <?php endif; ?>
            </div>
        </div>
        <div class="pos-main">
            <aside class="pos-menu" id="posMenu" role="complementary" aria-label="Menú de productos">
                <nav class="category-tabs-container" role="tablist" aria-label="Categorías del menú">
                    <div class="category-tabs">
                        <?php foreach ($categories as $key => $value): ?>
                            <button class="category-tab <?php if ($key == 0): ?>active<?php endif ?>" data-category="<?php echo $value->id_category ?>">
                                <img src="<?php echo urldecode($value->img_category) ?>" loading="lazy" onerror="this.src='https://placehold.co/50x50/28a745/ffffff?text=+';">
                                <?php echo urldecode($value->title_category) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </nav>
                <div class="menu-items-container" id="menuItemsContainer">
                    <div class="menu-items-grid">
                        <?php foreach ($categories as $key => $value): ?>
                            <div class="menu-category <?php if ($key == 0): ?>active<?php endif ?>" id="<?php echo $value->id_category ?>">
                                <?php foreach (($value->foods ?? array()) as $index => $item): ?>
                                    <div class="menu-item" data-item="<?php echo urldecode($item->id_food) ?>" data-price="<?php echo $item->price_food ?>" data-name="<?php echo urldecode($item->title_food) ?>">
                                        <div class="menu-item-image"><img src="<?php echo urldecode($item->img_food) ?>" alt="<?php echo urldecode($item->title_food) ?>" loading="lazy" onerror="this.src='https://placehold.co/200x200/28a745/ffffff?text=+';" style="width:100%;aspect-ratio:1/1;object-fit:cover;object-position:center;display:block;"></div>
                                        <div class="menu-item-info"><h6 class="menu-item-name"><?php echo urldecode($item->title_food) ?></h6><span class="menu-item-price"><?php echo fncMoney($item->price_food) ?></span></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
            <aside class="pos-order" id="posOrder" role="complementary" aria-label="Resumen de la orden">
                <div class="order-summary">
                    <div class="order-summary-header">
                        <h4 id="transactionOrder" idOrder="<?php echo $idOrder ?>" idTable="<?php echo $idTable ?>" processOrder="<?php echo $processOrder ?>">Orden # <?php echo $transactionOrder ?></h4>
                        <p class="order-date"><?php echo TemplateController::formatDate(4,$dateOrder) ?></p>
                        <?php if ($processOrder == "Ordenando"): ?><span class="status-badge status-pendiente">Pendiente</span><?php endif; ?>
                        <?php if ($processOrder == "Preparando"): ?><span class="status-badge status-preparando">En cocina</span><?php endif; ?>
                        <?php if ($processOrder == "Entregada"): ?><span class="status-badge status-entregada">Servida</span><?php endif; ?>
                    </div>
                    <div class="small text-center mb-3" style="color:var(--color-text-secondary)">Mesero: <?php echo explode("@",$_SESSION["admin"]->email_admin)[0] ?></div>
                    <?php if ($processOrder != "Entregada"): ?>
                        <div class="order-items-list" id="order-items-active">
                            <?php if (!empty($sales)): ?>
                                <?php foreach ($sales as $key => $value): ?>
                                    <?php if ($value->process_sale == "Pendiente"): ?>
                                        <div class="order-item" data-process="Pendiente" data-id="<?php echo $value->id_food_sale ?>" data-name="<?php echo urldecode($value->title_food) ?>" data-qty="<?php echo $value->qty_sale ?>" data-price="<?php echo $value->subtotal_sale ?>">
                                            <div class="order-item-header"><span class="order-item-name"><?php echo urldecode($value->title_food) ?></span><span class="order-item-price"><?php echo fncMoney($value->subtotal_sale) ?></span></div>
                                            <div class="order-item-controls"><button class="quantity-btn decrease-qty"><i class="bi bi-dash"></i></button><span class="quantity-display">x<?php echo $value->qty_sale ?></span><button class="quantity-btn increase-qty"><i class="bi bi-plus"></i></button><button class="quantity-btn ms-2 remove-item" style="background:#dc3545;border-color:#dc3545;color:white"><i class="bi bi-trash"></i></button></div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($value->process_sale == "Preparando"): ?>
                                        <div class="order-item" data-process="Preparando" data-name="<?php echo urldecode($value->title_food) ?>"><div class="order-item-header"><span class="order-item-name"><?php echo urldecode($value->title_food) ?></span><span class="order-item-price"><?php echo fncMoney($value->subtotal_sale) ?></span></div><div class="order-item-controls"><span class="quantity-display">x<?php echo $value->qty_sale ?></span></div></div>
                                    <?php endif; ?>
                                    <?php if ($value->process_sale == "Lista"): ?>
                                        <div class="order-item" data-process="Lista" data-name="<?php echo urldecode($value->title_food) ?>"><div class="order-item-header"><span class="order-item-name"><?php echo urldecode($value->title_food) ?></span><span class="order-item-price"><?php echo fncMoney($value->subtotal_sale) ?></span></div><div class="order-item-controls"><span class="quantity-display">x<?php echo $value->qty_sale ?></span><button class="serve-item px-1" idSale="<?php echo $value->id_sale ?>" style="color:#fff;border:none;cursor:pointer;font-size:var(--font-size-xs)">Servir</button></div></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-order"><i class="bi bi-cart3"></i><p>No hay items añadidos</p></div>
                            <?php endif; ?>
                        </div>
                        <div class="order-notes"><textarea class="form-control" id="note_order" placeholder="Adicionar notas a esta orden..." rows="2"><?php echo $noteOrder ? $noteOrder : null ?></textarea></div>
                        <div class="order-actions">
                            <button class="btn-order btn-order-primary" id="submit-order" idOrder="<?php echo $idOrder ?>"><i class="bi bi-check-circle"></i> Enviar Orden</button>
                            <?php if ($processOrder == "Ordenando"): ?><button class="btn-order btn-order-danger" id="clear-order"><i class="bi bi-trash"></i> Eliminar todos los items</button><?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="order-total">
                        <div class="total-line"><span>Subtotal</span><span>C$ <span id="subtotalValue">0.00</span></span></div>
                        <div class="total-line"><span>IVA (<?php echo $tax*100 ?>%)</span><span>C$ <span id="taxValue">0.00</span></span></div>
                        <div class="total-line total-final"><strong>Total</strong><strong>C$ <span id="totalValue">0.00</span></strong></div>
                    </div>
                    <?php if ($processOrder == "Entregada"): ?>
                        <div class="order-payment mt-3"><a href="/boucher?idOrder=<?php echo $idOrder ?>" target="_blank" class="btn-payment"><i class="bi bi-printer"></i> Imprimir Boucher</a></div>
                    <?php else: ?>
                        <div class="order-payment mt-3"><button class="btn-payment" data-bs-toggle="modal" data-bs-target="#myCheckout"><i class="fa-solid fa-cash-register"></i> Pagar Orden</button></div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
        <div class="pos-fab-container" id="posFabContainer" aria-hidden="true">
            <button class="pos-fab" id="toggleMenuFab" aria-label="Mostrar menú"><svg class="fab-icon-menu" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor"/><path d="M9 9h6M9 15h6M9 21h4" stroke="currentColor" stroke-width="2"/></svg><svg class="fab-icon-close" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2"/><line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2"/></svg></button>
            <button class="pos-fab pos-fab-order" id="toggleOrderFab" aria-label="Ver orden"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" stroke="currentColor"/><line x1="3" y1="6" x2="21" y2="6" stroke="currentColor"/><path d="M16 10a4 4 0 0 1-8 0" stroke="currentColor"/></svg><span class="fab-badge" id="orderItemCount" style="display:none">0</span></button>
        </div>
        <div class="pos-overlay" id="posOverlay" aria-hidden="true"></div>
    </div>
    <div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true"></div>
    <input type="hidden" id="idAdmin" value="<?php echo base64_encode($_SESSION["admin"]->id_admin) ?>">
    <input type="hidden" id="idOffice" value="<?php echo base64_encode($_SESSION["admin"]->id_office_admin) ?>">
    <input type="hidden" id="taxSystem" value="<?php echo $tax ?>">
    <input type="hidden" id="tipSystem" value="<?php echo $tip ?>">
    <input type="hidden" id="tasaUsd" value="<?php echo TASA_USD ?>">
    <!-- Checkout Modal -->
    <?php include "views/modules/modals/checkout.php"; ?>
    <!-- Keyboard Shortcuts Help Modal -->
    <div class="modal fade" id="shortcutsModal" tabindex="-1" aria-labelledby="shortcutsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="shortcutsModalLabel">Atajos de Teclado</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><dl class="row shortcuts-list">
                    <dt class="col-4"><kbd>F1</kbd></dt><dd class="col-8">Ayuda</dd>
                    <dt class="col-4"><kbd>F2</kbd></dt><dd class="col-8">Enviar orden a cocina</dd>
                    <dt class="col-4"><kbd>F3</kbd></dt><dd class="col-8">Abrir checkout / Pagar</dd>
                    <dt class="col-4"><kbd>F4</kbd></dt><dd class="col-8">Limpiar orden</dd>
                    <dt class="col-4"><kbd>Escape</kbd></dt><dd class="col-8">Cerrar modales</dd>
                    <dt class="col-4"><kbd>1-9</kbd></dt><dd class="col-8">Cambiar categoría</dd>
                </dl></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
            </div>
        </div>
    </div>
    <script src="/views/assets/js/pos/pos.js?v=<?php echo time() ?>"></script>
    <script src="/views/assets/js/checkout/checkout.js?v=<?php echo time() ?>"></script>
</body>
</html>