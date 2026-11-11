<?php
require_once "VentaMapper.php";
require_once "ConectorStock.php";
require_once "VentaRepositorio.php";

class VentaService{
    public $mapper;
    public $conector;
    public $repositorio;

    public function __construct(VentaMapper $mapper, ConectorStock $conector, VentaRepositorio $repositorio){
        $this->mapper = $mapper;
        $this->conector = $conector;
        $this->repositorio = $repositorio;
    }

    // Venta nueva: se guarda y se manda a B.
    public function procesar($venta){
        $esNueva = $this->repositorio->guardar($venta->origen, $venta->ventaId, $venta);
        if (!$esNueva) {
            return 'duplicada';
        }
        return $this->enviarAB($venta);
    }

    // Venta que ya estaba guardada como pendiente: solo se vuelve a mandar.
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
}