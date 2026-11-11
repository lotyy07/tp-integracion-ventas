<?php
require_once "TraductorVenta.php";
require_once "DTOVentaMovimiento.php";

class TraductorVentaA implements TraductorVenta {
    public function reconoce($data) {
        return isset($data['ventaId']) && isset($data['fecha']) && isset($data['productos']);
    }

    public function traducir($data) {
        return new DTOVentaMovimiento($data['ventaId'], $data['fecha'], $data['productos'], 'A');
    }
}