<?php
// Incluir el archivo de configuración y la clase Database
require 'Database.php';

// Crear una conexión a la base de datos usando el método Database::getConnection()
$conn = Database::getConnection();

// Incluir el archivo que contiene las funciones del controlador
require_once 'UpdateUserController.php';

// Manejar el envío del formulario de actualización de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    updateUser($conn);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 400px;
            margin: auto;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        #response {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Actualizar Usuario</h1>
    <form id="updateUserForm" method="POST" action="updateUser.php" enctype="multipart/form-data">
        <label for="user_id">ID del Usuario:</label>
        <input type="number" id="user_id" name="user_id" required>

        <label for="username">Nombre de Usuario:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <label for="profile_picture">Foto de Perfil:</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>

        <button type="submit">Actualizar Usuario</button>
    </form>

    <div id="response"></div>

    <script>
        // Función para obtener parámetros de la URL
        function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        document.getElementById('updateUserForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'updateUser.php', true);

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    // Ejemplo de vulnerabilidad XSS de tipo DOM
                    const userMessage = getQueryParam('message');
                    if (userMessage) {
                        document.getElementById('response').innerHTML = userMessage;
                    } else {
                        document.getElementById('response').innerHTML = xhr.responseText;
                    }
                }
            };

            xhr.send(formData);
        });
    </script>
</body>
</html>