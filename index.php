<?php
require_once "VentaControlador.php";
require_once "VentaService.php";
require_once "VentaMapper.php";
require_once "ConectorStockHttp.php";
require_once "VentaRepositorio.php";
require_once "TraductorVentaA.php";
require_once "TraductorVentaA2.php";
require_once "EstadisticaControlador.php";
header("Content-Type: application/json");

$conectorStock = new ConectorStockHttp();
$mapper = new VentaMapper();
$repositorio = new VentaRepositorio();
$service = new VentaService($mapper, $conectorStock, $repositorio);

$traductores = [new TraductorVentaA(), new TraductorVentaA2()];
$controler = new VentaControlador($service, $traductores);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['estadisticas'])) {
    $estadisticas = new EstadisticaControlador(new EstadisticaService($repositorio, new TransformadorEstadistica()));
    $estadisticas->consultar();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controler->consultarVentas();
} else {
    $controler->recibirVenta();
}