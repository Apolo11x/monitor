<?php
$host = 'localhost';
$user = 'root';
$pass = '1002958401';
$db = 'servicios_web';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}

$titulo = $_POST['titulo'];
$url = $_POST['url'];
$movible = $_POST['movible'];
$minimizable = $_POST['minimizable'];
$flotante = $_POST['flotante'];
$top = isset($_POST['top']) ? (int)$_POST['top'] : 100;
$left = isset($_POST['left']) ? (int)$_POST['left'] : 80;

$sql = "INSERT INTO ventanas (titulo, url, movible, minimizable, flotante, top, posicion_left)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $titulo, $url, $movible, $minimizable, $flotante, $top, $left);
$stmt->execute();

if ($stmt->affected_rows > 0) {
  echo "ok";
} else {
  echo "error";
}

$stmt->close();
$conn->close();
?>
