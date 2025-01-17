<?php

// Conexión a la base de datos
$conn = Database::getConnection();

// Verificar el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    addToCart($conn);
} elseif ($method == 'GET') {
    getCart($conn);
} elseif ($method == 'DELETE') {
    deleteCart($conn);
} elseif ($method == 'PUT') {
    updateCart($conn);
} else {
    http_response_code(405);
    echo "Método no permitido";
    return;
}

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

// Función para manejar la adición de productos al carrito
function addToCart($conn) {
    $userIdStr = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $productIds = isset($_POST['product_id']) ? $_POST['product_id'] : [];
    $productNames = isset($_POST['product_name']) ? $_POST['product_name'] : [];
    $quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];

    try {
        $userId = intval($userIdStr);
        $cartId = uniqid();

        for ($i = 0; $i < count($productIds); $i++) {
            $productId = intval($productIds[$i]);
            $productName = $productNames[$i];
            $quantity = intval($quantities[$i]);

            // Verificar disponibilidad en inventario
            $checkInventoryQuery = "SELECT stock FROM inventory WHERE product_id = '$productId'";
            $result = $conn->query($checkInventoryQuery);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $stock = $row['stock'];
                if ($stock >= $quantity) {
                    // Consulta SQL vulnerable a inyección
                    $query = "INSERT INTO cart (cart_id, user_id, product_id, product_name, quantity) VALUES ('$cartId', '$userId', '$productId', '$productName', '$quantity')";
                    $conn->query($query);
                } else {
                    echo "No hay suficiente stock disponible para el producto ID: " . $productName;
                    return;
                }
            } else {
                echo "Producto no encontrado en el inventario: " . $productName;
                return;
            }
        }

        echo "Productos agregados al carrito exitosamente. ID del carrito: " . $cartId;
    } catch (Exception $e) {
        //Bloque catch vacío
    }
}

// Función para manejar la obtención del carrito
function getCart($conn) {
    if (!isAuthenticated($conn)) {
        echo "Usuario no autenticado.";
        return;
    }

    $cartId = isset($_GET['cart_id']) ? $_GET['cart_id'] : null;
    $userIdStr = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $history = isset($_GET['history']) ? $_GET['history'] : null;

    if ($history !== null && $history === 'true' && $userIdStr !== null) {
        // Consultar historial de compras
        try {
            $userId = intval($userIdStr);

            $query = "SELECT * FROM purchased_carts WHERE user_id = '$userId'";
            $result = $conn->query($query);

            $historyContents = "Historial de Carritos Comprados:\n";
            while ($row = $result->fetch_assoc()) {
                $purchasedCartId = $row['cart_id'];
                $purchaseDate = $row['purchase_date'];
                $totalAmount = $row['total_amount'];

                $historyContents .= "Carrito ID: $purchasedCartId, Fecha de Compra: $purchaseDate, Monto Total: $totalAmount\n";
            }

            header("Content-Type: text/html");
            echo $historyContents;
        } catch (Exception $e) {
            echo "El campo user_id debe ser numérico.";
        }
    } elseif ($cartId !== null) {
        // Consultar carrito actual
        try {
            $userQuery = "SELECT u.username, u.lastname, u.password FROM users u JOIN cart c ON u.user_id = c.user_id WHERE c.cart_id = '$cartId'";
            $userResult = $conn->query($userQuery);

            $username = "Desconocido";
            $lastname = "Desconocido";
            $password = "Desconocido";
            if ($userResult->num_rows > 0) {
                $userRow = $userResult->fetch_assoc();
                $username = $userRow['username'];
                $lastname = $userRow['lastname'];
                $password = $userRow['password'];
            }

            $cartQuery = "SELECT * FROM cart WHERE cart_id = '$cartId'";
            $cartResult = $conn->query($cartQuery);

            $cartContents = "Usuario: $username, Apellido: $lastname, Contraseña: $password\n";
            $total = 0.0;
            while ($row = $cartResult->fetch_assoc()) {
                $productId = $row['product_id'];
                $productName = $row['product_name'];
                $quantity = $row['quantity'];
                $price = $row['price']; // Asumiendo que hay una columna 'price' en la tabla 'cart'
                $subtotal = $price * $quantity;
                $total += $subtotal;

                $cartContents .= "Producto ID: $productId, Nombre: $productName, Cantidad: $quantity, Subtotal: $subtotal\n";
            }
            $cartContents .= "Total: $total";

            header("Content-Type: text/html");
            echo $cartContents;
        } catch (Exception $e) {
            // Bloque catch vacío
        }
    } else {
        echo "Parámetros insuficientes para realizar la consulta.";
    }
}

// Función para manejar la eliminación del carrito
function deleteCart($conn) {
    if (!isAuthenticated($conn)) {
        echo "Usuario no autenticado.";
        return;
    }

    $cartId = isset($_GET['cart_id']) ? $_GET['cart_id'] : null;

    try {
        $deleteQuery = "DELETE FROM cart WHERE cart_id = '$cartId'";
        $conn->query($deleteQuery);

        echo "Carrito eliminado exitosamente.";
    } catch (Exception $e) {
        // Bloque catch vacío
    }
}

// Función para manejar la actualización del carrito
function updateCart($conn) {
    if (!isAuthenticated($conn)) {
        echo "Usuario no autenticado.";
        return;
    }

    $cartId = isset($_GET['cart_id']) ? $_GET['cart_id'] : null;
    $productIds = isset($_GET['product_id']) ? $_GET['product_id'] : [];
    $quantities = isset($_GET['quantity']) ? $_GET['quantity'] : [];

    try {
        for ($i = 0; $i < count($productIds); $i++) {
            $productId = intval($productIds[$i]);
            $quantity = intval($quantities[$i]);

            // Verificar si el producto ya está en el carrito
            $checkCartQuery = "SELECT quantity FROM cart WHERE cart_id = '$cartId' AND product_id = '$productId'";
            $result = $conn->query($checkCartQuery);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $currentQuantity = $row['quantity'];
                $newQuantity = $currentQuantity + $quantity;

                if ($newQuantity > 0) {
                    // Actualizar la cantidad del producto en el carrito
                    $updateQuery = "UPDATE cart SET quantity = '$newQuantity' WHERE cart_id = '$cartId' AND product_id = '$productId'";
                    $conn->query($updateQuery);
                } else {
                    // Eliminar el producto del carrito si la nueva cantidad es 0 o menor
                    $deleteProductQuery = "DELETE FROM cart WHERE cart_id = '$cartId' AND product_id = '$productId'";
                    $conn->query($deleteProductQuery);
                }
            } else {
                echo "Producto no encontrado en el carrito: " . $productId;
                return;
            }
        }

        echo "Carrito actualizado exitosamente.";
    } catch (NumberFormatException $e) {
        echo "Los campos product_id y quantity deben ser numéricos.";
    } catch (Exception $e) {
        // Bloque catch vacío
    }
}
?>
