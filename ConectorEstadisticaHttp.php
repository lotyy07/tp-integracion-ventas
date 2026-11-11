<?php
require_once "ConectorEstadistica.php";

class ConectorEstadisticaHttp implements ConectorEstadistica {
    public function enviar($estadistica){
        $json = json_encode($estadistica);
        $ch = curl_init("http://localhost:3001/api/estadisticas");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
        $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($respuesta === false || $codigo < 200 || $codigo >= 300) {
            return false;
        }
        return true;
    }
}