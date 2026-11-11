<?php
// Conexión a MySQL. Si la base o la tabla no existen, las crea.

class BaseDatos {
    public static function conectar() {
        $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS integracion_ventas");
        $pdo->exec("USE integracion_ventas");

        $pdo->exec("CREATE TABLE IF NOT EXISTS ventas (
            origen    VARCHAR(10) NOT NULL,
            venta_id  INT         NOT NULL,
            datos     TEXT        NOT NULL,
            estado    VARCHAR(20) NOT NULL DEFAULT 'pendiente',
            intentos  INT         NOT NULL DEFAULT 0,
            PRIMARY KEY (origen, venta_id)
        )");

        return $pdo;
    }
}