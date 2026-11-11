<?php
require_once "BaseDatos.php";

// Se encarga de guardar y actualizar las ventas en la base.
class VentaRepositorio {
    private $pdo;

    public function __construct() {
        $this->pdo = BaseDatos::conectar();
    }

    // Guarda la venta como pendiente. Devuelve false si ya existía.
    public function guardar($origen, $ventaId, $datos) {
        try {
            $sql = $this->pdo->prepare("INSERT INTO ventas (origen, venta_id, datos) VALUES (?, ?, ?)");
            $sql->execute([$origen, $ventaId, json_encode($datos)]);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return false;   // ya existía: es duplicada
            }
            throw $e;           // otro error: que se vea
        }
    }

    public function marcarProcesada($origen, $ventaId) {
        $sql = $this->pdo->prepare("UPDATE ventas SET estado = 'procesado' WHERE origen = ? AND venta_id = ?");
        $sql->execute([$origen, $ventaId]);
    }

    public function sumarIntento($origen, $ventaId) {
        $sql = $this->pdo->prepare("UPDATE ventas SET intentos = intentos + 1 WHERE origen = ? AND venta_id = ?");
        $sql->execute([$origen, $ventaId]);
    }

    // Si ya se intentó 3 veces, la venta pasa a fallida.
    public function marcarFallidaSiCorresponde($origen, $ventaId) {
        $sql = $this->pdo->prepare("UPDATE ventas SET estado = 'fallido' WHERE origen = ? AND venta_id = ? AND intentos >= 3");
        $sql->execute([$origen, $ventaId]);
    }

    // Devuelve las ventas que todavía hay que mandar a B.
    public function pendientes() {
        $sql = $this->pdo->query("SELECT datos FROM ventas WHERE estado = 'pendiente'");
        return $sql->fetchAll(PDO::FETCH_COLUMN);
        return $sql->rowCount() > 0;        
    }
    // Lista las ventas con su estado. Si se pasa un estado, filtra por ese.
    public function listar($estado) {
        if ($estado === null) {
            $sql = $this->pdo->query("SELECT origen, venta_id, estado, intentos FROM ventas");
        } else {
            $sql = $this->pdo->prepare("SELECT origen, venta_id, estado, intentos FROM ventas WHERE estado = ?");
            $sql->execute([$estado]);
        }
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    // Devuelve el JSON guardado de todas las ventas.
    public function todas() {
        $sql = $this->pdo->query("SELECT datos FROM ventas");
        return $sql->fetchAll(PDO::FETCH_COLUMN);
    }
}