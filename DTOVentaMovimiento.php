<?php
class DTOVentaMovimiento {
    public $ventaId;
    public $fecha;
    public array $productos;
    public function __construct($ventaId, $fecha, array $productos) {
        $this->ventaId=$ventaId;
        $this->fecha=$fecha;
        $this->productos=$productos;
    }
}
