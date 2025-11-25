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
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <header>
        <!-- Botón FAB en el header -->
        <div id="fab-container" class="fab-container-header">
            <button id="fab-main-btn" class="fab-main" aria-label="Menú de acciones">
                <i class="fas fa-bars" id="fab-icon"></i>
            </button>
            
            <!-- Menú desplegable de botones -->
            <div id="fab-menu" class="fab-menu">
                <button id="new-window-btn" class="fab-item" aria-label="Abrir formulario para crear nueva ventana" title="Nueva Ventana">
                    <i class="fas fa-plus-circle"></i>
                    <span class="fab-label">Nueva Ventana</span>
                </button>
                <button id="impresoras-btn" class="fab-item" aria-label="Abrir Impresoras Educauca en una nueva pestaña" title="Impresoras Sedcauca">
                    <i class="fas fa-print"></i>
                    <span class="fab-label">Impresoras Sedcauca</span>
                </button>
                <button id="ping-cmd-btn" class="fab-item" aria-label="Abrir CMD con ping constante" title="Ping Red">
                    <i class="fas fa-terminal"></i>
                    <span class="fab-label">Ping Red</span>
                </button>
                <button id="glpi-test-btn" class="fab-item" aria-label="Probar conexión GLPI" title="Probar GLPI" style="display: none;">
                    <i class="fas fa-network-wired"></i>
                    <span class="fab-label">Probar GLPI</span>
                </button>
            </div>
        </div>
        
        <img src="https://sedcauca.gov.co/wp-content/uploads/2024/10/Logo-WEB-SECRETARI.jpg" alt="Logo Gobernación del Cauca">
        <h1>Servicios Web Integrados</h1>
        
        <!-- Contenedor de iconos de ventanas -->
        <div id="windows-tabs-container" class="windows-tabs-container"></div>
    </header>

    <iframe id="glpi-background" src="https://tickets.sedcauca.gov.co" title="Fondo GLPI" loading="lazy" onerror="handleGlpiError()"></iframe>


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
        
        // Control del menú FAB
        const fabMainBtn = document.getElementById('fab-main-btn');
        const fabMenu = document.getElementById('fab-menu');
        const fabIcon = document.getElementById('fab-icon');
        let fabMenuOpen = false;
        
        fabMainBtn.onclick = (e) => {
            e.stopPropagation();
            fabMenuOpen = !fabMenuOpen;
            if (fabMenuOpen) {
                fabMenu.classList.add('open');
                fabIcon.classList.remove('fa-bars');
                fabIcon.classList.add('fa-times');
            } else {
                fabMenu.classList.remove('open');
                fabIcon.classList.remove('fa-times');
                fabIcon.classList.add('fa-bars');
            }
        };
        
        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (fabMenuOpen && !fabMainBtn.contains(e.target) && !fabMenu.contains(e.target)) {
                fabMenuOpen = false;
                fabMenu.classList.remove('open');
                fabIcon.classList.remove('fa-times');
                fabIcon.classList.add('fa-bars');
            }
        });

        // Función para manejar errores de GLPI
        function handleGlpiError() {
            const glpiIframe = document.getElementById('glpi-background');
            const glpiTestBtn = document.getElementById('glpi-test-btn');
            if (glpiIframe) {
                glpiIframe.style.display = 'none';
                document.body.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                if (glpiTestBtn) {
                    glpiTestBtn.style.display = 'inline-flex';
                }
                console.warn('No se pudo cargar GLPI. Verifica la conexión de red o la URL del servidor.');
            }
        }

        // Función para probar conexión GLPI
        function testGlpiConnection() {
            const glpiIframe = document.getElementById('glpi-background');
            const glpiTestBtn = document.getElementById('glpi-test-btn');
            
            if (glpiTestBtn) {
                glpiTestBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i>Probando...';
                glpiTestBtn.disabled = true;
            }

            // Crear un nuevo iframe para probar la conexión
            const testIframe = document.createElement('iframe');
            testIframe.style.display = 'none';
            testIframe.src = 'https://tickets.sedcauca.gov.co';
            
            testIframe.onload = () => {
                if (glpiIframe) {
                    glpiIframe.style.display = 'block';
                    document.body.style.background = '';
                }
                if (glpiTestBtn) {
                    glpiTestBtn.style.display = 'none';
                }
                testIframe.remove();
                console.log('Conexión GLPI restaurada');
            };
            
            testIframe.onerror = () => {
                if (glpiTestBtn) {
                    glpiTestBtn.innerHTML = '<i class="fas fa-network-wired mr-3"></i>Probar GLPI';
                    glpiTestBtn.disabled = false;
                }
                testIframe.remove();
                alert('No se pudo conectar con GLPI. Verifica que el servidor esté funcionando y la URL sea correcta.');
            };
            
            document.body.appendChild(testIframe);
        }

        // Verificar conexión GLPI después de 5 segundos
        setTimeout(() => {
            const glpiIframe = document.getElementById('glpi-background');
            try {
                // Intentar acceder al contenido del iframe para verificar si cargó
                if (glpiIframe && glpiIframe.contentDocument === null) {
                    handleGlpiError();
                }
            } catch (e) {
                // Error de CORS es normal, significa que el iframe está cargando
                console.log('GLPI cargando correctamente (CORS normal)');
            }
        }, 5000);

        // Funciones de actualización optimizadas
        const updateWindowPosition = (clientX, clientY) => {
            const win = windowState.activeWindow;
            const dx = clientX - windowState.startX;
            const dy = clientY - windowState.startY;
            const headerHeight = document.querySelector('header').offsetHeight;

            const newX = Math.max(0, Math.min(
                windowState.startLeft + dx, 
                window.innerWidth - win.offsetWidth
            ));
            const newY = Math.max(headerHeight, Math.min(
                windowState.startTop + dy, 
                window.innerHeight - win.offsetHeight
            ));

            win.style.left = `${newX}px`;
            win.style.top = `${newY}px`;
        };

        const updateWindowSize = (clientX, clientY) => {
            if (!windowState.isResizing || !windowState.activeWindow) return;
            
            const win = windowState.activeWindow;
            const resizeType = windowState.resizeType;
            const headerHeight = document.querySelector('header').offsetHeight;
            const minWidth = 320;
            const minHeight = 200;
            
            // Calcular deltas desde el punto inicial
            const deltaX = clientX - windowState.startX;
            const deltaY = clientY - windowState.startY;
            
            // Obtener posición y tamaño actuales desde el estado inicial
            let newLeft = windowState.startLeft;
            let newTop = windowState.startTop;
            let newWidth = windowState.startWidth;
            let newHeight = windowState.startHeight;

            // Aplicar redimensionamiento según el tipo (similar a ventanas nativas)
            switch (resizeType) {
                case 'nw': // Esquina superior izquierda
                    newWidth = Math.max(minWidth, windowState.startWidth - deltaX);
                    newHeight = Math.max(minHeight, windowState.startHeight - deltaY);
                    newLeft = windowState.startLeft + (windowState.startWidth - newWidth);
                    newTop = windowState.startTop + (windowState.startHeight - newHeight);
                    break;
                case 'ne': // Esquina superior derecha
                    newWidth = Math.max(minWidth, windowState.startWidth + deltaX);
                    newHeight = Math.max(minHeight, windowState.startHeight - deltaY);
                    newTop = windowState.startTop + (windowState.startHeight - newHeight);
                    break;
                case 'sw': // Esquina inferior izquierda
                    newWidth = Math.max(minWidth, windowState.startWidth - deltaX);
                    newHeight = Math.max(minHeight, windowState.startHeight + deltaY);
                    newLeft = windowState.startLeft + (windowState.startWidth - newWidth);
                    break;
                case 'se': // Esquina inferior derecha
                    newWidth = Math.max(minWidth, windowState.startWidth + deltaX);
                    newHeight = Math.max(minHeight, windowState.startHeight + deltaY);
                    break;
                case 'n': // Borde superior
                    newHeight = Math.max(minHeight, windowState.startHeight - deltaY);
                    newTop = windowState.startTop + (windowState.startHeight - newHeight);
                    break;
                case 's': // Borde inferior
                    newHeight = Math.max(minHeight, windowState.startHeight + deltaY);
                    break;
                case 'w': // Borde izquierdo
                    newWidth = Math.max(minWidth, windowState.startWidth - deltaX);
                    newLeft = windowState.startLeft + (windowState.startWidth - newWidth);
                    break;
                case 'e': // Borde derecho
                    newWidth = Math.max(minWidth, windowState.startWidth + deltaX);
                    break;
            }

            // Aplicar límites de pantalla
            const maxLeft = window.innerWidth - minWidth;
            const maxTop = window.innerHeight - minHeight;
            const maxRight = window.innerWidth;
            const maxBottom = window.innerHeight;

            // Limitar posición izquierda
            if (newLeft < 0) {
                newWidth = newWidth + newLeft;
                newLeft = 0;
                if (newWidth < minWidth) {
                    newWidth = minWidth;
                    newLeft = 0;
                }
            }
            
            // Limitar posición superior
            if (newTop < headerHeight) {
                newHeight = newHeight + (newTop - headerHeight);
                newTop = headerHeight;
                if (newHeight < minHeight) {
                    newHeight = minHeight;
                    newTop = headerHeight;
                }
            }
            
            // Limitar ancho (no exceder el borde derecho)
            if (newLeft + newWidth > maxRight) {
                newWidth = maxRight - newLeft;
                if (newWidth < minWidth) {
                    newWidth = minWidth;
                    newLeft = maxRight - minWidth;
                }
            }
            
            // Limitar alto (no exceder el borde inferior)
            if (newTop + newHeight > maxBottom) {
                newHeight = maxBottom - newTop;
                if (newHeight < minHeight) {
                    newHeight = minHeight;
                    newTop = maxBottom - minHeight;
                }
            }

            // Aplicar cambios directamente - sin transiciones para mejor rendimiento
            win.style.width = `${newWidth}px`;
            win.style.height = `${newHeight}px`;
            win.style.left = `${newLeft}px`;
            win.style.top = `${newTop}px`;
        };

        // Manejo de interacciones
        const startInteraction = (e, win, type, resizeType = null) => {
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
                windowState.resizeType = resizeType;
                windowState.startX = clientX;
                windowState.startY = clientY;
                // Capturar valores iniciales usando getBoundingClientRect para precisión
                const rect = win.getBoundingClientRect();
                windowState.startWidth = rect.width;
                windowState.startHeight = rect.height;
                windowState.startLeft = rect.left;
                windowState.startTop = rect.top;
                
                // Agregar clase resizing a la ventana
                win.classList.add('resizing');
                
                // Prevenir selección de texto durante el redimensionamiento
                document.body.style.userSelect = 'none';
                document.body.style.pointerEvents = 'none';
                win.style.pointerEvents = 'auto';
                
                // Agregar clase resizing al redimensionador activo
                const activeResizer = win.querySelector(`.resizer-${resizeType}`);
                if (activeResizer) {
                    activeResizer.classList.add('resizing');
                }
                
                // Establecer cursor apropiado
                const cursorMap = {
                    'nw': 'nw-resize', 'ne': 'ne-resize', 'sw': 'sw-resize', 'se': 'se-resize',
                    'n': 'n-resize', 's': 's-resize', 'w': 'w-resize', 'e': 'e-resize'
                };
                document.body.style.cursor = cursorMap[resizeType] || 'nwse-resize';
                document.body.style.userSelect = 'none';
            }

            e.preventDefault();
        };

        const endInteraction = () => {
            if (windowState.activeWindow) {
                windowState.activeWindow.classList.remove('dragging', 'resizing');
                windowState.activeWindow.style.transition = '';
                windowState.activeWindow.style.willChange = 'auto';
                
                // Remover clase resizing de todos los redimensionadores
                const resizers = windowState.activeWindow.querySelectorAll('.window-resizer');
                resizers.forEach(resizer => {
                    resizer.classList.remove('resizing');
                });
            }
            windowState.isDragging = false;
            windowState.isResizing = false;
            windowState.activeWindow = null;
            document.body.style.cursor = '';
            document.body.style.userSelect = '';
            document.body.style.pointerEvents = '';

            if (windowState.animationFrameId) {
                cancelAnimationFrame(windowState.animationFrameId);
                windowState.animationFrameId = null;
            }
        };

        // Event listeners optimizados para mejor rendimiento - redimensionamiento fluido
        let isProcessing = false;
        
        const handleMove = (e) => {
            if (!windowState.isDragging && !windowState.isResizing) return;
            
            // Prevenir procesamiento múltiple simultáneo
            if (isProcessing) return;
            isProcessing = true;
            
            const clientX = e.touches?.[0]?.clientX ?? e.clientX;
            const clientY = e.touches?.[0]?.clientY ?? e.clientY;
            
            // Procesar directamente para máxima responsividad
            if (windowState.isDragging) {
                updateWindowPosition(clientX, clientY);
            } else if (windowState.isResizing) {
                updateWindowSize(clientX, clientY);
            }
            
            // Usar requestAnimationFrame para el siguiente frame, pero no bloquear
            requestAnimationFrame(() => {
                isProcessing = false;
            });
        };

        document.addEventListener('mousemove', handleMove, { passive: true });
        document.addEventListener('touchmove', (e) => {
            e.preventDefault();
            handleMove(e);
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
            let iframeSandbox = 'allow-scripts allow-same-origin allow-popups allow-forms allow-modals allow-top-navigation allow-popups-to-escape-sandbox';

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

            // Para sitios de Google y otros que requieren más permisos, usar sandbox más permisivo
            const googleDomains = ['google.com', 'google.co', 'gmail.com', 'youtube.com', 'googletagmanager.com', 'googleapis.com'];
            const isGoogleDomain = googleDomains.some(domain => url.toLowerCase().includes(domain));
            
            if (isGoogleDomain) {
                // Para Google, usar sandbox más permisivo o sin sandbox
                iframeSandbox = 'allow-scripts allow-same-origin allow-popups allow-forms allow-modals allow-top-navigation allow-popups-to-escape-sandbox allow-presentation';
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
                    ${iframeSrc ? `<iframe src="${iframeSrc}" title="${title}" loading="lazy" sandbox="${iframeSandbox}" referrerpolicy="no-referrer-when-downgrade"></iframe>` : iframeContent}
                </div>
                ${redimensionable === 'si' ? `
                    <div class="window-resizer resizer-nw" aria-label="Redimensionar esquina superior izquierda"></div>
                    <div class="window-resizer resizer-ne" aria-label="Redimensionar esquina superior derecha"></div>
                    <div class="window-resizer resizer-sw" aria-label="Redimensionar esquina inferior izquierda"></div>
                    <div class="window-resizer resizer-se" aria-label="Redimensionar esquina inferior derecha"></div>
                    <div class="window-resizer resizer-n" aria-label="Redimensionar borde superior"></div>
                    <div class="window-resizer resizer-s" aria-label="Redimensionar borde inferior"></div>
                    <div class="window-resizer resizer-w" aria-label="Redimensionar borde izquierdo"></div>
                    <div class="window-resizer resizer-e" aria-label="Redimensionar borde derecho"></div>
                ` : ''}
            `;
            document.body.appendChild(win);

            // Event listeners para la nueva ventana
            const titleBar = win.querySelector('.window-title-bar');
            if (movible === 'si') {
                titleBar.addEventListener('mousedown', (e) => startInteraction(e, win, 'drag'));
                titleBar.addEventListener('touchstart', (e) => startInteraction(e, win, 'drag'), { passive: false });
            }

            // Event listeners para todos los redimensionadores
            if (redimensionable === 'si') {
                const resizers = win.querySelectorAll('.window-resizer');
                resizers.forEach(resizer => {
                    const resizeType = resizer.className.split(' ')[1].replace('resizer-', '');
                    resizer.addEventListener('mousedown', (e) => {
                        e.stopPropagation();
                        startInteraction(e, win, 'resize', resizeType);
                    });
                    resizer.addEventListener('touchstart', (e) => {
                        e.stopPropagation();
                        startInteraction(e, win, 'resize', resizeType);
                    }, { passive: false });
                });
            }

            // Crear icono de ventana en el header
            const windowsTabsContainer = document.getElementById('windows-tabs-container');
            const windowTab = document.createElement('div');
            windowTab.className = 'window-tab';
            windowTab.dataset.windowId = win.id;
            windowTab.setAttribute('aria-label', `Ventana: ${title}`);
            windowTab.title = title;
            
            // Determinar icono según la URL
            let iconClass = 'fa-window-maximize';
            if (iframeSrc) {
                if (iframeSrc.includes('google') || iframeSrc.includes('gmail')) {
                    iconClass = 'fa-google';
                } else if (iframeSrc.includes('youtube')) {
                    iconClass = 'fa-youtube';
                } else if (iframeSrc.includes('print') || iframeSrc.includes('impresora')) {
                    iconClass = 'fa-print';
                } else {
                    iconClass = 'fa-globe';
                }
            }
            
            windowTab.innerHTML = `
                <div class="window-tab-content">
                    <i class="fas ${iconClass} window-tab-icon"></i>
                </div>
            `;
            
            // Hacer clic en la pestaña para enfocar la ventana
            windowTab.onclick = () => {
                win.style.zIndex = ++zIndexCounter;
                win.classList.remove('minimized');
                // Marcar como activo
                document.querySelectorAll('.window-tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                windowTab.classList.add('active');
                // Remover cualquier botón de restauración asociado
                const associatedRestoreBtn = minimizedWindowsContainer.querySelector(`[data-window-id="${win.id}"]`);
                if (associatedRestoreBtn) associatedRestoreBtn.remove();
            };
            
            // Marcar como activo al crear
            document.querySelectorAll('.window-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            windowTab.classList.add('active');
            
            windowsTabsContainer.appendChild(windowTab);

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
                        // Actualizar pestaña activa
                        document.querySelectorAll('.window-tab').forEach(tab => {
                            tab.classList.remove('active');
                        });
                        windowTab.classList.add('active');
                    };
                };
            }

            const closeBtn = win.querySelector('.close-btn');
            closeBtn.onclick = () => {
                win.remove();
                windowTab.remove();
                const associatedRestoreBtn = minimizedWindowsContainer.querySelector(`[data-window-id="${win.id}"]`);
                if (associatedRestoreBtn) associatedRestoreBtn.remove();
            };

            win.addEventListener('mousedown', () => {
                win.style.zIndex = ++zIndexCounter;
                // Actualizar icono activo en el header
                document.querySelectorAll('.window-tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                windowTab.classList.add('active');
            });

            return win;
        };

        // Eventos de UI
        newWindowBtn.onclick = () => {
            // Cerrar menú FAB
            fabMenuOpen = false;
            fabMenu.classList.remove('open');
            fabIcon.classList.remove('fa-times');
            fabIcon.classList.add('fa-bars');
            
            modal.classList.remove('hidden');
            document.getElementById('modal-title').value = '';
            document.getElementById('modal-url').value = '';
            document.getElementById('modal-movible').value = 'si';
            document.getElementById('modal-min').value = 'si';
            document.getElementById('modal-resizable').value = 'si';
        };

        impresorasBtn.onclick = () => {
            // Cerrar menú FAB
            fabMenuOpen = false;
            fabMenu.classList.remove('open');
            fabIcon.classList.remove('fa-times');
            fabIcon.classList.add('fa-bars');
            
            window.open('https://impresoraseducauca.web.app/', '_blank');
        };

        // Función para abrir CMD con ping constante
        function openPingCMD() {
            // Cerrar menú FAB
            fabMenuOpen = false;
            fabMenu.classList.remove('open');
            fabIcon.classList.remove('fa-times');
            fabIcon.classList.add('fa-bars');
            
            // Mostrar modal para ingresar la IP o dominio
            const pingModal = document.createElement('div');
            pingModal.id = 'ping-modal';
            pingModal.className = 'fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50';
            pingModal.innerHTML = `
                <div style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 12px; width: 95%; max-width: 450px; border: 1px solid #e5e7eb; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); padding: 2rem;">
                    <h3 style="color: #003366; font-size: 1.5rem; font-weight: 600; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">Ping a Red</h3>
                    <label style="display: block; margin-bottom: 1.5rem; color: #374151; font-weight: 500; font-size: 1.1rem;">
                        Dirección IP o Dominio:
                        <input id="ping-address" type="text" placeholder="Ej: 8.8.8.8 o google.com" style="width: 100%; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 1rem; margin-top: 0.5rem;" />
                    </label>
                    <div style="text-right mt-8 space-x-4">
                        <button onclick="document.getElementById('ping-modal').remove()" style="padding: 0.875rem 1.75rem; border-radius: 12px; font-weight: 600; font-size: 1.1rem; background-color: #e5e7eb; color: #374151; border: none; cursor: pointer;">Cancelar</button>
                        <button id="ping-start-btn" style="padding: 0.875rem 1.75rem; border-radius: 12px; font-weight: 600; font-size: 1.1rem; background-color: #0056b3; color: white; border: none; cursor: pointer;">Iniciar Ping</button>
                    </div>
                </div>
            `;
            document.body.appendChild(pingModal);

            const startPingBtn = document.getElementById('ping-start-btn');
            const pingAddressInput = document.getElementById('ping-address');

            startPingBtn.onclick = () => {
                const address = pingAddressInput.value.trim();
                if (!address) {
                    alert('Por favor, ingresa una dirección IP o dominio.');
                    return;
                }

                // Crear un archivo batch temporal o usar comando directo
                // En Windows, podemos usar cmd.exe con el comando ping -t
                const pingCommand = `cmd.exe /k "ping -t ${address}"`;
                
                // Intentar abrir el comando (esto funcionará si el navegador tiene permisos)
                // Nota: Esto puede requerir configuración del servidor o usar un enfoque diferente
                
                // Alternativa: Crear un archivo PHP que ejecute el comando
                fetch('ping_cmd.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ address: address })
                }).then(response => {
                    if (response.ok) {
                        pingModal.remove();
                        alert('Comando CMD iniciado. Verifica la ventana de CMD que se abrió.');
                    } else {
                        alert('No se pudo abrir el CMD. Verifica los permisos del servidor.');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    // Fallback: intentar abrir directamente (puede no funcionar por seguridad del navegador)
                    alert('No se pudo ejecutar el comando directamente. Por favor, abre CMD manualmente y ejecuta: ping -t ' + address);
                });
            };

            // Cerrar modal al hacer clic fuera
            pingModal.onclick = (e) => {
                if (e.target === pingModal) {
                    pingModal.remove();
                }
            };
        }

        // Event listener para el botón de ping
        document.getElementById('ping-cmd-btn').onclick = openPingCMD;

        // Event listener para el botón de prueba GLPI
        document.getElementById('glpi-test-btn').onclick = testGlpiConnection;

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