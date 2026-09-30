<?php
    class VentaMapper{
        public function transformar($venta){
            $listaProductos = array();
            foreach($venta->productos as $i => $producto){
                $listaProductos[$i] = array('codigoProducto' => $producto['codigo'], 'cantidad' => $producto['cantidad']);
            }
            return array(
                'tipoMovimiento' => 'SALIDA',
                'fecha' => $venta->fecha,
                'referencia' => 'VENTA-' . $venta->ventaId,
                'productos' => $listaProductos
            );
        }
    }
