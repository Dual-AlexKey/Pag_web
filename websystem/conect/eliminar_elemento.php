<?php
include 'conexion.php';
include '../../contador_visitas.php';

// ✅ Obtener valores desde la URL
$id_parametro = isset($_GET['id']) ? trim($_GET['id']) : '';
$id_tablero = isset($_GET['id_img']) ? trim($_GET['id_img']) : '';
$cod_parametro = isset($_GET['cod']) ? trim($_GET['cod']) : '';
$codtab_parametro = isset($_GET['codtab']) ? trim($_GET['codtab']) : '';
$archivo_a_borrar = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';
$nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : '';


eliminar_archivo_y_contador($archivo_a_borrar . '.php');

// ✅ Mostrar errores (para depuración)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$raiz_proyecto = dirname(__DIR__, 2); // 📌 Ruta base del proyecto

if (!empty($archivo_a_borrar)) {
    echo "<b>🔍 Buscando registros en la BD...</b><br>";

    $sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
    $result_tablas = $conn->query($sql_buscar_tablas);

    $rutas_a_borrar = [];
    $nombres_a_borrar = [];

    if ($result_tablas->num_rows > 0) {
        while ($fila = $result_tablas->fetch_array()) {
            $tabla = $fila[0];

            // ✅ Buscar registros en la BD
            $sql_buscar = "SELECT nombre, secciones FROM `$tabla` WHERE secciones LIKE ? OR nombre = ?";
            $stmt_buscar = $conn->prepare($sql_buscar);
            $param_busqueda = "%/$archivo_a_borrar%"; 
            $stmt_buscar->bind_param("ss", $param_busqueda, $archivo_a_borrar);
            $stmt_buscar->execute();
            $result_buscar = $stmt_buscar->get_result();

            if ($result_buscar->num_rows > 0) {
                while ($row = $result_buscar->fetch_assoc()) {
                    $nombre = trim($row['nombre']);
                    $secciones = isset($row['secciones']) ? trim($row['secciones'], "/") : "";

                    if (!empty($secciones)) {
                        // ✅ Dividir `secciones` en partes y agregarlas
                        $rutas_a_borrar = array_merge($rutas_a_borrar, explode("/", $secciones));
                    } 
                    
                    if (!empty($nombre)) {
                        // ✅ Incluir `nombre` en la lista de eliminaciones
                        $nombres_a_borrar[] = $nombre;
                    }
                }
            }

            // ✅ Eliminar registros en la BD
            $sql_delete = "DELETE FROM `$tabla` WHERE secciones LIKE ? OR nombre = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("ss", $param_busqueda, $archivo_a_borrar);
            $stmt_delete->execute();
            $stmt_delete->close();
        }
    } else {
        echo "⚠️ No se encontraron tablas que coincidan con 'menu_%'.<br>";
    }

    // ✅ Unir y eliminar duplicados
    $rutas_a_borrar = array_unique(array_merge($rutas_a_borrar, $nombres_a_borrar));

    // ✅ Eliminar archivos y carpetas individualmente
    foreach ($rutas_a_borrar as $ruta) {
        echo "📌 Eliminando registros y archivos para: <b>$ruta</b><br>";
        eliminarArchivoYCarpeta($ruta, $raiz_proyecto);
    }
}

/**
 * Función para eliminar archivo y carpeta si existen
 */
function eliminarArchivoYCarpeta($nombre, $raiz_proyecto) {
    $nombre_sanitizado = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombre);

    // ✅ Rutas absolutas
    $directorio = $raiz_proyecto . "/" . $nombre_sanitizado; 
    $ruta_archivo = $raiz_proyecto . "/" . $nombre_sanitizado . '.php';

    // ✅ Archivos protegidos que no deben eliminarse
    $archivos_protegidos = ['eliminar_elemento_php.php'];
    if (in_array($nombre_sanitizado . '.php', $archivos_protegidos)) {
        echo "❌ No se puede eliminar el archivo protegido: $ruta_archivo<br>";
        return;
    }

    echo "🛠 Buscando archivo: $ruta_archivo<br>";
    echo "🛠 Buscando carpeta: $directorio<br>";

    // 🔥 Eliminar archivo
    if (file_exists($ruta_archivo)) {
        if (unlink($ruta_archivo)) {
            echo "✅ Archivo eliminado: $ruta_archivo<br>";
        } else {
            echo "❌ No se pudo eliminar el archivo: $ruta_archivo<br>";
        }
    } else {
        echo "⚠️ Archivo no encontrado: $ruta_archivo<br>";
    }

    // 🗂 Eliminar carpeta y subdirectorios
    eliminarCarpetaRecursiva($directorio);
}

/**
 * Función para eliminar carpeta y todo su contenido
 */
function eliminarCarpetaRecursiva($carpeta) {
    if (!is_dir($carpeta)) {
        echo "⚠️ Carpeta no encontrada: $carpeta<br>";
        return;
    }

    $archivos = array_diff(scandir($carpeta), ['.', '..']);
    
    foreach ($archivos as $archivo) {
        $ruta_completa = $carpeta . "/" . $archivo;
        if (is_dir($ruta_completa)) {
            eliminarCarpetaRecursiva($ruta_completa); // 🔄 Eliminar subcarpeta
        } else {
            unlink($ruta_completa); // 🗑 Eliminar archivo
        }
    }

    if (rmdir($carpeta)) {
        echo "✅ Carpeta eliminada: $carpeta<br>";
    } else {
        echo "⚠️ No se pudo eliminar la carpeta: $carpeta (puede no estar vacía)<br>";
    }
}


if (!empty($nombre)) {
    $sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
    $result_tablas = $conn->query($sql_buscar_tablas);

    if ($result_tablas->num_rows > 0) {
        while ($fila = $result_tablas->fetch_array()) {
            $tabla = $fila[0];

            // Buscar registros donde 'secciones' comience con "/$nombre"
            $sql_buscar = "SELECT id FROM `$tabla` WHERE secciones LIKE ?";
            $stmt_buscar = $conn->prepare($sql_buscar);
            $param_busqueda = "/$nombre%";
            $stmt_buscar->bind_param("s", $param_busqueda);
            $stmt_buscar->execute();
            $result_buscar = $stmt_buscar->get_result();

            if ($result_buscar->num_rows > 0) {
                // Eliminar registros encontrados
                $sql_delete = "DELETE FROM `$tabla` WHERE secciones LIKE ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("s", $param_busqueda);
                $stmt_delete->execute();
                $stmt_delete->close();
            }
            $stmt_buscar->close();
        }
    }
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
$se_borro_id_tablero = false;

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
else if (!empty($id_tablero)) {
    $sql_check_id = "SELECT id_img FROM Imagenes WHERE id_img = ?";
    $stmt_check_id = $conn->prepare($sql_check_id);
    $stmt_check_id->bind_param("s", $id_tablero);
    $stmt_check_id->execute();
    $result_check_id = $stmt_check_id->get_result();

    if ($result_check_id->num_rows > 0) {
        $sql_delete_id = "DELETE FROM Imagenes WHERE id_img = ?";
        $stmt_delete_id = $conn->prepare($sql_delete_id);
        $stmt_delete_id->bind_param("s", $id_tablero);
        if ($stmt_delete_id->execute()) {
            $se_borro_id_tab = true;
        }
        $stmt_delete_id->close();
    }

    $stmt_check_id->close();
}

// ✅ 🔥 Redireccionar según el tipo de eliminación
if ($se_borro_id) {
    header("Location: ../tablero.php");
    exit();
} 
elseif ($se_borro_id_tab) {
    header("Location: ../new_img.php");
    exit();
}
elseif ($se_borro_cod_o_codtab) {
    header("Location: ../secciones.php");
    exit();
} else {
    die("Error: No se encontraron registros para eliminar.");
}
?>
