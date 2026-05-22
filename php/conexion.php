<?php
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "leo_and_friends";
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
?>