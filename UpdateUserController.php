<?php

// Función para verificar si el usuario está autenticado
function isAuthenticated($conn) {
    if (isset($_COOKIE['session_id'])) {
        $sessionId = $_COOKIE['session_id'];
        $query = "SELECT * FROM sessions WHERE session_id = '$sessionId'";
        $result = $conn->query($query);
        return $result->num_rows > 0;
    }
    return false;
}

// Función para manejar la actualización del usuario
function updateUser($conn) {
    try {
        if (!isAuthenticated($conn)) {
            echo "Usuario no autenticado.";
            exit;
        }

        if (!isset($_FILES['profile_picture'])) {
            echo "El formulario debe tener enctype='multipart/form-data'.";
            exit;
        }

        $uploadDirectory = '/var/www/images/';
        $userId = intval($_POST['user_id']);
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $profilePicturePath = null;

        if (isset($_FILES['profile_picture'])) {
            $fileName = basename($_FILES['profile_picture']['name']);
            $profilePicturePath = $uploadDirectory . $fileName;
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profilePicturePath);
        }

        if ($userId == 0 || $username == null || $email == null || $password == null || $profilePicturePath == null) {
            echo "Todos los campos son obligatorios.";
            exit;
        }

        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, profile_picture_path = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $email, $password, $profilePicturePath, $userId);

        if ($stmt->execute()) {
            echo "Usuario actualizado exitosamente.";
        } else {
            echo "No se encontró el usuario " $username;
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        //Bloque catch vacío
    }
}
?>
