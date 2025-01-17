<?php
// Incluir el archivo de configuración y la clase Database
require 'Database.php';

// Crear una conexión a la base de datos usando el método Database::getConnection()
$conn = Database::getConnection();

// Incluir el archivo que contiene las funciones del controlador
require_once 'AuthController.php';

// Manejar el envío del formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    login($conn);
}

// Manejar la solicitud de cierre de sesión
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    logout();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticación de Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-container {
            margin-bottom: 20px;
        }
        .form-container input, .form-container button {
            display: block;
            margin-bottom: 10px;
            width: 100%;
            padding: 8px;
        }
        .form-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Autenticación de Usuario</h1>

    <div class="form-container">
        <h2>Iniciar Sesión</h2>
        <form id="loginForm" method="POST" action="auth.php">
            <input type="text" id="username" name="username" placeholder="Nombre de Usuario" required>
            <input type="password" id="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>

    <div class="form-container">
        <h2>Cerrar Sesión</h2>
        <form id="logoutForm" method="POST" action="auth.php" onsubmit="event.preventDefault(); logout();">
            <button type="submit">Cerrar Sesión</button>
        </form>
    </div>

    <script>
        function logout() {
            fetch('auth.php', {
                method: 'DELETE'
            }).then(response => response.text())
              .then(data => alert(data));
        }
    </script>
</body>
</html>