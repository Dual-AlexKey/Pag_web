<?php
include 'conexion.php';
include '../../contador_visitas.php';


// ✅ Obtener valores desde la URL
$id_parametro = isset($_GET['id']) ? trim($_GET['id']) : '';
$cod_parametro = isset($_GET['cod']) ? trim($_GET['cod']) : '';
$codtab_parametro = isset($_GET['codtab']) ? trim($_GET['codtab']) : '';
$archivo_a_borrar = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';


eliminar_archivo_y_contador($archivo_a_borrar . '.php');

// ✅ Mostrar errores (para depuración)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Obtener la raíz del proyecto dinámicamente
$raiz_proyecto = dirname(__DIR__, 2); // 📌 Subimos dos niveles desde "websystem/conect/"

// ✅ Validar que el nombre del archivo a borrar no esté vacío
if (!empty($archivo_a_borrar)) {
    // ✅ Sanitizar el nombre del archivo (permitir solo letras, números, guiones y guiones bajos)
    $nombre_sanitizado = preg_replace('/[^a-zA-Z0-9_-]/', '_', $archivo_a_borrar);

    // ✅ Rutas: carpeta y archivo
    $raiz_proyecto = __DIR__ . '/../../'; // Cambia esto a la ruta base de tu proyecto
    $directorio = $raiz_proyecto . $nombre_sanitizado; // Carpeta correspondiente
    $ruta_archivo = $raiz_proyecto . $nombre_sanitizado . '.php'; // Archivo al mismo nivel que la carpeta

    // ✅ Archivos protegidos que no deben eliminarse
    $archivos_protegidos = ['eliminar_elemento_php.php'];
    if (in_array($nombre_sanitizado . '.php', $archivos_protegidos)) {
        die("❌ Error: No puedes eliminar este archivo.");
    }

    // ✅ Verificar si el archivo existe
    if (file_exists($ruta_archivo)) {
        // 🔥 Eliminar el archivo
        if (unlink($ruta_archivo)) {
            echo "✅ Archivo eliminado correctamente: $nombre_sanitizado.php<br>";

            // 🧹 Verificar si la carpeta está vacía y eliminarla
            if (is_dir($directorio) && count(scandir($directorio)) == 2) { // Solo "." y ".."
                if (rmdir($directorio)) {
                    echo "✅ Carpeta eliminada correctamente: $nombre_sanitizado<br>";
                } else {
                    echo "⚠️ No se pudo eliminar la carpeta: $directorio<br>";
                }
            } else {
                echo "⚠️ La carpeta no está vacía o no existe: $directorio<br>";
            }
        } else {
            echo "❌ Error al eliminar el archivo: $ruta_archivo<br>";
        }
    } else {
        echo "⚠️ El archivo no existe: $ruta_archivo<br>";
    }
} else {
    echo "❌ Error: Nombre del archivo a borrar no especificado.<br>";
}

// ✅ 🔥 Continúa con la eliminación en la base de datos si hay `cod` o `codtab`
$se_borro_cod_o_codtab = false;

if (!empty($cod_parametro) || !empty($codtab_parametro)) {
    $sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
    $result_tablas = $conn->query($sql_buscar_tablas);

    if ($result_tablas->num_rows > 0) {
        while ($fila = $result_tablas->fetch_array()) {
            $tabla = $fila[0];

            // ✅ Eliminar por `codtab`
            if (!empty($codtab_parametro)) {
                $sql_delete = "DELETE FROM `$tabla` WHERE codtab = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("s", $codtab_parametro);
                if ($stmt_delete->execute()) {
                    $se_borro_cod_o_codtab = true;
                }
                $stmt_delete->close();
            }

            // ✅ Eliminar por `cod`
            if (!empty($cod_parametro)) {
                $sql_delete = "DELETE FROM `$tabla` WHERE cod = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("s", $cod_parametro);
                if ($stmt_delete->execute()) {
                    $se_borro_cod_o_codtab = true;
                }
                $stmt_delete->close();
            }
        }
    }
}

if (!empty($cod_parametro)){
    $sql_delete = "DELETE FROM detalles WHERE cod = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("s", $cod_parametro);
    if ($stmt_delete->execute()) {
        $se_borro_cod_o_codtab = true;
    }
    $stmt_delete->close();
}
if (!empty($cod_parametro)){
    $sql_delete = "DELETE FROM paginas WHERE cod = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("s", $cod_parametro);
    if ($stmt_delete->execute()) {
        $se_borro_cod_o_codtab = true;
    }
    $stmt_delete->close();
}

// ✅ 🔥 Si se proporcionó `id`, eliminar en la tabla `tablero`
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

// ✅ 🔥 Redireccionar según el tipo de eliminación
if ($se_borro_id) {
    header("Location: ../tablero.php");
    exit();
} elseif ($se_borro_cod_o_codtab) {
    header("Location: ../secciones.php");
    exit();
} else {
    die("Error: No se encontraron registros para eliminar.");
}
?>
