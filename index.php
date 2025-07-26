<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios Web Integrados - Gobernación del Cauca</title>
    <link rel="icon" href="https://sedcauca.gov.co/wp-content/uploads/2024/10/Logo-WEB-SECRETARI.jpg" type="image/jpeg">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #0056b3;
            --light-blue: #3a7bd5;
            --dark-blue: #003366;
            --accent-green: #28a745;
            --accent-red: #dc3545;
            --gray-bg: #f8f9fa;
            --gray-light: #e5e7eb;
            --gray-medium: #9ca3af;
            --gray-dark: #374151;
            --button-neutral: #6b7280;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--gray-bg);
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: var(--gray-dark);
        }

        /* Ventanas */
        .window {
            position: absolute;
            box-sizing: border-box;
            min-width: 320px;
            min-height: 200px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid var(--primary-blue);
            background: white;
            z-index: 999;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform, top, left, width, height;
            user-select: none;
            touch-action: none;
        }

        .window.minimized {
            opacity: 0;
            transform: translateY(40px) scale(0.85);
            pointer-events: none;
            display: none;
        }

        /* Barra de título */
        .window-title-bar {
            background: linear-gradient(to right, var(--primary-blue), var(--dark-blue));
            color: white;
            padding: 12px 16px;
            cursor: grab;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            transition: background 0.2s ease;
        }

        .window-title-bar.dragging {
            cursor: grabbing;
            background: var(--dark-blue);
        }

        /* Controles de ventana */
        .window-controls {
            display: flex;
            gap: 8px;
        }

        .window-btn {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 12px;
            color: white;
            transition: all 0.2s ease;
            border: none;
        }

        .minimize-btn {
            background-color: var(--button-neutral);
        }
        .minimize-btn:hover {
            background-color: #4b5563;
        }

        .close-btn {
            background-color: var(--accent-red);
        }
        .close-btn:hover {
            background-color: #c82333;
        }

        /* Contenido de la ventana */
        .window-content {
            flex: 1;
            width: 100%;
            height: calc(100% - 52px);
            overflow: hidden;
            background-color: white;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .window-content iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        /* Redimensionador */
        .window-resizer {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 24px;
            height: 24px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="%236b7280" viewBox="0 0 24 24"><path d="M20 20h-4v-4h1.5v2.5H20V20z"/></svg>');
            background-repeat: no-repeat;
            background-position: bottom right;
            cursor: nwse-resize;
            z-index: 100;
            opacity: 0.5;
            transition: opacity 0.2s ease;
        }

        .window-resizer:hover {
            opacity: 1;
        }

        /* Botones de restauración */
        .restore-btn {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95em;
            white-space: nowrap;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            backdrop-filter: blur(4px);
        }
        .restore-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        /* Modal */
        #modal > div {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 12px;
            width: 95%;
            max-width: 550px;
            border: 1px solid var(--gray-light);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            padding: 2rem;
            animation: fadeInScale 0.3s ease-out forwards;
        }
        #modal.hidden > div {
            animation: fadeOutScale 0.3s ease-in forwards;
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes fadeOutScale {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.9); }
        }

        #modal h3 {
            color: var(--dark-blue);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-light);
        }

        #modal label {
            display: block;
            margin-bottom: 1.5rem;
            color: var(--gray-dark);
            font-weight: 500;
            font-size: 1.1rem;
        }

        #modal input, #modal select {
            width: 100%;
            padding: 1rem;
            border: 1px solid var(--gray-light);
            border-radius: 12px;
            font-size: 1rem;
            margin-top: 0.5rem;
            transition: all 0.2s ease;
        }
        #modal input:focus, #modal select:focus {
            outline: none;
            border-color: var(--light-blue);
            box-shadow: 0 0 0 3px rgba(58, 123, 213, 0.25);
        }

        #modal button {
            padding: 0.875rem 1.75rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        #modal button:first-of-type {
            background-color: var(--gray-light);
            color: var(--gray-dark);
        }
        #modal button:first-of-type:hover {
            background-color: var(--gray-medium);
        }
        #modal button:last-child {
            background-color: var(--primary-blue);
            color: white;
        }
        #modal button:last-child:hover {
            background-color: var(--dark-blue);
        }

        /* Botones principales */
        #new-window-btn, #impresoras-btn {
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            border: none;
            cursor: pointer;
        }
        #new-window-btn {
            background-color: var(--primary-blue);
            color: white;
        }
        #new-window-btn:hover {
            background-color: var(--light-blue);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        #impresoras-btn {
            background-color: var(--accent-green);
            color: white;
        }
        #impresoras-btn:hover {
            background-color: #218838;
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* Contenedor de ventanas minimizadas */
        #minimized-windows-container {
            position: fixed;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.75rem;
            padding: 0.75rem;
            background: rgba(30, 64, 175, 0.9);
            border-radius: 12px;
            backdrop-filter: blur(8px);
            z-index: 40;
            max-width: 90vw;
            flex-wrap: wrap;
            justify-content: center;
        }

        /* Header */
        header {
            background-color: white;
            color: var(--gray-dark);
            padding: 1rem;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 20;
            border-bottom: 1px solid var(--gray-light);
        }
        header img {
            height: 3.5rem;
            width: auto;
            margin-right: 1rem;
        }
        header h1 {
            font-size: 1.75rem;
            font-weight: 700;
        }

        /* Fondo GLPI */
        #glpi-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            z-index: 0;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-gray-100">
    <header>
        <img src="https://sedcauca.gov.co/wp-content/uploads/2024/10/Logo-WEB-SECRETARI.jpg" alt="Logo Gobernación del Cauca">
        <h1>Servicios Web Integrados</h1>
    </header>

    <iframe id="glpi-background" src="http://172.19.0.214/glpi" title="Fondo GLPI" loading="lazy" sandbox="allow-scripts allow-same-origin"></iframe>

    <div class="fixed bottom-10 left-10 z-40 flex space-x-6">
        <button id="new-window-btn" aria-label="Abrir formulario para crear nueva ventana">
            <i class="fas fa-plus-circle mr-3"></i>Nueva Ventana
        </button>
        <button id="impresoras-btn" aria-label="Abrir Impresoras Educauca en una nueva pestaña">
            <i class="fas fa-print mr-3"></i>Impresoras Educauca
        </button>
    </div>

    <div id="modal" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50 hidden">
        <div>
            <h3>Crear Nueva Ventana</h3>
            <label class="block mb-6">Título de la Ventana:
                <input id="modal-title" type="text" placeholder="Ej: Mi Aplicación" class="mt-2" aria-label="Título de la ventana" />
            </label>
            <label class="block mb-6">Dirección URL o IP:
                <input id="modal-url" type="text" placeholder="Ej: https://ejemplo.com o 192.168.1.1" class="mt-2" aria-label="Dirección URL o IP de la ventana" />
            </label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <label>Movible:
                    <select id="modal-movible" class="mt-2" aria-label="¿Ventana movible?">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </label>
                <label>Minimizable:
                    <select id="modal-min" class="mt-2" aria-label="¿Ventana minimizable?">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </label>
                <label>Redimensionable:
                    <select id="modal-resizable" class="mt-2" aria-label="¿Ventana redimensionable?">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </label>
            </div>
            <div class="text-right mt-8 space-x-4">
                <button onclick="modal.classList.add('hidden')" aria-label="Cancelar creación de ventana">Cancelar</button>
                <button id="modal-create-btn" aria-label="Crear nueva ventana">Crear</button>
            </div>
        </div>
    </div>

    <div id="minimized-windows-container"></div>

    <script>
        // Objeto para manejar el estado global de las ventanas
        const windowState = {
            activeWindow: null,
            isDragging: false,
            isResizing: false,
            startX: 0,
            startY: 0,
            startWidth: 0,
            startHeight: 0,
            startLeft: 0,
            startTop: 0,
            animationFrameId: null,
        };

        let zIndexCounter = 1000;
        const modal = document.getElementById('modal');
        const newWindowBtn = document.getElementById('new-window-btn');
        const impresorasBtn = document.getElementById('impresoras-btn');
        const createBtn = document.getElementById('modal-create-btn');
        const minimizedWindowsContainer = document.getElementById('minimized-windows-container');

        // Funciones de actualización de posición y tamaño
        const updateWindowPosition = (event) => {
            if (!windowState.isDragging || !windowState.activeWindow) return;

            const clientX = event.touches?.[0]?.clientX ?? event.clientX;
            const clientY = event.touches?.[0]?.clientY ?? event.clientY;

            const dx = clientX - windowState.startX;
            const dy = clientY - windowState.startY;

            const newX = Math.max(0, Math.min(
                windowState.startLeft + dx, 
                window.innerWidth - windowState.activeWindow.offsetWidth
            ));
            const newY = Math.max(0, Math.min(
                windowState.startTop + dy, 
                window.innerHeight - windowState.activeWindow.offsetHeight
            ));

            windowState.activeWindow.style.left = `${newX}px`;
            windowState.activeWindow.style.top = `${newY}px`;
        };

        const updateWindowSize = (event) => {
            if (!windowState.isResizing || !windowState.activeWindow) return;

            const clientX = event.touches?.[0]?.clientX ?? event.clientX;
            const clientY = event.touches?.[0]?.clientY ?? event.clientY;

            const newWidth = Math.max(320, windowState.startWidth + (clientX - windowState.startX));
            const newHeight = Math.max(200, windowState.startHeight + (clientY - windowState.startY));

            windowState.activeWindow.style.width = `${newWidth}px`;
            windowState.activeWindow.style.height = `${newHeight}px`;
        };

        const animateWindow = (event) => {
            if (windowState.isDragging) updateWindowPosition(event);
            else if (windowState.isResizing) updateWindowSize(event);
            windowState.animationFrameId = null;
        };

        // Manejo de interacciones
        const startInteraction = (e, win, type) => {
            if (type === 'drag' && e.target.closest('.window-btn')) return;

            windowState.activeWindow = win;
            windowState.activeWindow.style.zIndex = ++zIndexCounter;
            windowState.activeWindow.style.transition = 'none';
            windowState.activeWindow.style.willChange = 'transform, left, top, width, height';

            const clientX = e.touches?.[0]?.clientX ?? e.clientX;
            const clientY = e.touches?.[0]?.clientY ?? e.clientY;

            if (type === 'drag') {
                windowState.isDragging = true;
                windowState.startX = clientX;
                windowState.startY = clientY;
                windowState.startLeft = win.offsetLeft;
                windowState.startTop = win.offsetTop;
                win.classList.add('dragging');
            } else if (type === 'resize') {
                windowState.isResizing = true;
                windowState.startX = clientX;
                windowState.startY = clientY;
                windowState.startWidth = win.offsetWidth;
                windowState.startHeight = win.offsetHeight;
                document.body.style.cursor = 'nwse-resize';
                document.body.style.userSelect = 'none';
            }

            e.preventDefault();
        };

        const endInteraction = () => {
            if (windowState.activeWindow) {
                windowState.activeWindow.classList.remove('dragging');
                windowState.activeWindow.style.transition = '';
                windowState.activeWindow.style.willChange = 'auto';
            }
            windowState.isDragging = false;
            windowState.isResizing = false;
            windowState.activeWindow = null;
            document.body.style.cursor = '';
            document.body.style.userSelect = '';

            if (windowState.animationFrameId) {
                cancelAnimationFrame(windowState.animationFrameId);
                windowState.animationFrameId = null;
            }
        };

        // Event listeners
        document.addEventListener('mousemove', (e) => {
            if ((windowState.isDragging || windowState.isResizing) && !windowState.animationFrameId) {
                windowState.animationFrameId = requestAnimationFrame(() => animateWindow(e));
            }
        });

        document.addEventListener('touchmove', (e) => {
            if ((windowState.isDragging || windowState.isResizing) && !windowState.animationFrameId) {
                windowState.animationFrameId = requestAnimationFrame(() => animateWindow(e));
            }
        }, { passive: false });

        document.addEventListener('mouseup', endInteraction);
        document.addEventListener('touchend', endInteraction);
        document.addEventListener('touchcancel', endInteraction);

        // Validación de IP local
        const isLocalIpAddress = (str) => {
            const ipRegex = /^(10(\.\d{1,3}){3}|172\.(1[6-9]|2\d|3[0-1])(\.\d{1,3}){2}|192\.168(\.\d{1,3}){2})$/;
            return ipRegex.test(str);
        };

        // Creación de ventanas
        const createDraggableResizableWindow = (title, url, movible, minimizable, redimensionable) => {
            const win = document.createElement('div');
            win.className = 'window';
            win.id = `window-${Date.now()}`;
            win.style.zIndex = ++zIndexCounter;
            win.style.left = `${Math.random() * (window.innerWidth - 550) + 50}px`;
            win.style.top = `${Math.random() * (window.innerHeight - 400) + 50}px`;

            let iframeSrc = '';
            let iframeContent = '';

            if (url.startsWith('http://') || url.startsWith('https://')) {
                iframeSrc = url;
            } else if (isLocalIpAddress(url) || url.includes('.') || url.includes(':')) {
                iframeSrc = `http://${url}`;
            } else {
                iframeContent = `
                    <div style="background-color: #f8f9fa; height: 100%; width: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 1.5rem; color: #374151;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #dc3545; margin-bottom: 1.25rem;"></i>
                        <p style="font-size: 1.25rem; font-weight: 600; text-align: center;">Dirección no válida o vacía.</p>
                        <p style="text-align: center; margin-top: 0.75rem;">Por favor, ingresa una URL completa (ej. https://ejemplo.com) o una dirección IP/dominio local (ej. 192.168.1.1, mi-servidor.local:8080).</p>
                    </div>
                `;
            }

            win.innerHTML = `
                <div class="window-title-bar" aria-label="Barra de título de la ventana">
                    <span class="font-medium">${title}</span>
                    <div class="window-controls">
                        ${minimizable === 'si' ? '<button class="window-btn minimize-btn" title="Minimizar" aria-label="Minimizar ventana"><i class="fas fa-minus"></i></button>' : ''}
                        <button class="window-btn close-btn" title="Cerrar" aria-label="Cerrar ventana"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <div class="window-content">
                    ${iframeSrc ? `<iframe src="${iframeSrc}" title="${title}" loading="lazy" sandbox="allow-scripts allow-same-origin allow-popups allow-forms allow-modals"></iframe>` : iframeContent}
                </div>
                ${redimensionable === 'si' ? '<div class="window-resizer" aria-label="Redimensionar ventana"></div>' : ''}
            `;
            document.body.appendChild(win);

            // Event listeners para la nueva ventana
            const titleBar = win.querySelector('.window-title-bar');
            if (movible === 'si') {
                titleBar.addEventListener('mousedown', (e) => startInteraction(e, win, 'drag'));
                titleBar.addEventListener('touchstart', (e) => startInteraction(e, win, 'drag'), { passive: false });
            }

            const resizer = win.querySelector('.window-resizer');
            if (resizer && redimensionable === 'si') {
                resizer.addEventListener('mousedown', (e) => startInteraction(e, win, 'resize'));
                resizer.addEventListener('touchstart', (e) => startInteraction(e, win, 'resize'), { passive: false });
            }

            const minBtn = win.querySelector('.minimize-btn');
            if (minBtn) {
                minBtn.onclick = () => {
                    win.classList.add('minimized');
                    const restoreBtn = document.createElement('button');
                    restoreBtn.textContent = title;
                    restoreBtn.className = 'restore-btn';
                    restoreBtn.dataset.windowId = win.id;
                    restoreBtn.setAttribute('aria-label', `Restaurar ventana ${title}`);
                    minimizedWindowsContainer.appendChild(restoreBtn);
                    restoreBtn.onclick = () => {
                        win.classList.remove('minimized');
                        win.style.zIndex = ++zIndexCounter;
                        restoreBtn.remove();
                    };
                };
            }

            const closeBtn = win.querySelector('.close-btn');
            closeBtn.onclick = () => {
                win.remove();
                const associatedRestoreBtn = minimizedWindowsContainer.querySelector(`[data-window-id="${win.id}"]`);
                if (associatedRestoreBtn) associatedRestoreBtn.remove();
            };

            win.addEventListener('mousedown', () => {
                win.style.zIndex = ++zIndexCounter;
            });

            return win;
        };

        // Eventos de UI
        newWindowBtn.onclick = () => {
            modal.classList.remove('hidden');
            document.getElementById('modal-title').value = '';
            document.getElementById('modal-url').value = '';
            document.getElementById('modal-movible').value = 'si';
            document.getElementById('modal-min').value = 'si';
            document.getElementById('modal-resizable').value = 'si';
        };

        impresorasBtn.onclick = () => {
            window.open('https://impresoraseducauca.web.app/', '_blank');
        };

        createBtn.onclick = () => {
            const title = document.getElementById('modal-title').value.trim() || 'Nueva Ventana';
            const urlInput = document.getElementById('modal-url').value.trim();
            const movible = document.getElementById('modal-movible').value;
            const minimizable = document.getElementById('modal-min').value;
            const redimensionable = document.getElementById('modal-resizable').value;

            if (!urlInput || (!urlInput.startsWith('http://') && !urlInput.startsWith('https://') && !isLocalIpAddress(urlInput) && !urlInput.includes('.') && !urlInput.includes(':'))) {
                alert('Por favor, ingresa una dirección web completa (ej. https://ejemplo.com) o una dirección IP/dominio local (ej. 192.168.1.1, mi-servidor.local:8080).');
                return;
            }

            createDraggableResizableWindow(title, urlInput, movible, minimizable, redimensionable);
            modal.classList.add('hidden');
        };
    </script>
</body>
</html>