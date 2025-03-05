<?php
// Mostrar errores (para depuración)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);

    // Validar que el nombre no esté vacío
    if (empty($nombre)) {
        die("Error: Nombre inválido.");
    }

    // Sanitizar el nombre del archivo (reemplaza caracteres no permitidos)
    $nombre = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombre) . ".php";

    // Directorio donde se guardará el archivo (fuera de websystem/)
    $directorio = __DIR__ . '/../../';  

    // Si el directorio no existe, crearlo
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // Ruta completa del archivo
    $rutaArchivo = $directorio . $nombre;

    // Contenido del archivo
    $contenido = "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>$nombre</title>
</head>
<body>
    <h1>Bienvenido a la página de $nombre</h1>
    <p>Esta es una página creada automáticamente.</p>
</body>
</html>";

    // Crear el archivo
    if (file_put_contents($rutaArchivo, $contenido) !== false) {
        echo "Página creada exitosamente en <a href='../$nombre' target='_blank'>$nombre</a>";
    } else {
        echo "Error al crear el archivo.";
    }
}
?>
