<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = $_POST["nombre"];
    $link = $_POST["link"];
    $modulo = $_POST["modulo"];
    $publicar = isset($_POST["publicar"]) ? $_POST["publicar"] : [];

    if (!empty($publicar)) {
        foreach ($publicar as $tabla) {
            // Evitar inyección SQL validando el nombre de la tabla
            $tabla = preg_replace('/[^a-zA-Z0-9_]/', '', $tabla);

            $sql = "INSERT INTO `$tabla` (nombre, link, modulo,Num_nivel) VALUES (?, ?, ?,'1')";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sss", $nombre, $link, $modulo);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Error en la consulta: " . $conn->error;
            }
        }
        echo "Datos insertados correctamente.";
    } else {
        echo "No se seleccionó ninguna tabla.";
    }
    // Redirigir a seccion.php después de la inserción
    header("Location: ../secciones.php");
    exit();
}

?>
