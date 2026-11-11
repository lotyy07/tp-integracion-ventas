<?php
require_once "VentaMapper.php";
require_once "ConectorStock.php";
require_once "VentaRepositorio.php";
require_once "TransformadorEstadistica.php";
require_once "ConectorEstadistica.php";

class VentaService{
    public $mapper;
    public $conector;
    public $repositorio;
    public $transformadorEstadistica;
    public $conectorEstadistica;

    public function __construct(VentaMapper $mapper, ConectorStock $conector, VentaRepositorio $repositorio, TransformadorEstadistica $transformadorEstadistica, ConectorEstadistica $conectorEstadistica){
        $this->mapper = $mapper;
        $this->conector = $conector;
        $this->repositorio = $repositorio;
        $this->transformadorEstadistica = $transformadorEstadistica;
        $this->conectorEstadistica = $conectorEstadistica;
    }

    // Venta nueva: se guarda, se manda a B y después a C.
    public function procesar($venta){
        $esNueva = $this->repositorio->guardar($venta->origen, $venta->ventaId, $venta);
        if (!$esNueva) {
            return 'duplicada';
        }
        $estado = $this->enviarAB($venta);
        $this->enviarAC($venta);
        return $estado;
    }

    // Venta que ya estaba guardada como pendiente: solo se vuelve a mandar a B.
    public function reintentar($venta){
        return $this->enviarAB($venta);
    }

    public function listar($estado){
        return $this->repositorio->listar($estado);
    }

    private function enviarAB($venta){
        $this->repositorio->sumarIntento($venta->origen, $venta->ventaId);

        $movimiento = $this->mapper->transformar($venta);
        if ($this->conector->enviar($movimiento)) {
            $this->repositorio->marcarProcesada($venta->origen, $venta->ventaId);
            return 'procesada';
        }

        if ($this->repositorio->marcarFallidaSiCorresponde($venta->origen, $venta->ventaId)) {
            return 'fallida';
        }
        return 'pendiente';
    }

    // Manda la estadística a C. Si C falla, no afecta en nada a B.
    private function enviarAC($venta){
        try {
            $estadistica = $this->transformadorEstadistica->transformar((array) $venta);
            $this->conectorEstadistica->enviar($estadistica);
        } catch (Throwable $e) {
            // C no está disponible: la venta sigue igual
        }
    }
}