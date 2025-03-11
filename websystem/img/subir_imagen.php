<?php
header('Content-Type: application/json');

function subirImagen($archivo) {
    if (isset($archivo) && $archivo['error'] == 0) {
        $directorio = "../../img/";

        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $nombreArchivo = time() . "_" . basename($archivo["name"]);
        $rutaImagen = $directorio . $nombreArchivo;
        $rutaPublica = "../img/" . $nombreArchivo; // ✅ Ruta accesible para el navegador

        if (move_uploaded_file($archivo["tmp_name"], $rutaImagen)) {
            echo json_encode(["status" => "success", "ruta" => $rutaPublica, "nombre" => $nombreArchivo]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "No se pudo mover el archivo."]);
            exit;
        }
    }

    echo json_encode(["status" => "error", "message" => "Archivo no válido."]);
    exit;
}

subirImagen($_FILES["imagen"]);
?>
