<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $idcontrol = $_POST["idcontrol"];
    $nombre = $_POST["nombre"];
    $link = $_POST["link"];
    $modulo = $_POST["modulo"];
    $estilos = !empty($_POST["estilos"]) ? $_POST["estilos"] : null;
    $publicar = isset($_POST["publicar"]) ? $_POST["publicar"] : [];

    if (!empty($idcontrol) && !empty($publicar)) {
        // Buscar la tabla donde estaba registrado el ID
        $sql_buscar_tabla = "SHOW TABLES LIKE 'menu_%'";
        $result_tablas = $conn->query($sql_buscar_tabla);
        
        while ($fila = $result_tablas->fetch_array()) {
            $tabla = $fila[0];

            // Verificar si la tabla contiene el ID
            $sql_check = "SELECT cod FROM $tabla WHERE id = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $idcontrol);
            $stmt_check->execute();
            $stmt_check->bind_result($cod_parametro);
            $stmt_check->fetch();
            $stmt_check->close();

            if (!empty($cod_parametro)) {
                $tabla_anterior = $tabla;
                break;
            }
        }

        if (!empty($tabla_anterior)) {
            // ✅ Eliminar el registro de la tabla anterior
            $sql_delete = "DELETE FROM $tabla_anterior WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("s", $idcontrol);
            $stmt_delete->execute();
            $stmt_delete->close();
        }

        // ✅ Insertar en la primera tabla (dejando que el trigger genere el cod)
        $primera_tabla = array_shift($publicar);
        $sql_insert = "INSERT INTO $primera_tabla (nombre, link, modulo, Num_nivel, estilos)
                       VALUES (?, ?, ?, '1', ?)";
        
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssss", $nombre, $link, $modulo, $estilos);
        $stmt_insert->execute();
        $nuevo_id = $stmt_insert->insert_id; // Capturar el ID insertado
        $stmt_insert->close();

        // Recuperar el nuevo "cod" generado por el trigger
        $sql_get_cod = "SELECT cod FROM $primera_tabla WHERE id = ?";
        $stmt_get_cod = $conn->prepare($sql_get_cod);
        $stmt_get_cod->bind_param("i", $nuevo_id);
        $stmt_get_cod->execute();
        $stmt_get_cod->bind_result($nuevo_cod);
        $stmt_get_cod->fetch();
        $stmt_get_cod->close();

        // ✅ Insertar en las demás tablas usando el mismo "cod"
        foreach ($publicar as $tabla) {
            $tabla = preg_replace('/[^a-zA-Z0-9_]/', '', $tabla); // Seguridad para evitar inyección SQL
            
            $sql_insert_extra = "INSERT INTO $tabla (cod, nombre, link, modulo, Num_nivel, estilos)
                                 VALUES (?,?, ?, ?, '1', ?)";
            
            $stmt_insert_extra = $conn->prepare($sql_insert_extra);
            $stmt_insert_extra->bind_param("sssss",$cod_parametro, $nombre, $link, $modulo, $estilos);
            $stmt_insert_extra->execute();
            $stmt_insert_extra->close();
        }

        echo "Registro actualizado correctamente en todas las tablas.";
    } else {
        echo "Error: No se encontró el ID o la nueva tabla.";
    }

    // Redirigir a secciones.php después de la actualización
    header("Location: ../secciones.php");
    exit();
}
?>