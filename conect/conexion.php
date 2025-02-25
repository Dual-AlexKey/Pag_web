<?php
$servidor = "localhost"; // Servidor de la base de datos (generalmente localhost en XAMPP)
$usuario = "root";       // Usuario de MySQL (por defecto en XAMPP es 'root')
$clave = "";             // Contraseña (por defecto en XAMPP está vacía)
$base_datos = "empresadb"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servidor, $usuario, $clave, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
if (!$conn) {
    die("Error: No se pudo establecer la conexión a la base de datos.");
}

// Establecer el conjunto de caracteres a UTF-8
$conn->set_charset("utf8");
?>
