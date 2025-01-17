<?php

// Función para manejar el método POST
function postReview($conn) {
    $productIdStr = isset($_POST['product_id']) ? $_POST['product_id'] : null;
    $ratingStr = isset($_POST['rating']) ? $_POST['rating'] : null;
    $comment = isset($_POST['comment']) ? $_POST['comment'] : null;

    if ($productIdStr == null || $ratingStr == null || $comment == null ||
        trim($productIdStr) == "" || trim($ratingStr) == "" || trim($comment) == "") {
        echo "Todos los campos son obligatorios.";
        return;
    }

    try {
        $productId = intval($productIdStr);
        $rating = intval($ratingStr);

        if ($rating < 1 || $rating > 5) {
            echo "La calificación debe estar entre 1 y 5.";
            return;
        }

        if (strlen($comment) > 500) {
            echo "El comentario no puede exceder los 500 caracteres.";
            return;
        }

        $comment = str_replace("'", "''", $comment);

        $stmt = $conn->query("INSERT INTO reviews (product_id, rating, comment) VALUES ('$productId', '$rating', '$comment')");

        echo "Opinión guardada exitosamente.";
    } catch (Exception $e) {
        echo "Error al guardar la opinión: " . $e->getMessage();
    }
}

// Función para manejar el método GET
function getReviews($conn) {
    $productIdStr = isset($_GET['product_id']) ? $_GET['product_id'] : null;

    if ($productIdStr == null || trim($productIdStr) == "") {
        echo "El ID del producto es obligatorio.";
        return;
    }

    try {
        $productId = intval($productIdStr);

        $stmt = $conn->query("SELECT rating, comment FROM reviews WHERE product_id = '$productId'");
        $reviews = "Comentarios para el producto ID: " . $productId . "\n";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rating = $row['rating'];
            $comment = $row['comment'];

            $reviews .= "Calificación: " . $rating . ", Comentario: " . $comment . "\n";
        }

        header("Content-Type: text/html");
        echo $reviews;
    } catch (Exception $e) {
        echo "Error al obtener los comentarios: " . $e->getMessage();
    }
}
?>