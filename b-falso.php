<?php
// Simula el sistema B (ERP de stock) para poder probar la integración.
// Recibe el movimiento, lo anota en movimientos-b.txt y responde OK.

$json = file_get_contents('php://input');
file_put_contents('movimientos-b.txt', $json . "\n", FILE_APPEND);

http_response_code(201);
header('Content-Type: application/json');
echo '{"message":"movimiento registrado"}';