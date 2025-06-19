<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Servicios Web Integrados</title>
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Segoe UI', sans-serif;
      background: #eaeaea;
      overflow: hidden;
    }
    header {
      background: #01406c;
      color: white;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      gap: 15px;
      position: relative;
      z-index: 100;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    header img {
      height: 50px;
    }
    header h1 {
      margin: 0;
      font-size: 1.5em;
    }
    #glpi-background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: none;
      z-index: 0;
    }
    .floating-server {
      position: absolute;
      top: 120px;
      left: 100px;
      width: 650px;
      height: 420px;
      border: 2px solid #01406c;
      background: white;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      z-index: 999;
    }
    .drag-bar {
      background: #01406c;
      color: white;
      padding: 10px;
      cursor: move;
      font-weight: bold;
      user-select: none;
    }
    iframe.server-frame {
      width: 100%;
      height: calc(100% - 40px);
      border: none;
    }
  </style>
</head>
<body>
  <header>
    <img src="logo-gobernacion-cauca.png" alt="Gobernación del Cauca">
    <h1>Servicios Web Integrados</h1>
  </header>

  <iframe id="glpi-background" src="http://172.19.0.214/glpi" title="GLPI"></iframe>

  <div class="floating-server" id="serverWindow">
    <div class="drag-bar" id="dragHandle">Monitor de Contenedores</div>
    <iframe class="server-frame" src="http://172.19.0.214:61208" title="Contenedores"></iframe>
  </div>

  <script>
    const dragHandle = document.getElementById("dragHandle");
    const serverWindow = document.getElementById("serverWindow");

    let isDragging = false;
    let offsetX, offsetY;

    dragHandle.addEventListener("mousedown", (e) => {
      isDragging = true;
      offsetX = e.clientX - serverWindow.offsetLeft;
      offsetY = e.clientY - serverWindow.offsetTop;
    });

    document.addEventListener("mouseup", () => {
      isDragging = false;
    });

    document.addEventListener("mousemove", (e) => {
      if (isDragging) {
        serverWindow.style.left = `${e.clientX - offsetX}px`;
        serverWindow.style.top = `${e.clientY - offsetY}px`;
      }
    });
  </script>
</body>
</html>
