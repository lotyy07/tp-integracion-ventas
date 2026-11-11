<?php
require_once "VentaRepositorio.php";
require_once "TransformadorEstadistica.php";

class EstadisticaService {
    public $repositorio;
    public $transformador;

    public function __construct(VentaRepositorio $repositorio, TransformadorEstadistica $transformador) {
        $this->repositorio = $repositorio;
        $this->transformador = $transformador;
    }

    public function listar() {
        $resultado = [];
        foreach ($this->repositorio->todas() as $json) {
            $venta = json_decode($json, true);
            $resultado[] = $this->transformador->transformar($venta);
        }
        return $resultado;
    }
}