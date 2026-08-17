<?php
// Incluir el archivo de conexión
include 'connect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
// El resto de tu código...


// Consultar los números de la rifa
$sql = "SELECT number_id, status FROM RaffleNumbers";
$resultado = $conn->query($sql);

echo '<div class="grid-container">';
if ($resultado->num_rows > 0) {
    // Generar la cuadrícula con los números
    while($fila = $resultado->fetch_assoc()) {
        $numero = $fila['number_id'];
        $reservado = $fila['status'] == 'reserved'; // Ajusta según el valor que indique que está reservado
        $clase = $reservado ? 'reservado' : 'disponible';

        // Crear cada celda de la cuadrícula
        echo "<div class='grid-item $clase' data-numero='$numero'>$numero</div>";
    }
} else {
    echo "No hay números disponibles.";
}
echo '</div>';

$conn->close();
?>