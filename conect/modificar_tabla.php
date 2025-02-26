<?php
include 'conexion.php';

// Habilitar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['idcontrol']) ? intval($_POST['idcontrol']) : 0;
    $tabla = isset($_POST['tabla']) ? trim($_POST['tabla']) : '';
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $modulo = isset($_POST['modulo']) ? trim($_POST['modulo']) : '';
    $estilos = isset($_POST['estilos']) ? trim($_POST['estilos']) : '';
    $link = isset($_POST['link']) ? trim($_POST['link']) : '';
    $menusSeleccionados = isset($_POST['publicar']) ? $_POST['publicar'] : [];

    if ($id > 0 && !empty($tabla) && !empty($nombre) && !empty($modulo) && !empty($estilos)) {
        $conn->begin_transaction();

        // ✅ 1. Actualizar la tabla principal
        $sql_update = "UPDATE `$tabla` SET nombre = ?, modulo = ?, estilos = ?, link = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        if (!$stmt) die("Error en la preparación: " . $conn->error);
        $stmt->bind_param("ssssi", $nombre, $modulo, $estilos, $link, $id);
        if (!$stmt->execute()) die("Error al actualizar: " . $stmt->error);
        $stmt->close();

        // ✅ 2. Obtener las tablas "menu_..." actuales
        $sql_menus = "SHOW TABLES LIKE 'menu_%'";
        $result_menus = $conn->query($sql_menus);
        $tablas_menu = [];
        while ($row = $result_menus->fetch_array()) {
            $tablas_menu[] = $row[0];
        }

        if (empty($tablas_menu)) {
            echo "<script>alert('No hay tablas de menú disponibles.'); window.history.back();</script>";
            exit();
        }

        // ✅ 3. Eliminar entradas de las tablas donde ya no debería estar
        foreach ($tablas_menu as $menu_tabla) {
            if (!in_array($menu_tabla, $menusSeleccionados)) {
                $sql_delete = "DELETE FROM `$menu_tabla` WHERE cod = ?";
                $stmt = $conn->prepare($sql_delete);
                if (!$stmt) die("Error al preparar eliminación: " . $conn->error);
                $stmt->bind_param("s", $id);
                if (!$stmt->execute()) die("Error al eliminar: " . $stmt->error);
                $stmt->close();
            }
        }

        // ✅ 4. Insertar o actualizar en los menús seleccionados
        foreach ($menusSeleccionados as $menu_tabla) {
            if (in_array($menu_tabla, $tablas_menu)) {
                $sql_check = "SELECT COUNT(*) FROM `$menu_tabla` WHERE cod = ?";
                $stmt = $conn->prepare($sql_check);
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $stmt->bind_result($existe);
                $stmt->fetch();
                $stmt->close();

                if ($existe > 0) {
                    $sql_update_menu = "UPDATE `$menu_tabla` SET nombre = ?, modulo = ?, estilos = ?, link = ? WHERE cod = ?";
                    $stmt = $conn->prepare($sql_update_menu);
                    $stmt->bind_param("sssss", $nombre, $modulo, $estilos, $link, $id);
                } else {
                    $sql_insert = "INSERT INTO `$menu_tabla` (cod, nombre, modulo, estilos, link) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql_insert);
                    $stmt->bind_param("sssss", $id, $nombre, $modulo, $estilos, $link);
                }
                if (!$stmt->execute()) die("Error en la operación de menú: " . $stmt->error);
                $stmt->close();
            }
        }

        // ✅ 5. Crear el trigger dinámico si no existe
        foreach ($menusSeleccionados as $nombre_tabla) {
            $conn->query("DROP TRIGGER IF EXISTS `before_insert_cod_$nombre_tabla`");
            preg_match('/menu_([a-zA-Z0-9]+)_/', $nombre_tabla, $matches);
            $inicial = isset($matches[1]) ? substr($matches[1], 0, 1) : 'X';
            $trigger_sql = "CREATE TRIGGER `before_insert_cod_$nombre_tabla`\n            BEFORE INSERT ON `$nombre_tabla`\n            FOR EACH ROW BEGIN\n                DECLARE max_cod INT;\n                SELECT IFNULL(MAX(SUBSTRING(cod, 2)), 0) + 1 INTO max_cod FROM `$nombre_tabla` WHERE cod REGEXP '^[a-zA-Z]\\d+$';\n                IF NEW.cod IS NULL OR NEW.cod = '' THEN\n                    SET NEW.cod = CONCAT('$inicial', max_cod);\n                END IF;\n            END;";
            if (!$conn->query($trigger_sql)) {
                die("Error al crear el trigger: " . $conn->error);
            }
        }

        $conn->commit();
        header("Location: ../secciones.php?msg=success");
        exit();
    } else {
        echo "<script>alert('Error: Datos inválidos.'); window.history.back();</script>";
        exit();
    }
} else {
    echo "Método no permitido.";
}
$conn->close();
?>
