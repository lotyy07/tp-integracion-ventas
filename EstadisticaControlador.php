<?php
require_once "EstadisticaService.php";

class EstadisticaControlador {
    public $service;

    public function __construct(EstadisticaService $service) {
        $this->service = $service;
    }

    public function consultar() {
        http_response_code(200);
        echo json_encode($this->service->listar());
    }
}