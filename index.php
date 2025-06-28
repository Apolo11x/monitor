<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Servicios Web Integrados</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');

    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Roboto', sans-serif;
      overflow: hidden;
      background-color: #f4f4f4;
    }

    header {
      background-color: #01406c;
      color: white;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      gap: 15px;
      z-index: 100;
      position: relative;
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

    .window {
      position: absolute;
      top: 100px;
      left: 80px;
      width: 500px;
      height: 350px;
      display: flex;
      flex-direction: column;
      border: 1px solid #01406c;
      background: white;
      z-index: 999;
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
      opacity: 1;
      transform: scale(1);
      transition: all 0.3s ease;
      border-radius: 10px;
      overflow: hidden;
    }

    .window.minimized {
      opacity: 0;
      transform: scale(0.95);
      pointer-events: none;
      display: none; /* Oculta completamente */
    }

    .title-bar {
      background: #01406c;
      color: white;
      padding: 8px 12px;
      cursor: move;
      display: flex;
      justify-content: space-between;
      align-items: center;
      user-select: none;
      font-size: 0.95em;
    }

    .title-bar div {
      display: flex;
      gap: 5px;
    }

    .title-bar button {
      background: white;
      color: #01406c;
      border: none;
      font-weight: bold;
      border-radius: 4px;
      padding: 2px 8px;
      font-size: 14px;
      cursor: pointer;
      transition: background 0.2s;
    }

    .title-bar button:hover {
      background: #ccc;
    }

    iframe {
      width: 100%;
      height: 100%;
      border: none;
      flex: 1;
    }

    #tab-btn, #new-window-btn {
     position: fixed;
      z-index: 1000;
      padding: 10px 16px;
      border-radius: 8px;
      background: #01406c;
      color: white;
      border: none;
      font-weight: bold;
      box-shadow: 0 3px 8px rgba(0,0,0,0.3);
      cursor: pointer;
      font-size: 14px;
    }

    #tab-btn {
      bottom: 20px;
      left: 20px;
      display: none;
      transition: opacity 0.3s ease;
    }

    #new-window-btn {
      bottom: 20px;
      right: 20px;
    }

    #modal {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.4);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 2000;
    }

    #modal > div {
      background: white;
      padding: 20px;
      border-radius: 10px;
      width: 320px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.3);
    }

    #modal h3 {
      margin-top: 0;
      color: #01406c;
    }

    #modal label {
      display: block;
      margin-bottom: 10px;
    }

    #modal input, #modal select {
      width: 100%;
      padding: 5px;
    }

    #modal button {
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    #modal button:last-child {
      background: #01406c;
      color: white;
    }

    #minimized-windows-container {
      position: fixed;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 10px;
      z-index: 3000;
    }

    .restore-btn {
  padding: 8px 12px;
  background: #01406c;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin-right: 5px;
}

.restore-btn:hover {
  background: #012d4d;
}
  </style>
</head>
<body>
  <header>
    <img src="https://sedcauca.gov.co/wp-content/uploads/2024/10/Logo-WEB-SECRETARI.jpg" alt="Gobernación del Cauca" />
    <h1>Monitor de Servicios Web Integrados</h1>
  </header>

  <iframe id="glpi-background" src="http://172.19.0.214/glpi" title="GLPI"></iframe>


  <button id="new-window-btn">Crear nueva ventana</button>
  <button id="view-saved-btn" style="

"></button>
  <div id="modal">
    <div id="ventanas-listado" style="
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.4);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 2000;
">
<div id="dock" style="
  position: fixed;
  right: 0;
  top: 80px;
  width: 160px;
  max-height: 80%;
  overflow-y: auto;
  background: #eeeeee;
  border-left: 2px solid #ccc;
  z-index: 1500;
  padding: 10px;
  display: flex;
  flex-direction: column;
  gap: 10px;
">
</div>

  <div style="
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 400px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 6px 15px rgba(0,0,0,0.3);
  ">
  
    <h3 style="color:#01406c;">Ventanas guardadas</h3>
    <div id="listado-ventanas"></div>
    <div style="text-align:right; margin-top: 15px;">
      <button onclick="document.getElementById('ventanas-listado').style.display='none'">Cerrar</button>
    </div>
  </div>
</div>

    <div>
      <h3>Crear nueva ventana</h3>
      <label>Título:<br><input id="modal-title" type="text"></label>
      <label>Dirección IP o Ruta:<br><input id="modal-url" type="text" placeholder="172.19.0.214:puerto/opcional"></label>
      <label>¿Movible?<select id="modal-movible"><option value="si">Sí</option><option value="no">No</option></select></label>
      <label>¿Minimizable?<select id="modal-min"><option value="si">Sí</option><option value="no">No</option></select></label>
      <label>¿Flotante?<select id="modal-float"><option value="si">Sí</option><option value="no">No</option></select></label>
      <div style="text-align:right;">
        <button onclick="modal.style.display='none'">Cancelar</button>
        <button id="modal-create-btn">Crear</button>
      </div>
    </div>
  </div>

  <div id="minimized-windows-container"></div>

  <script>
    const modal = document.getElementById('modal');
    const newWindowBtn = document.getElementById('new-window-btn');
    const createBtn = document.getElementById('modal-create-btn');
    const tabBtn = document.getElementById('tab-btn');

    newWindowBtn.onclick = () => modal.style.display = 'flex';

    createBtn.onclick = () => {
      const title = document.getElementById('modal-title').value || 'Nueva Ventana';
      const url = document.getElementById('modal-url').value || 'about:blank';
      const movible = document.getElementById('modal-movible').value;
      const minimizable = document.getElementById('modal-min').value;
      const flotante = document.getElementById('modal-float').value;

      document.getElementById('view-saved-btn').onclick = () => {
  fetch('leer_ventanas.php')
    .then(res => res.json())
    .then(data => {
      const listado = document.getElementById('listado-ventanas');
      listado.innerHTML = ''; // Limpiar antes
      data.forEach(v => {
        const div = document.createElement('div');
        div.style.marginBottom = '8px';
        div.innerHTML = `<strong>${v.titulo}</strong> - <a href="http://${v.url}" target="_blank">${v.url}</a>`;
        listado.appendChild(div);
      });
      document.getElementById('ventanas-listado').style.display = 'flex';
    })
    .catch(err => {
      console.error('Error cargando ventanas:', err);
    });
};

      // Guardar en base de datos vía fetch
fetch('guardar_ventana.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded',
  },
  body: new URLSearchParams({
    titulo: title,
    url: url,
    movible: movible,
    minimizable: minimizable,
    flotante: flotante,
    top: 100,
    left: 80
  })
})
.then(res => res.text())
.then(res => {
  if (res.trim() === "ok") {
    console.log("Ventana guardada en la base de datos");
  } else {
    console.error("Error al guardar ventana");
  }
});
modal.style.display = 'none';

      const win = document.createElement('div');
      win.className = 'window';
      win.style.top = '100px';
      win.style.left = '80px';
      win.innerHTML = `
        <div class="title-bar">
          <span>${title}</span>
          <div>
            ${minimizable === 'si' ? '<button class="min-btn">–</button>' : ''}
            <button class="close-btn">×</button>
          </div>
        </div>
        <iframe src="http://${url}"></iframe>
      `;
      document.body.appendChild(win);

    
      // Movimiento
      const titleBar = win.querySelector('.title-bar');
      if (movible === 'si') {
        let drag = false, offX = 0, offY = 0;
        titleBar.addEventListener('mousedown', e => {
          drag = true;
          offX = e.clientX - win.offsetLeft;
          offY = e.clientY - win.offsetTop;
        });
        document.addEventListener('mouseup', () => drag = false);
        document.addEventListener('mousemove', e => {
          if (drag) {
            win.style.left = `${e.clientX - offX}px`;
            win.style.top = `${e.clientY - offY}px`;
          }
        });
      }

      // Minimizar
      const minBtn = win.querySelector('.min-btn');
      if (minBtn) {
        minBtn.onclick = () => {
          win.classList.add('minimized');

          // Crear botón de restauración
          const restoreBtn = document.createElement('button');
          restoreBtn.textContent = title;
          restoreBtn.className = 'restore-btn';
          restoreBtn.onclick = () => {
            win.classList.remove('minimized');
            restoreBtn.remove();
          };

          document.getElementById('minimized-windows-container').appendChild(restoreBtn);
        };
      }

      // Cerrar (elimina la ventana completamente)
      const closeBtn = win.querySelector('.close-btn');
      closeBtn.onclick = () => {
        win.remove();
        // Opcional: Eliminar también su botón de minimizado si existe
        const restoreBtns = document.querySelectorAll('.restore-btn');
        restoreBtns.forEach(btn => {
          if (btn.textContent === title) btn.remove();
        });
      };

      // Flotante
      win.style.position = flotante === 'no' ? 'static' : 'absolute';
      if (flotante === 'no') win.style.margin = '10px auto';

      // Mostrar ventana minimizada
      tabBtn.onclick = () => {
        win.classList.remove('minimized');
        win.style.display = 'flex';
        tabBtn.style.opacity = '0';
        setTimeout(() => tabBtn.style.display = 'none', 300);
      };

      modal.style.display = 'none';
    };
  </script>
</body>
</html>