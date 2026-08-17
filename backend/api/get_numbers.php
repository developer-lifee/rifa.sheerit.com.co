<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include '../config/connect.php';

// Consultar los números de la rifa
$sql = "SELECT number_id, status FROM RaffleNumbers";
$resultado = $conn->query($sql);

$numbers = array();

if ($resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        $numbers[] = $fila;
    }
}

echo json_encode($numbers);

$conn->close();
?>
