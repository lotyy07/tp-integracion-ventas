<?php
class DTOVentaMovimiento {
    public $ventaId;
    public $fecha;
    public array $productos;
    public $origen;

    public function __construct($ventaId, $fecha, array $productos, $origen) {
        $this->ventaId = $ventaId;
        $this->fecha = $fecha;
        $this->productos = $productos;
        $this->origen = $origen;
    }
}