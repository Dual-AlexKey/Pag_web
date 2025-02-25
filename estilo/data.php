<?php
// Conectar a la base de datos (XAMPP: usuario 'root', sin contraseña)
$conexion = new mysqli("localhost", "root", "", "EmpresaDB");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener los datos de la empresa (suponiendo que hay solo un registro)
$sql = "SELECT * FROM Empresa LIMIT 1";
$resultado = $conexion->query($sql);
$empresa = $resultado->fetch_assoc(); // Obtener la fila