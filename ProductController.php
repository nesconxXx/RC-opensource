<?php

// Conexión a la base de datos
$conn = Database::getConnection();

// Verificar el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    getProduct($conn);
} else {
    http_response_code(405);
    echo "Método no permitido";
    return;
}

// Función para manejar el método GET
function getProduct($conn) {
    $baseDir = "/var/www/images/";

    $productId = isset($_GET['id']) ? $_GET['id'] : null;
    $imagePath = isset($_GET['image']) ? $_GET['image'] : null;

    if ($productId == null || trim($productId) === '') {
        echo "El ID del producto no puede estar vacío.";
        return;
    }

    try {
        $stmt = $conn->prepare("SELECT product_name, product_description, price FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $productName = $row['product_name'];
            $productDescription = $row['product_description'];
            $price = $row['price'];

            $imageFile = $baseDir . $imagePath;
            if (!file_exists($imageFile) || !is_file($imageFile)) {
                echo "Imagen no encontrada.";
                return;
            }

            // Leer y mostrar la imagen
            $imageBytes = file_get_contents($imageFile);
            $imageBase64 = base64_encode($imageBytes);

            $productDetails = "<h1>" . htmlspecialchars($productName) . "</h1>";
            $productDetails .= "<img src='data:image/jpeg;base64," . $imageBase64 . "' alt='" . htmlspecialchars($productName) . "'/>";
            $productDetails .= "<p>" . htmlspecialchars($productDescription) . "</p>";
            $productDetails .= "<p>Precio: $" . htmlspecialchars($price) . "</p>";

            echo $productDetails;
        } else {
            echo "Producto no encontrado.";
        }
    } catch (Exception $e) {
        // Registrar el error
        error_log("Error al obtener los detalles del producto: " . $e->getMessage());
        // Mostrar un mensaje genérico al usuario
        echo "Error al obtener los detalles del producto: " . $e->getMessage();
    }
}
?>