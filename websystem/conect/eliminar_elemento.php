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
$id_usuario = isset($_GET['id_user']) ? trim($_GET['id_user']) : '';
$seccion = isset($_GET['secc']) ? trim($_GET['secc']) : '';
$id_sub = isset($_GET['idSub']) ? trim($_GET['idSub']) : '';



eliminar_archivo_y_contador($archivo_a_borrar . '.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$raiz_proyecto = dirname(__DIR__, 2); // Ejemplo: C:\xampp\htdocs\hub

if (!empty($archivo_a_borrar)) {
    echo "🔍 Buscando registros en la BD...<br>";

    // Buscar y eliminar archivo y carpeta principal
    eliminarArchivoYCarpeta($archivo_a_borrar, $raiz_proyecto);

    // Si no se encontró en la raíz, intentar con seccion
    if (!empty($seccion)) {
        $ruta_desde_seccion = $raiz_proyecto . "/" . $seccion;
        echo "📁 Buscando en la ruta alternativa: $ruta_desde_seccion<br>";
        buscarYEliminarDesdeRuta($archivo_a_borrar, $ruta_desde_seccion);
    }
}

/**
 * Elimina archivo .php y carpeta si están en la raíz del proyecto
 */
function eliminarArchivoYCarpeta($nombre, $raiz) {
    $archivo = $raiz . "/" . $nombre . ".php";
    $carpeta = $raiz . "/" . $nombre;

    echo "🛠 Intentando eliminar en raíz: $archivo<br>";

    if (file_exists($archivo)) {
        unlink($archivo);
        echo "✅ Archivo eliminado: $archivo<br>";
    } else {
        echo "⚠️ Archivo no encontrado: $archivo<br>";
    }

    if (is_dir($carpeta)) {
        eliminarCarpetaRecursiva($carpeta);
    } else {
        echo "⚠️ Carpeta no encontrada: $carpeta<br>";
    }
}

/**
 * Busca y elimina archivo y carpeta dentro de una ruta específica
 */
function buscarYEliminarDesdeRuta($nombre, $ruta_base) {
    $archivo = $ruta_base . "/" . $nombre . ".php";
    $carpeta = $ruta_base . "/" . $nombre;

    echo "🛠 Buscando archivo en: $archivo<br>";
    echo "🛠 Buscando carpeta en: $carpeta<br>";

    if (file_exists($archivo)) {
        unlink($archivo);
        echo "✅ Archivo eliminado: $archivo<br>";
    } else {
        echo "⚠️ Archivo no encontrado: $archivo<br>";
    }

    if (is_dir($carpeta)) {
        eliminarCarpetaRecursiva($carpeta);
    } else {
        echo "⚠️ Carpeta no encontrada: $carpeta<br>";
    }
}

/**
 * Elimina carpeta y su contenido
 */
function eliminarCarpetaRecursiva($carpeta) {
    $archivos = array_diff(scandir($carpeta), ['.', '..']);

    foreach ($archivos as $archivo) {
        $ruta_completa = $carpeta . "/" . $archivo;
        if (is_dir($ruta_completa)) {
            eliminarCarpetaRecursiva($ruta_completa);
        } else {
            unlink($ruta_completa);
        }
    }

    if (rmdir($carpeta)) {
        echo "✅ Carpeta eliminada: $carpeta<br>";
    } else {
        echo "⚠️ No se pudo eliminar la carpeta: $carpeta<br>";
    }
}
// ✅ 🔥 Continúa con la eliminación en la base de datos si hay `cod` o `codtab`
$se_borro_cod_o_codtab = false;

// Contar cuántas secciones hay
$partes = array_filter(explode('/', $seccion));
$num_niveles = count($partes);

if ($num_niveles >= 2) {
    // ✅ Caso 1: Hay 2 niveles o más (ej: /pie/prueba) → solo se borra el actual
    $sql_delete = "DELETE FROM subnivel WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id_sub);
    if ($stmt->execute()) {
        echo "✅ Registro (nivel final) eliminado (ID: $id_sub)";
    } else {
        echo "❌ Error al eliminar: " . $stmt->error;
    }
    $stmt->close();

} elseif ($num_niveles === 1) {
    // ✅ Caso 2: solo un nivel → borrar este ID y todos los que tengan su nombre como parte de 'secciones'

    // Obtener el nombre del registro principal
    $nombre = null;
    $sql_nombre = "SELECT nombre FROM subnivel WHERE id = ?";
    $stmt_nombre = $conn->prepare($sql_nombre);
    if ($stmt_nombre) {
        $stmt_nombre->bind_param("i", $id_sub);
        $stmt_nombre->execute();
        $stmt_nombre->bind_result($nombre);
        $stmt_nombre->fetch();
        $stmt_nombre->close();
    }

    if ($nombre) {
        // Eliminar hijos: secciones que contienen /$nombre
        $like_secciones = "%/$nombre%";
        $sql_delete_hijos = "DELETE FROM subnivel WHERE secciones LIKE ?";
        $stmt_hijos = $conn->prepare($sql_delete_hijos);
        if ($stmt_hijos) {
            $stmt_hijos->bind_param("s", $like_secciones);
            $stmt_hijos->execute();
            echo "🧹 Se eliminaron hijos con secciones que contienen '/$nombre'<br>";
            $stmt_hijos->close();
        }

        // Eliminar el registro original
        $sql_delete_self = "DELETE FROM subnivel WHERE id = ?";
        $stmt_self = $conn->prepare($sql_delete_self);
        if ($stmt_self) {
            $stmt_self->bind_param("i", $id_sub);
            $stmt_self->execute();
            echo "✅ Registro principal eliminado (ID: $id_sub)";
            $stmt_self->close();
        }

    } else {
        echo "❌ No se encontró el nombre del ID: $id_sub.";
    }

} else {
    echo "❌ La ruta de secciones es inválida.";
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
$se_borro_user_id = false;

if (!empty($id_usuario)) {
    // Verificar si el usuario existe en la tabla `log`
    $sql_check_id = "SELECT id FROM log WHERE id = ?";
    $stmt_check_id = $conn->prepare($sql_check_id);
    $stmt_check_id->bind_param("s", $id_usuario);
    $stmt_check_id->execute();
    $result_check_id = $stmt_check_id->get_result();

    if ($result_check_id->num_rows > 0) {
        // Eliminar el usuario de la tabla `log`
        $sql_delete_id = "DELETE FROM log WHERE id = ?";
        $stmt_delete_id = $conn->prepare($sql_delete_id);
        $stmt_delete_id->bind_param("s", $id_usuario);
        if ($stmt_delete_id->execute()) {
            $se_borro_user_id = true;
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
}elseif($se_borro_user_id) {
    header("Location: ../administracion.php");
    exit();
} 
else {
    die("Error: No se encontraron registros para eliminar.");
}
?>
