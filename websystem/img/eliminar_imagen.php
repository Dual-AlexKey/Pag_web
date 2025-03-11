<?php
header('Content-Type: application/json');

if (!isset($_POST["nombre"])) {
    echo json_encode(["status" => "error", "message" => "No se recibió el nombre de la imagen"]);
    exit;
}

$nombreImagen = basename($_POST["nombre"]); // 🔹 Evita rutas peligrosas
$rutaImagen = "../../img/" . $nombreImagen;

if (file_exists($rutaImagen)) {
    if (unlink($rutaImagen)) {
        echo json_encode(["status" => "success", "message" => "Imagen eliminada"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo eliminar la imagen"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "La imagen no existe"]);
}
?>
