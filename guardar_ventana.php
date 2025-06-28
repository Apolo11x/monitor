<?php
require 'config.php'; 

$pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
// Recibe datos del formulario
$titulo = $_POST['titulo'];
$url = $_POST['url'];
$movible = $_POST['movible'] === 'si' ? 1 : 0;
$minimizable = $_POST['minimizable'] === 'si' ? 1 : 0;
$flotante = $_POST['flotante'] === 'si' ? 1 : 0;
$top = intval($_POST['top']);
$left = intval($_POST['left']);

$stmt = $pdo->prepare("INSERT INTO ventanas (titulo, url, movible, minimizable, flotante, top, `left`) VALUES (?, ?, ?, ?, ?, ?, ?)");
if ($stmt->execute([$titulo, $url, $movible, $minimizable, $flotante, $top, $left])) {
  echo "ok";
} else {
  echo "error";
}
?>
