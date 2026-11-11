<?php
// Convierte una venta al formato que pide el sistema C (estadísticas).
class TransformadorEstadistica {
    public function transformar($venta) {
        $importeTotal = 0;
        foreach ($venta['productos'] as $producto) {
            if ($producto['precioUnitario'] === null) {
                $importeTotal = null;   // A2 no manda precios: no se puede calcular
                break;
            }
            $importeTotal += $producto['cantidad'] * $producto['precioUnitario'];
        }

        return [
            'ventaId' => $venta['ventaId'],
            'cantidadProductos' => count($venta['productos']),
            'importeTotal' => $importeTotal,
        ];
    }
}