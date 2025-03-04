<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cod = $_POST["cod"];
    $codtab = isset($_POST["codtab"]) ? $_POST["codtab"] : null;
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
    $mantener_cod = [];

    if ($result_tablas) {
        while ($fila = $result_tablas->fetch_array()) {
            $tabla = $fila[0];

            // Verificar si el registro existe en la tabla
            $sql_check = "SELECT COUNT(*) FROM $tabla WHERE cod = ? OR codtab = ?";
            $stmt_check = $conn->prepare($sql_check);
            if ($stmt_check) {
                $stmt_check->bind_param("ss", $cod, $codtab);
                $stmt_check->execute();
                $stmt_check->bind_result($existe);
                $stmt_check->fetch();
                $stmt_check->close();

                if ($existe > 0) {
                    $tablas_existentes[] = $tabla;
                    $mantener_cod[] = $tabla;
                }
            }
        }
    }

    // 1️⃣ Actualizar registros en las tablas existentes
    foreach ($tablas_existentes as $tabla) {
        $sql_update = "UPDATE $tabla SET nombre = ?, link = ?, modulo = ?, estilos = ? WHERE cod = ? OR codtab = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update) {
            $stmt_update->bind_param("ssssss", $nombre, $link, $modulo, $estilos, $cod, $codtab);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }

    // 2️⃣ Eliminar registros si ya no están en ninguna tabla seleccionada
    foreach ($tablas_existentes as $tabla) {
        if (!in_array($tabla, $publicar) && !array_intersect($mantener_cod, $publicar)) {
            $sql_delete = "DELETE FROM $tabla WHERE cod = ? OR codtab = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            if ($stmt_delete) {
                $stmt_delete->bind_param("ss", $cod, $codtab);
                $stmt_delete->execute();
                $stmt_delete->close();
            }
        }
    }

    // 3️⃣ Insertar en nuevas tablas
    foreach ($publicar as $tabla) {
        $tabla = preg_replace('/[^a-zA-Z0-9_]/', '', $tabla);

        if (!in_array($tabla, $tablas_existentes)) {
            if ($codtab) {
                $sql_insert = "INSERT INTO $tabla (cod, codtab, nombre, link, modulo, Num_nivel, estilos) 
                               VALUES (?, ?, ?, ?, ?, '1', ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                if ($stmt_insert) {
                    $stmt_insert->bind_param("ssssss", $cod, $codtab, $nombre, $link, $modulo, $estilos);
                    $stmt_insert->execute();
                    $stmt_insert->close();
                }
            } else {
                $sql_insert = "INSERT INTO $tabla (cod, nombre, link, modulo, Num_nivel, estilos) 
                               VALUES (?, ?, ?, ?, '1', ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                if ($stmt_insert) {
                    $stmt_insert->bind_param("sssss", $cod, $nombre, $link, $modulo, $estilos);
                    $stmt_insert->execute();
                    $stmt_insert->close();
                }
            }
        }
    }

    echo "Proceso completado.";
    header("Location: ../secciones.php");
    exit();
}
?>
