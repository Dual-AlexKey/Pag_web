<?php
include 'conexion.php';

// ✅ Obtener valores desde la URL
$id_parametro = isset($_GET['id']) ? trim($_GET['id']) : '';
$cod_parametro = isset($_GET['cod']) ? trim($_GET['cod']) : '';
$codtab_parametro = isset($_GET['codtab']) ? trim($_GET['codtab']) : '';

// Mostrar errores (para depuración)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Obtener parámetros
$archivo_a_borrar = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';

// ✅ Validar que se haya proporcionado un nombre
if (empty($archivo_a_borrar)) {
    die("Error: No se proporcionó un archivo para eliminar.");
}

// ✅ Obtener la raíz del proyecto dinámicamente
$raiz_proyecto = dirname(__DIR__, 2); // 📌 Subimos dos niveles desde "websystem/conect/"

// ✅ Sanitizar el nombre del archivo (quitar caracteres peligrosos)
$nombre_sanitizado = preg_replace('/[^a-zA-Z0-9_-]/', '_', $archivo_a_borrar);

// ✅ Construir ruta del archivo y de la carpeta que lo contiene
$directorio = $raiz_proyecto . '/' . $nombre_sanitizado;
$ruta_archivo = $directorio . '/' . $nombre_sanitizado . '.php';

// 🔍 Mostrar la ruta exacta para depuración (puedes quitar esto luego)
echo "Buscando archivo en: $ruta_archivo<br>";

// ✅ Evitar eliminar archivos críticos
$archivos_protegidos = ['eliminar_elemento_php.php'];
if (in_array($nombre_sanitizado . '.php', $archivos_protegidos)) {
    die("Error: No puedes eliminar este archivo.");
}

// ✅ Verificar si el archivo existe antes de borrarlo
if (file_exists($ruta_archivo)) {
    if (unlink($ruta_archivo)) {
        echo "✅ Archivo eliminado correctamente: $nombre_sanitizado.php<br>";

        // ✅ Verificar si la carpeta está vacía y eliminarla
        if (is_dir($directorio) && count(scandir($directorio)) == 2) {
            if (rmdir($directorio)) {
                echo "✅ Carpeta eliminada correctamente: $nombre_sanitizado<br>";
                $se_borro_cod_o_codtab = false; 
            } else {
                echo "⚠️ No se pudo eliminar la carpeta.";
            }
        }
    } else {
        echo "❌ Error al eliminar el archivo.";
    }
} else {
    echo "⚠️ El archivo no existe en: $ruta_archivo";
}
// ✅ Continúa con la eliminación en la base de datos
$sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
$result_tablas = $conn->query($sql_buscar_tablas);

if ($result_tablas->num_rows == 0) {
    die("Error: No se encontraron tablas en la base de datos.");
}

// ✅ Buscar registros en `menu_%` por `cod` y `codtab`
$nombres_tablas = [];
$codtab_encontrados = [];
$se_borro_cod_o_codtab = false; 

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
            $codtab_encontrados[] = $row_check['codtab']; 
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
