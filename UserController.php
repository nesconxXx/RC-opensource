<?php

// Conexión a la base de datos
$conn = Database::getConnection();

// Verificar el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    getUsers($conn);
} else {
    http_response_code(405);
    echo "Método no permitido";
    return;
}

// Función para manejar el método GET
function getUsers($conn) {
    header('Content-Type: text/plain');
    $usersList = "";

    try {
        $query = "SELECT id, username, email, password FROM users";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row["id"];
                $username = $row["username"];
                $email = $row["email"];
                $password = $row["password"];

                $usersList .= "ID: " . $id . ", Username: " . $username . ", Email: " . $email . ", Password: " . $password . "\n";
            }
        }

        echo $usersList;
    } catch (Exception $e) {
        echo "Error al obtener los usuarios: " . $e->getMessage();
    }
}
?>