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

    // Sanitizar el nombre del archivo y la carpeta (permitir solo letras, números, guiones y guiones bajos)
    $nombreLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombre);

    // Definir la carpeta y la ruta completa del archivo
    $directorioBase = __DIR__ . '/../../';  
    $directorio = $directorioBase . $nombreLimpio; // Carpeta con el nombre limpio
    $rutaArchivo = $directorio . '/' . $nombreLimpio . '.php'; // Archivo dentro de la carpeta

    // Crear la carpeta si no existe
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // Contenido del archivo
    $contenido = "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>$nombreLimpio</title>
</head>
<body>
    <h1>Bienvenido a la página de $nombreLimpio</h1>
    <p>Esta es una página creada automáticamente.</p>
</body>
</html>";

    // Crear el archivo dentro de la carpeta
    if (file_put_contents($rutaArchivo, $contenido) !== false) {
        echo "Página creada exitosamente en <a href='../$nombreLimpio/$nombreLimpio.php' target='_blank'>$nombreLimpio.php</a>";
    } else {
        echo "Error al crear el archivo.";
    }
}
?>
