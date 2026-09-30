<?php
require_once "JsonParse.php";
require_once "DTOVentaMovimiento.php";
require_once "VentaService.php";
require_once "ConectorStock.php";
class VentaControlador{
    public $service;
    public $conector;

    public function __construct(VentaService $service, ConectorStock $conector){
        $this->service = $service;
        $this->conector = $conector;
    }
    public function recibirVenta(){
        $ventajs = new JsonParse();
      $data = $ventajs->parsear();
        if ($data===null){
          http_response_code(400);
      echo "Error: el JSON recibido es inválido";
         return;
        }
        if (!isset($data['ventaId']) || !isset($data['fecha']) || !isset($data['productos'])) {
          http_response_code(400);
        echo "Error: faltan datos de la venta (ventaId, fecha o productos)";
    
          return;
        }
        $dto = new DTOVentaMovimiento($data['ventaId'],$data['fecha'],$data['productos']);
        $movimiento=$this->service->procesar($dto);
        $respuesta=$this->conector->enviar($movimiento);
        if($respuesta===false){
          http_response_code(502);
          echo '{"message":"error no se pudo Registrar en el sistema b"}';
      }else {
          http_response_code(200);
          echo '{"message":"Se genero Exitosamente"}';

        }
    }
}