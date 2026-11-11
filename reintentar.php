<?php
// Vuelve a mandar a B las ventas que quedaron pendientes.
// Se ejecuta en la terminal: php reintentar.php

require_once "DTOVentaMovimiento.php";
require_once "VentaService.php";
require_once "VentaMapper.php";
require_once "ConectorStockHttp.php";
require_once "VentaRepositorio.php";

$conectorStock = new ConectorStockHttp();
$mapper = new VentaMapper();
$repositorio = new VentaRepositorio();
$service = new VentaService($mapper, $conectorStock, $repositorio);

$pendientes = $repositorio->pendientes();

if (count($pendientes) === 0) {
    echo "No hay ventas pendientes.\n";
}

foreach ($pendientes as $json) {
    $datos = json_decode($json, true);
    $venta = new DTOVentaMovimiento($datos['ventaId'], $datos['fecha'], $datos['productos'], $datos['origen']);

    $resultado = $service->reintentar($venta);
    echo "Venta " . $datos['origen'] . "-" . $datos['ventaId'] . ": " . $resultado . "\n";
}