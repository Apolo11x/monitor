<?php
try {
  // Conecta a la base de datos
  $db = new PDO('mysql:host=localhost;dbname=servicios_web;charset=utf8', 'root', '1002958401');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Prepara e inserta
  $stmt = $db->prepare("INSERT INTO ventanas (titulo, url, movible, minimizable, flotante, top, left) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([
    $_POST['titulo'], $_POST['url'], $_POST['movible'],
    $_POST['minimizable'], $_POST['flotante'],
    $_POST['top'], $_POST['left']
  ]);

  echo "ok";
} catch (PDOException $e) {
  // Muestra error (útil para depuración)
  echo "Error: " . $e->getMessage();
}
?>
