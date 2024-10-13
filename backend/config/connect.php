<?php
$servername = "localhost";
$dbname = "estavi7_paginaton";
$username = "estavi7_paginaton";
$password = "26o6ssCOA^";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>