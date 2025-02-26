<?php
include 'conexion.php';

// ✅ Obtener el código desde la URL
$cod_parametro = isset($_GET['cod']) ? trim($_GET['cod']) : '';
if (empty($cod_parametro)) {
    die("Error: No se proporcionó un código válido.");
}

// ✅ Buscar todas las tablas que comienzan con "menu_"
$sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
$result_tablas = $conn->query($sql_buscar_tablas);

if ($result_tablas->num_rows == 0) {
    die("Error: No se encontraron tablas en la base de datos.");
}

// ✅ Filtrar las tablas que contienen el `cod`
$nombres_tablas = [];
while ($fila = $result_tablas->fetch_array()) {
    $tabla = $fila[0];

    // Verificar si la tabla contiene el `cod`
    $sql_check = "SELECT COUNT(*) as count FROM `$tabla` WHERE cod = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $cod_parametro);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();

    if ($row_check['count'] > 0) {
        $nombres_tablas[] = $tabla;
    }
    $stmt_check->close();
}

// Si ninguna tabla tiene el código, mostrar error
if (empty($nombres_tablas)) {
    die("Error: No se encontraron registros con este código.");
}

// ✅ Eliminar registros de cada tabla
foreach ($nombres_tablas as $tabla) {
    $sql_delete = "DELETE FROM `$tabla` WHERE cod = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("s", $cod_parametro);
    $stmt_delete->execute();
    $stmt_delete->close();
}

// Confirmar eliminación
echo "Registros eliminados correctamente de las tablas: " . implode(", ", $nombres_tablas);

// Cerrar conexión
$conn->close();

header("Location: ../secciones.php");
    exit();
?>