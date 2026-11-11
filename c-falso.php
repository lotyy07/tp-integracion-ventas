<?php
// Simula el sistema C (estadísticas) para poder probar.
// Anota lo que recibe en estadisticas-c.txt y responde OK.

$json = file_get_contents('php://input');
file_put_contents('estadisticas-c.txt', $json . "\n", FILE_APPEND);

http_response_code(201);
header('Content-Type: application/json');
echo '{"message":"estadistica registrada"}';