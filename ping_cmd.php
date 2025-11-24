<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['address']) || empty($data['address'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dirección no proporcionada']);
    exit;
}

$address = escapeshellarg($data['address']);

// En Windows, ejecutar cmd.exe con ping -t
// Nota: Esto requiere que el servidor tenga permisos para ejecutar comandos del sistema
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Comando para Windows
    $command = "start cmd.exe /k \"ping -t {$address}\"";
    $result = exec($command, $output, $return_var);
    
    if ($return_var === 0 || $return_var === false) {
        echo json_encode(['success' => true, 'message' => 'CMD abierto con ping constante']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'No se pudo ejecutar el comando']);
    }
} else {
    // Para sistemas Linux/Unix (si es necesario)
    $command = "xterm -e 'ping -c 1000 {$address}' &";
    exec($command, $output, $return_var);
    
    if ($return_var === 0) {
        echo json_encode(['success' => true, 'message' => 'Terminal abierto con ping constante']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'No se pudo ejecutar el comando']);
    }
}
?>

