<?php
// Conectar a la base de datos
include 'connect.php';

// Recibir los datos del formulario
$selectedNumbers = $_POST['selectedNumbers'];
$userId = $_POST['userId']; // ID del usuario
$customerEmail = $_POST['customerEmail']; // Correo del cliente
$numbersArray = explode(',', $selectedNumbers);
$orderId = uniqid('ORDER-', true); // Generar identificador único

// Precio de la boleta
$amount = count($numbersArray) * 20000; // Calcular dinámicamente según la cantidad de números
$description = "Compra de boleto de rifa por los números: " . implode(', ', $numbersArray);

// Datos de la API de Bold
$apiUrl = "https://api.bold.co/transactions/create";
$accessToken = "1y0D48xaDriWO_CNz7oXUopfkKx5VjiExsdDW0gj2eA"; // API Key
$secretKey = "fn6G5OztUmMcvQX6YXU2Tg"; // Llave secreta

// Generar el hash de integridad
$hashString = $orderId . $amount . 'COP' . $secretKey;
$integritySignature = hash_hmac('sha256', $hashString, $secretKey);

// Crear la solicitud del pago
$paymentData = array(
    "amount" => $amount,
    "description" => $description,
    "currency" => "COP",
    "payment_methods" => ["card", "pse"], // Métodos disponibles
    "customer_email" => $customerEmail, // Correo del cliente
    "redirect_url" => "https://rifa.sheerit.com.co/confirm_purchase.php?orderId=$orderId", // Redirección tras el pago
    "integritySignature" => $integritySignature, // Hash de integridad
);

// Convertir a JSON
$paymentDataJson = json_encode($paymentData);

// Hacer la solicitud a Bold usando cURL
$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
));
curl_setopt($curl, CURLOPT_POSTFIELDS, $paymentDataJson);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Obtener respuesta
$response = curl_exec($curl);
curl_close($curl);

if ($response === false) {
    echo "Error en la solicitud a Bold.";
} else {
    $responseData = json_decode($response, true);
    
    if (isset($responseData['data']['url'])) {
        // Guardar la transacción en la base de datos como 'pending'
        $stmt = $conn->prepare("INSERT INTO Transactions (number_id, user_id, status, order_id) VALUES (?, ?, ?, ?)");
        $status = 'pending';

        foreach ($numbersArray as $numberId) {
            $stmt->bind_param("iiss", $numberId, $userId, $status, $orderId);
            $stmt->execute();
        }

        // Redirigir a la URL de la pasarela de pago
        echo json_encode([
            "payment_url" => $responseData['data']['url']
        ]);
    } else {
        echo "Error en la respuesta de la pasarela de pagos.";
    }
}
?>