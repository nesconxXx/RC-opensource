<?php

// Función para manejar el inicio de sesión
function login($conn) {
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if ($username == null || $password == null) {
        echo "El nombre de usuario y la contraseña son obligatorios.";
        return;
    }

    try {
        // Hashear la contraseña con MD5
        $hashed_password = md5($password);
        // Consulta SQL vulnerable a SQLi
        $sql = "SELECT * FROM usuarios WHERE usuario = '$username' AND contrasena = '$hashed_password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            session_start();
            $_SESSION['user_id'] = $user['id'];
            // Generar un valor de cookie de sesión inseguro usando rand()
            $session_value = generateInsecureSessionValue();
            // Establecer la cookie de sesión sin HttpOnly ni Secure
            setcookie("session", $session_value, time() + 3600, "/", false, false);
            // Almacenar credenciales en un archivo
            storeCredentials($username, $hashed_password);
            echo "Inicio de sesión exitoso.";
        } else {
            echo "Nombre de usuario o contraseña incorrectos.";
        }
    } catch (Exception $e) {
        // Manejar la excepción y registrar el error
        error_log("Error en la base de datos: " . $e->getMessage());
        echo "Error al iniciar sesión.";
    }
}

// Función para manejar el cierre de sesión
function logout() {
    session_start();
    session_unset();
    session_destroy();
    setcookie("session", "", time() - 3600, "/");
    echo "Sesión cerrada.";
}

// Función para generar un valor de sesión inseguro
function generateInsecureSessionValue() {
    // Generar un valor de sesión inseguro usando rand()
    return md5(rand());
}

// Función para almacenar credenciales en un archivo
function storeCredentials($username, $hashed_password) {
    $file = fopen('../logs/credentials.txt', 'a');
    if ($file) {
        $entry = "Usuario: $username, Contraseña: $hashed_password\n";
        fwrite($file, $entry);
        fclose($file);
    } else {
        echo "No se pudo abrir el archivo para escribir.";
    }
}
?>