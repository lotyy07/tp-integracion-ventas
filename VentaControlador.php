<?php
require_once "JsonParse.php";
require_once "VentaService.php";

class VentaControlador{
    public $service;
    public $traductores;

    public function __construct(VentaService $service, array $traductores){
        $this->service = $service;
        $this->traductores = $traductores;
    }

    public function recibirVenta(){
        $ventajs = new JsonParse();
        $data = $ventajs->parsear();
        if ($data === null){
            http_response_code(400);
            echo '{"message":"Error: el JSON recibido es inválido"}';
            return;
        }

        // Buscar el traductor que reconoce este formato (A, A2...).
        $dto = null;
        foreach ($this->traductores as $traductor) {
            if ($traductor->reconoce($data)) {
                $dto = $traductor->traducir($data);
                break;
            }
        }
        if ($dto === null) {
            http_response_code(400);
            echo '{"message":"Error: el formato de la venta no es de ningún sistema conocido"}';
            return;
        }

        $estado = $this->service->procesar($dto);

        http_response_code(200);
        if ($estado === 'procesada') {
            echo '{"message":"Venta registrada en el sistema B"}';
        } elseif ($estado === 'duplicada') {
            echo '{"message":"La venta ya había sido recibida, no se vuelve a registrar"}';
        } else {
            echo '{"message":"Venta recibida, quedó pendiente de registrar en el sistema B"}';
        }
    }

    public function consultarVentas(){
        $estado = isset($_GET['estado']) ? $_GET['estado'] : null;
        $ventas = $this->service->listar($estado);
        http_response_code(200);
        echo json_encode($ventas);
    }
}