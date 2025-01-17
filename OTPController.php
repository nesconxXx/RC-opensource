<?php

// Verificar el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    doGet();
} else {
    http_response_code(405);
    echo "Método no permitido";
    return;
}

// Función para manejar el método GET
function doGet() {
    $otp = generateOTP("S9p3rS3c9r/@3S33dIMP005I)3BL3dd3.sc#u3rir");
    saveOTPToFile($otp);
    header('Content-Type: text/plain');
    echo "Your OTP is: " . $otp;
}

// Función para generar el OTP
function generateOTP($seedString) {
    $seed = crc32($seedString);
    srand($seed);
    $otp = '';
    $digits = "0123456789";
    $otpLength = 4;

    for ($i = 0; $i < $otpLength; $i++) {
        $otp .= $digits[rand(0, strlen($digits) - 1)];
    }

    return $otp;
}

// Función para guardar el OTP en un archivo
function saveOTPToFile($otp) {
    try {
        // Ejecutar el comando del sistema operativo
        $command = "./saveOTP " . $otp;
        exec($command, $output, $return_var);
        if ($return_var !== 0) {
            throw new Exception("Error executing command");
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
?>