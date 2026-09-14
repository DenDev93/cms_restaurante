<?php

ob_start();

use Dompdf\Dompdf;
use Dompdf\Options;

// Configuración para imágenes remotas y otras opciones
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

if(isset($_GET["idOrder"])){

	/*=============================================
	Traer comandas
	=============================================*/

	$url = "relations?rel=orders,admins,tables&type=order,admin,table&linkTo=id_order&equalTo=".$_GET["idOrder"];
	$method = "GET";
	$fields = array();

	$getOrders = CurlController::request($url,$method,$fields);

	if($getOrders->status == 200){

	  	$orders = $getOrders->results[0];

	  	/*=============================================
	    Capturar items de ventas
	    =============================================*/

	    $url = "relations?rel=sales,foods&type=sale,food&linkTo=id_order_sale&equalTo=".$_GET["idOrder"];
	    $method = "GET";
	    $fields = array();

	    $getSales = CurlController::request($url,$method,$fields);

	    $orders->sales = $getSales->results;

	}else{

		$orders = null;
		
	}


}

?>

<!-- <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #007BFF; }
        p { font-size: 14px; }
    </style>
</head>
<body>
    <h1>Factura de Compra</h1>
    <p>Gracias por tu compra. Este es tu comprobante en PDF.</p>
</body>
</html> -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
    	@page {margin: 0cm;}
        body { font-family: Arial, sans-serif;  margin: 0.35cm; }
        h1 { color: #007BFF; }
        p { font-size: 12px; }
    </style>
</head>
<body>
	<h2><?php echo urldecode($orders->title_table) ?></h2>
	<h3>Orden # <?php echo $orders->transaction_order ?></h3>
	<p><?php echo TemplateController::formatDate(4,$orders->date_order) ?></p>
	<br>
	<table>
    	<tr>
    		<th style="text-align: left;">Descripción</th>
    		<th style="text-align: right;">Cantidad</th>
    	</tr>

    	<br>

    	<?php foreach ($orders->sales as $key => $value): ?>

			<tr>
	    		<td><?php echo urldecode($value->title_food) ?></td>
	    		<td style="text-align: right;"><?php echo urldecode($value->qty_sale) ?></td>
	    	</tr>
    		
    	<?php endforeach ?>

    </table>

    <br>

    <p>Notas:</p>
    <div style="border:1px solid #333; padding:5px">
    	<p><?php echo $orders->note_order ?></p>
    </div>

    <h4>Mesero: <?php echo explode("@",$orders->email_admin)[0] ?></h4>

    <hr>
</body>

</html>

<?php

$html = ob_get_clean();

$dompdf->loadHtml($html);

$customPaper = array(0, 0, 226.8, 566.9);

$dompdf->setPaper($customPaper, 'portrait');

$dompdf->render();

// Limpia el búfer de salida y establece el tipo de contenido
ob_clean();
header("Content-Type: application/pdf");

// Envía el archivo al navegador sin forzar la descarga
$dompdf->stream("archivo_generado.pdf", ["Attachment" => false]);

?>