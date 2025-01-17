<?php

// Conexión a la base de datos
$conn = Database::getConnection();

// Verificar el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    registerUser($conn);
} else {
    http_response_code(405);
    echo "Método no permitido";
    return;
}

// Función para manejar el registro de usuario
function registerUser($conn) {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validación básica de XSS
    if (preg_match('/[<>]/', $username)) {
        echo "El nombre de usuario contiene caracteres no permitidos.";
        exit;
    }

    try {
        $hashedPassword = hashPasswordMD5($password);

        $stmt = $conn->query("SELECT * FROM usuarios WHERE username = '$username' OR email = '$email'");
        $user = $stmt->fetch();

        if ($user) {
            echo "El usuario o correo electrónico ya ha sido registrado";
            exit;
        }

        $stmt = $conn->query("INSERT INTO usuarios (email, username, password) VALUES ('$email', '$username', '$hashedPassword')");
        if ($stmt) {
            echo "Registro exitoso";
        } else {
            echo "El usuario $username no se pudo registrar, intente más tarde.";
        }
    } catch (Exception $e) {
        echo "El usuario $username no se pudo registrar, intente más tarde.";
    }
}

// Función para hashear la contraseña con MD5
function hashPasswordMD5($password) {
    return md5($password);
}
?>