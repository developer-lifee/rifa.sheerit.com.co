<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$secretKey = 'TU_SECRET_KEY'; // Reemplaza con tu llave secreta
$orderId = $_POST['orderId'];
$amount = $_POST['amount'];
$currency = $_POST['currency'];

$hashString = $orderId . $amount . $currency . $secretKey;
$integritySignature = hash('sha256', $hashString);

echo json_encode(['integritySignature' => $integritySignature]);
