<?php
require_once "TraductorVenta.php";
require_once "DTOVentaMovimiento.php";

class TraductorVentaA2 implements TraductorVenta {
    public function reconoce($data) {
        return isset($data['id']) && isset($data['timestamp']) && isset($data['items']);
    }

    public function traducir($data) {
        $productos = [];
        foreach ($data['items'] as $item) {
            $productos[] = [
                'codigo' => $item['sku'],
                'cantidad' => $item['units'],
                'precioUnitario' => null,   // A2 no manda precio
            ];
        }
        return new DTOVentaMovimiento((int) $data['id'], $data['timestamp'], $productos, 'A2');
    }
}