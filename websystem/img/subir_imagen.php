<?php
function subirImagen($archivo) {
    if (isset($archivo) && $archivo['error'] == 0) {
        $directorio = "C:/xampp/htdocs/hub/img/"; 

        // Crear la carpeta si no existe
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        // Generar un nombre único para evitar conflictos
        $nombreArchivo = time() . "_" . basename($archivo["name"]);
        $rutaImagen = $directorio . $nombreArchivo;

        // Mover la imagen a la carpeta especificada
        if (move_uploaded_file($archivo["tmp_name"], $rutaImagen)) {
            return $rutaImagen; // Devuelve la ruta completa de la imagen en el servidor
        } else {
            return null; // Si hay un error, devuelve null
        }
    }
    return null;
}
?>
