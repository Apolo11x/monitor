<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "1002958401", "servicios_web");

// Verificar conexión
if ($conexion->connect_error) {
  echo json_encode(["error" => "Conexión fallida: " . $conexion->connect_error]);
  exit;
}

// Consulta SQL (asegúrate de que las columnas existen)
$sql = "SELECT id, titulo, url, movible, minimizable, flotante, top_pos, left_pos FROM ventanas";
$resultado = $conexion->query($sql);

// Verificar si la consulta fue exitosa
if (!$resultado) {
  echo json_encode(["error" => "Error en la consulta SQL: " . $conexion->error]);
  exit;
}

// Construir el array de resultados
$datos = [];
while ($fila = $resultado->fetch_assoc()) {
  $datos[] = $fila;
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE);
