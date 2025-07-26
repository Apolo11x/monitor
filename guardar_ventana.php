<?php
require_once 'database.php';

try {
    // Recibe datos del formulario
    $titulo = $_POST['titulo'] ?? '';
    $url = $_POST['url'] ?? '';
    $movible = isset($_POST['movible']) && $_POST['movible'] === 'si' ? 1 : 0;
    $minimizable = isset($_POST['minimizable']) && $_POST['minimizable'] === 'si' ? 1 : 0;
    $flotante = isset($_POST['flotante']) && $_POST['flotante'] === 'si' ? 1 : 0;
    $top = isset($_POST['top']) ? intval($_POST['top']) : 0;
    $left = isset($_POST['left']) ? intval($_POST['left']) : 0;

    // Obtiene la conexión usando la clase Database
    $pdo = Database::getInstance();
    
    // Prepara y ejecuta la consulta
    $stmt = $pdo->prepare("INSERT INTO ventanas (titulo, url, movible, minimizable, flotante, top, `left`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$titulo, $url, $movible, $minimizable, $flotante, $top, $left])) {
        echo json_encode(['status' => 'success', 'message' => 'Ventana guardada correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar la ventana']);
    }
} catch (PDOException $e) {
    error_log("Error en guardar_ventana: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error de base de datos']);
}
?>