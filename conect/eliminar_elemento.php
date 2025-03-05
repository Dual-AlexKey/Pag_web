<?php
include 'conexion.php';

// ✅ Obtener valores desde la URL
$id_parametro = isset($_GET['id']) ? trim($_GET['id']) : '';
$cod_parametro = isset($_GET['cod']) ? trim($_GET['cod']) : '';
$codtab_parametro = isset($_GET['codtab']) ? trim($_GET['codtab']) : '';

// ✅ Validar que al menos un parámetro esté presente
if (empty($id_parametro) && empty($cod_parametro) && empty($codtab_parametro)) {
    die("Error: No se proporcionaron parámetros válidos para eliminar.");
}

// ✅ Buscar todas las tablas que comienzan con "menu_" (solo para `cod` y `codtab`)
$sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
$result_tablas = $conn->query($sql_buscar_tablas);

if ($result_tablas->num_rows == 0) {
    die("Error: No se encontraron tablas en la base de datos.");
}

// ✅ Buscar registros en `menu_%` por `cod` y `codtab`
$nombres_tablas = [];
$codtab_encontrados = [];
$se_borro_cod_o_codtab = false; // Nueva variable para saber si se eliminó por cod o codtab

while ($fila = $result_tablas->fetch_array()) {
    $tabla = $fila[0];

    // Buscar registros con `cod` o `codtab`
    $sql_check = "SELECT cod, codtab FROM `$tabla` WHERE cod = ? OR codtab = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $cod_parametro, $codtab_parametro);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    while ($row_check = $result_check->fetch_assoc()) {
        $nombres_tablas[$tabla][] = $row_check;
        if (!empty($row_check['codtab'])) {
            $codtab_encontrados[] = $row_check['codtab']; // Guardamos los valores de codtab encontrados
        }
    }
    $stmt_check->close();
}

// ✅ Si hay `codtab`, eliminar primero por `codtab`
if (!empty($codtab_parametro)) {
    foreach ($nombres_tablas as $tabla => $registros) {
        $sql_delete = "DELETE FROM `$tabla` WHERE codtab = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("s", $codtab_parametro);
        if ($stmt_delete->execute()) {
            $se_borro_cod_o_codtab = true;
        }
        $stmt_delete->close();
    }
}

// ✅ Luego eliminar los registros restantes con `cod`
if (!empty($cod_parametro)) {
    foreach ($nombres_tablas as $tabla => $registros) {
        $sql_delete = "DELETE FROM `$tabla` WHERE cod = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("s", $cod_parametro);
        if ($stmt_delete->execute()) {
            $se_borro_cod_o_codtab = true;
        }
        $stmt_delete->close();
    }
}

// ✅ Si se proporcionó `id`, buscar y eliminar solo en la tabla `tablero`
$se_borro_id = false;

if (!empty($id_parametro)) {
    $sql_check_id = "SELECT id FROM tablero WHERE id = ?";
    $stmt_check_id = $conn->prepare($sql_check_id);
    $stmt_check_id->bind_param("s", $id_parametro);
    $stmt_check_id->execute();
    $result_check_id = $stmt_check_id->get_result();

    if ($result_check_id->num_rows > 0) {
        $sql_delete_id = "DELETE FROM tablero WHERE id = ?";
        $stmt_delete_id = $conn->prepare($sql_delete_id);
        $stmt_delete_id->bind_param("s", $id_parametro);
        if ($stmt_delete_id->execute()) {
            $se_borro_id = true;
        }
        $stmt_delete_id->close();
    }

    $stmt_check_id->close();
}

// ✅ Redireccionar según el tipo de eliminación
if ($se_borro_id) {
    header("Location: ../tablero.php");
} elseif ($se_borro_cod_o_codtab) {
    header("Location: ../secciones.php");
} else {
    die("Error: No se encontraron registros para eliminar.");
}
exit();
?>
