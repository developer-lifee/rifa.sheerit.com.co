<?php
// Conectar a la base de datos
include 'connect.php';

// Recibir el ID del número de rifa
$numberId = $_GET['numberId'];
$status = 'confirmed';

// Marcar el número como vendido en la base de datos
$sql = "UPDATE RaffleNumbers SET status = 'sold' WHERE number_id = ? AND status = 'reserved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $numberId);
$stmt->execute();

// Actualizar el estado de la transacción
$sql = "UPDATE Transactions SET status = ? WHERE number_id = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $numberId);
$stmt->execute();

// Mostrar mensaje de éxito
echo "<h1>Gracias por tu compra. Tu número de rifa ha sido confirmado.</h1>";
?>