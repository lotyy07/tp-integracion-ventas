<?php
require_once "JsonParse.php";
require_once "DTOVentaMovimiento.php";
require_once "VentaService.php";
require_once "ConectorStock.php";
require_once "VentaControlador.php";
require_once "VentaMapper.php";
require_once "ConectorStockHttp.php";
header("Content-Type: application/json");
$conectorStock = new ConectorStockHttp();

$mapper = new VentaMapper();
$service = new VentaService($mapper);

$controler = new VentaControlador($service,$conectorStock);
$controler->recibirVenta();
