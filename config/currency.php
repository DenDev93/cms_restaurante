<?php

/*=============================================
Configuración de Moneda — Córdobas Nicaragüenses
=============================================*/

define("TASA_USD", 36.75);

define("MONEDA_LOCAL", "C$");

define("MONEDA_USD", "US$");

/*=============================================
Formatear monto en Córdobas
=============================================*/

function fncMoney($monto){

	return MONEDA_LOCAL." ".number_format($monto, 2, ".", ",");

}

/*=============================================
Formatear monto en Dólares
=============================================*/

function fncUsd($monto){

	return MONEDA_USD." ".number_format($monto, 2, ".", ",");

}

/*=============================================
Convertir Córdobas a Dólares
=============================================*/

function fncCordobaToUsd($cordobas){

	return $cordobas / TASA_USD;

}

/*=============================================
Convertir Dólares a Córdobas
=============================================*/

function fncUsdToCordoba($dolares){

	return $dolares * TASA_USD;

}