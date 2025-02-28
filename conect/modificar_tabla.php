<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cod = $_POST["cod"]; // Identificador principal
    $nombre = $_POST["nombre"];
    $link = $_POST["link"];
    $modulo = $_POST["modulo"];
    $estilos = !empty($_POST["estilos"]) ? (is_array($_POST["estilos"]) ? implode(',', $_POST["estilos"]) : $_POST["estilos"]) : '';
    $publicar = isset($_POST["publicar"]) ? $_POST["publicar"] : [];

    if (empty($publicar)) {
        echo "Error: No se ha seleccionado ninguna tabla.";
        exit();
    }

    // Buscar todas las tablas que comienzan con 'menu_'
    $sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
    $result_tablas = $conn->query($sql_buscar_tablas);
    $tablas_existentes = [];
    $mantener_cod = []; // Tablas donde el código debe seguir existiendo

    if ($result_tablas) {
        while ($fila = $result_tablas->fetch_array()) {
            $tabla = $fila[0];

            // Verificar si el registro existe en la tabla
            $sql_check = "SELECT COUNT(*) FROM $tabla WHERE cod = ?";
            $stmt_check = $conn->prepare($sql_check);
            if ($stmt_check) {
                $stmt_check->bind_param("s", $cod);
                $stmt_check->execute();
                $stmt_check->bind_result($existe);
                $stmt_check->fetch();
                $stmt_check->close();

                if ($existe > 0) {
                    $tablas_existentes[] = $tabla; // Guardar tabla donde ya existe el registro
                    $mantener_cod[] = $tabla; // Agregar al control de eliminación
                }
            }
        }
    }

    // 1️⃣ Actualizar registros en TODAS las tablas donde ya existe
    foreach ($tablas_existentes as $tabla) {
        $sql_update = "UPDATE $tabla SET nombre = ?, link = ?, modulo = ?, estilos = ? WHERE cod = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update) {
            $stmt_update->bind_param("sssss", $nombre, $link, $modulo, $estilos, $cod);
            if ($stmt_update->execute()) {
                echo "Registro actualizado en $tabla <br>";
            } else {
                echo "Error al actualizar en $tabla: " . $stmt_update->error . "<br>";
            }
            $stmt_update->close();
        }
    }

    // 2️⃣ Eliminar registros SOLO si el `cod` ya no está en ninguna tabla seleccionada
    foreach ($tablas_existentes as $tabla) {
        if (!in_array($tabla, $publicar)) {
            // Verificamos si el `cod` aún debe existir en otra tabla antes de eliminarlo
            if (!array_intersect($mantener_cod, $publicar)) {
                $sql_delete = "DELETE FROM $tabla WHERE cod = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                if ($stmt_delete) {
                    $stmt_delete->bind_param("s", $cod);
                    $stmt_delete->execute();
                    $stmt_delete->close();
                    echo "Registro eliminado de $tabla porque ya no está en ninguna otra tabla <br>";
                }
            }
        }
    }

    // 3️⃣ Insertar en nuevas tablas sin borrar en otras
    foreach ($publicar as $tabla) {
        $tabla = preg_replace('/[^a-zA-Z0-9_]/', '', $tabla); // Seguridad contra SQL Injection

        if (!in_array($tabla, $tablas_existentes)) {
            $sql_insert = "INSERT INTO $tabla (cod, nombre, link, modulo, Num_nivel, estilos) 
                           VALUES (?, ?, ?, ?, '1', ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            if ($stmt_insert) {
                $stmt_insert->bind_param("sssss", $cod, $nombre, $link, $modulo, $estilos);
                if ($stmt_insert->execute()) {
                    echo "Registro insertado en $tabla <br>";
                } else {
                    echo "Error al insertar en $tabla: " . $stmt_insert->error . "<br>";
                }
                $stmt_insert->close();
            }
        }
    }

    echo "Proceso completado.";
    header("Location: ../secciones.php");
    exit();
}
?>
