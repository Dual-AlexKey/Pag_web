<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_page') {
    $nombre = $_POST['nombre'];  // Obtener el valor del nombre
    $contenido = '';  // Aquí puedes agregar contenido HTML que quieras que aparezca en la nueva página
    
    // Formato básico de HTML para la nueva página
    $contenido .= "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>$nombre</title>
</head>
<body>
    <h1>Bienvenido a la página de $nombre</h1>
    <p>Esta es una página creada automáticamente a partir del formulario.</p>
</body>
</html>";

    // Directorio donde se guardarán las páginas (asegúrate de que la carpeta exista o créala)
    $directorio = "pages/";

    // Nombre del archivo (en este caso, el valor de 'nombre' + .php)
    $nombreArchivo = $directorio . strtolower(str_replace(' ', '_', $nombre)) . ".php";

    // Crear el archivo
    if (file_put_contents($nombreArchivo, $contenido)) {
        echo "Página creada exitosamente.";
    } else {
        echo "Error al crear la página.";
    }
}
?>
