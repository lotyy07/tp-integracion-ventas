<?php
require_once "ConectorStock.php";
    class ConectorStockHttp implements ConectorStock {
        public function enviar($movimiento){
            $json = json_encode($movimiento);
           $ch = curl_init("http://localhost:3000/api/stock/movimiento");
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS,$json );
           curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
           curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
           $respuesta = curl_exec($ch);

           curl_close($ch);
            if($respuesta === false) {
                return false;
            }

            return $respuesta;

        }
    }