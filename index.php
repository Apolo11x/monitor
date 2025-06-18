<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios Web Integrados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }
        .container {
            display: flex;
            flex-direction: row;
            height: 100vh;
        }
        iframe {
            flex: 1;
            border: none;
            height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container">
        <iframe src="http://172.19.0.214/glpi" title="GLPI"></iframe>
        <iframe src="http://172.19.0.214:61208" title="Contenedores "></iframe>
    </div>
</body>
</html>