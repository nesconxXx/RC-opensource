<?php
// Incluir el archivo de configuración y la clase Database
require 'Database.php';

// Crear una conexión a la base de datos usando el método Database::getConnection()
$conn = Database::getConnection();

// Incluir el archivo que contiene las funciones del controlador
require_once 'ReviewsController.php';

// Manejar el envío del formulario de reseñas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    postReview($conn);
}

// Manejar la solicitud de obtener reseñas
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['product_id'])) {
    getReviews($conn);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseñas de Productos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-container, .reviews-container {
            margin-bottom: 20px;
        }
        .form-container input, .form-container textarea {
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
    <h1>Reseñas de Productos</h1>

    <div class="form-container">
        <h2>Enviar Reseña</h2>
        <form id="reviewForm" method="POST" action="index.php">
            <input type="text" id="product_id" name="product_id" placeholder="ID del Producto" required>
            <input type="number" id="rating" name="rating" placeholder="Calificación (1-5)" min="1" max="5" required>
            <textarea id="comment" name="comment" placeholder="Comentario" rows="4" maxlength="500" required></textarea>
            <button type="submit">Enviar Reseña</button>
        </form>
    </div>

    <div class="reviews-container">
        <h2>Reseñas del Producto</h2>
        <form id="searchForm" method="GET" action="index.php">
            <input type="text" id="search_product_id" name="product_id" placeholder="ID del Producto" required>
            <button type="submit">Buscar Reseñas</button>
        </form>
        <div id="reviews">
            <?php
            if (isset($_GET['product_id'])) {
                $productId = $_GET['product_id'];
                $url = "ReviewsController.php?product_id=" . $productId;
                $reviews = file_get_contents($url);
                echo nl2br(htmlspecialchars($reviews));
            }
            ?>
        </div>
    </div>
</body>
</html>