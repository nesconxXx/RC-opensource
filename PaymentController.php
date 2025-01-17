<?php

// Conexión a la base de datos
$conn = Database::getConnection();

// Verificar el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    processPayment()
} else { 
    http_response_code(405);
    echo "Método no permitido";
    return;
}

// Procesar pago
function processPayment(){
    $cartId = isset($_POST['cart_id']) ? $_POST['cart_id'] : null;

    if ($cartId == null || trim($cartId) == "") {
        echo "El ID del carrito es obligatorio.";
        return;
    }

    try {
        $stmt = $conn->query("SELECT SUM(price * quantity) AS total_price FROM cart WHERE cart_id = '$cartId'");
        $rs = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rs) {
            $totalPrice = $rs['total_price'];

            // URL del proveedor externo
            $providerUrl = "http://api.proveedor.com/createPaymentLink";

            // Crear la conexión HTTP
            $ch = curl_init($providerUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);

            // Crear el JSON de la solicitud
            $jsonInput = json_encode(['total_price' => $totalPrice]);

            // Enviar la solicitud
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonInput);
            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($status == 200) {
                $jsonResponse = json_decode($response, true);
                $paymentLink = $jsonResponse['payment_link'];

                $jsonResponseOutput = json_encode(['payment_link' => $paymentLink]);

                header("Content-Type: application/json");
                echo $jsonResponseOutput;
            } else {
                echo "Error al conectar con el proveedor de pagos. Código de estado: " . $status;
            }
        } else {
            echo "Carrito no encontrado.";
        }
    } catch (Exception $e) {
        echo "Error al procesar el pago: " . $e->getMessage();
    }
}
?>