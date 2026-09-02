<?php

ob_start();

use Dompdf\Dompdf;
use Dompdf\Options;

// Configuración para imágenes remotas y otras opciones
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$order = null;
$sales = array();

if(isset($_GET["idOrder"])){

	/*=============================================
	Traer la orden pagada
	=============================================*/

	$url = "relations?rel=orders,admins,tables&type=order,admin,table&linkTo=id_order&equalTo=".$_GET["idOrder"];
	$method = "GET";
	$fields = array();

	$getOrder = CurlController::request($url,$method,$fields);

	if($getOrder->status == 200){

	  	$order = $getOrder->results[0];

	  	/*=============================================
	    Capturar items de ventas
	    =============================================*/

	    $url = "relations?rel=sales,foods&type=sale,food&linkTo=id_order_sale&equalTo=".$_GET["idOrder"];
	    $method = "GET";
	    $fields = array();

	    $getSales = CurlController::request($url,$method,$fields);

	    if($getSales->status == 200){

	    	$sales = $getSales->results;
	    }

	}

}

if($order != null):

	$totalPagar = $order->total_order + $order->tip_order;
	$methodLabel = "Efectivo";

	if($order->method_order == "card"){

		$methodLabel = "Tarjeta";
	}

	if($order->method_order == "transfer"){

		$methodLabel = "Transferencia";
	}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
    	@page {margin: 0cm;}
        body { font-family: 'DejaVu Sans', Arial, sans-serif; margin: 0.35cm; font-size: 11px; color: #333; }
        .center { text-align: center; }
        .title { font-size: 14px; font-weight: bold; margin: 0 0 2px 0; }
        .small { font-size: 9px; }
        .muted { color: #777; }
        .line { border-bottom: 1px dashed #999; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        .total-row td { font-weight: bold; }
        .bottom-line { border-top: 1px dashed #999; }
    </style>
</head>
<body>

	<div class="center">
		<p class="title"><?php echo urldecode($order->title_admin) ?></p>
		<p class="small muted">BOUCHER DE PAGO</p>
		<p class="small muted"><?php echo TemplateController::formatDate(4,$order->date_order) ?></p>
	</div>

	<div class="line"></div>

	<table>
		<tr>
			<td>Mesa:</td>
			<td class="center"><?php echo urldecode($order->title_table) ?></td>
		</tr>
		<tr>
			<td>Orden:</td>
			<td class="center"># <?php echo $order->transaction_order ?></td>
		</tr>
		<tr>
			<td>Mesero:</td>
			<td class="center"><?php echo explode("@",$order->email_admin)[0] ?></td>
		</tr>
		<tr>
			<td>Método:</td>
			<td class="center"><?php echo $methodLabel ?></td>
		</tr>
	</table>

	<div class="line"></div>

	<table>
		<?php foreach ($sales as $key => $item): ?>
			<tr>
				<td><?php echo urldecode($item->title_food) ?> x<?php echo $item->qty_sale ?></td>
				<td style="text-align: right;"><?php echo fncMoney($item->subtotal_sale) ?></td>
			</tr>
		<?php endforeach ?>
	</table>

	<div class="line"></div>

	<table>
		<tr>
			<td>Subtotal</td>
			<td style="text-align: right;"><?php echo fncMoney($order->subtotal_order) ?></td>
		</tr>
		<tr>
			<td>IVA (15%)</td>
			<td style="text-align: right;"><?php echo fncMoney($order->tax_order) ?></td>
		</tr>
		<tr>
			<td>Propina</td>
			<td style="text-align: right;"><?php echo fncMoney($order->tip_order) ?></td>
		</tr>
		<tr class="total-row">
			<td>TOTAL A PAGAR</td>
			<td style="text-align: right;"><?php echo fncMoney($totalPagar) ?></td>
		</tr>
	</table>

	<div class="line"></div>

	<table>
		<tr>
			<td>Recibido</td>
			<td style="text-align: right;"><?php echo $order->currency_order." ".number_format($order->received_order, 2, ".", ",") ?></td>
		</tr>
		<tr>
			<td>Vuelto</td>
			<td style="text-align: right;"><?php echo $order->currency_order." ".number_format($order->change_order, 2, ".", ",") ?></td>
		</tr>
		<?php if($order->currency_order == "US$"): ?>
			<tr>
				<td class="muted">Equivalente en Córdobas</td>
				<td class="muted" style="text-align: right;"><?php echo fncMoney(fncUsdToCordoba($order->received_order)) ?></td>
			</tr>
		<?php endif ?>
	</table>

	<?php if(!empty($order->note_order)): ?>
		<div class="line"></div>
		<p class="small muted">Notas: <?php echo urldecode($order->note_order) ?></p>
	<?php endif ?>

	<div class="line"></div>

	<p class="center small muted">¡Gracias por su visita!</p>

</body>
</html>

<?php

endif;

$html = ob_get_clean();

$dompdf->loadHtml($html);

$customPaper = array(0, 0, 226.8, 566.9);

$dompdf->setPaper($customPaper, 'portrait');

$dompdf->render();

// Limpia el búfer de salida y establece el tipo de contenido
ob_clean();
header("Content-Type: application/pdf");

// Envía el archivo al navegador sin forzar la descarga
$dompdf->stream("boucher.pdf", ["Attachment" => false]);

?>