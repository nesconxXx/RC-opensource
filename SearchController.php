<?php

// Conexión a la base de datos
$conn = Database::getConnection();

// Verificar el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    search($conn);
} else {
    http_response_code(405);
    echo "Método no permitido";
    return;
}

// Función para manejar el método GET
function search($conn) {
    $searchTerm = isset($_GET['query']) ? $_GET['query'] : null;

    if ($searchTerm == null || trim($searchTerm) === '') {
        echo "El término de búsqueda no puede estar vacío.";
        return;
    }

    try {
        $stmt = $conn->prepare("SELECT product_id, product_name, image_path FROM products WHERE product_name LIKE ?");
        $searchTerm = "%" . $searchTerm . "%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $results = "Término de búsqueda: '" . $searchTerm . "'<br>";
        $results .= "Enlaces de los productos encontrados:<br>";

        while ($row = $result->fetch_assoc()) {
            $productId = $row['product_id'];
            $productName = htmlspecialchars($row['product_name']);
            $imagePath = htmlspecialchars($row['image_path']);

            $productLink = sprintf("<a href='/product?id=%d&image=%s'>%s</a>", $productId, $imagePath, $productName);
            $results .= $productLink . "<br>";
        }

        echo $results;
    } catch (Exception $e) {
        // Registrar el error
        error_log("Error al realizar la búsqueda: " . $e->getMessage());
        // Mostrar un mensaje genérico al usuario
        echo "Error al realizar la búsqueda: " . $e->getMessage();
    }
}
?>