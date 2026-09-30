<?php
require_once "VentaMapper.php"; 
class VentaService{
    public $mapper;

    public function __Construct(VentaMapper $mapper){
          $this->mapper=$mapper; 
    }

    public function procesar($venta){
      $movimiento = $this->mapper->transformar($venta);
      return $movimiento;


    }

}
