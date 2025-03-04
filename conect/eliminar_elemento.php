<?php
include 'conexion.php';

// ✅ Obtener valores desde la URL
$cod_parametro = isset($_GET['cod']) ? trim($_GET['cod']) : '';
$codtab_parametro = isset($_GET['codtab']) ? trim($_GET['codtab']) : '';

if (empty($cod_parametro)) {
    die("Error: No se proporcionó un código válido.");
}

// ✅ Buscar todas las tablas que comienzan con "menu_"
$sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
$result_tablas = $conn->query($sql_buscar_tablas);

if ($result_tablas->num_rows == 0) {
    die("Error: No se encontraron tablas en la base de datos.");
}

// ✅ Filtrar las tablas que contienen el `cod` o `codtab`
$nombres_tablas = [];
$codtab_encontrados = [];

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

// Si no hay registros con `cod` o `codtab`, mostrar error
if (empty($nombres_tablas)) {
    die("Error: No se encontraron registros con este código.");
}

// ✅ Si hay `codtab`, eliminar primero por `codtab`
if (!empty($codtab_parametro)) {
    foreach ($nombres_tablas as $tabla => $registros) {
        $sql_delete = "DELETE FROM `$tabla` WHERE codtab = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("s", $codtab_parametro);
        $stmt_delete->execute();
        $stmt_delete->close();
    }
}

// ✅ Luego eliminar los registros restantes con `cod`
foreach ($nombres_tablas as $tabla => $registros) {
    $sql_delete = "DELETE FROM `$tabla` WHERE cod = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("s", $cod_parametro);
    $stmt_delete->execute();
    $stmt_delete->close();
}

// Confirmar eliminación
echo "Registros eliminados correctamente de las tablas: " . implode(", ", array_keys($nombres_tablas));

// Cerrar conexión
$conn->close();

// Redirigir a la página principal
header("Location: ../secciones.php");
exit();
?>
