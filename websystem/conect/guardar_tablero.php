<?php
include 'conexion.php';          // 🔹 Se encuentra en la misma carpeta que `guardar.php`

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipoFormulario = $_POST['formulario_tipo'] ?? null;
    $sql = "";

    // 📌 Campos generales (aplican a todos los formularios)
    $nombre = $_POST['nombre'] ?? null; 
    $ubicacion = $_POST['ubicacion'] ?? null;
    $orden = $_POST['orden'] ?? null;
    $columnas = $_POST['columnas'] ?? null;
    $columnas_moviles = $_POST['columnas_moviles'] ?? null;
    $estilo = $_POST['estilo'] ?? null;
    $margen = isset($_POST['margen']) ? implode(',', $_POST['margen']) : null;
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_final = $_POST['fecha_final'] ?? null;
    $codigo = $_POST['codigo'] ?? null;

    $formu = $tipoFormulario; 

    // 📌 Obtener las tablas 'menu_%'
    $tablas_menu = [];
    $query = "SHOW TABLES LIKE 'menu_%'";
    $resultado_tablas = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_row($resultado_tablas)) {
        $tablas_menu[] = $row[0];
    }

    // 📌 Obtener los códigos únicos de las tablas
    $codigos = [];
    foreach ($tablas_menu as $tabla) {
        $query = "SELECT cod FROM $tabla";
        $resultado = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($resultado)) {
            if (!in_array($row['cod'], $codigos)) {
                $codigos[] = $row['cod'];
            }
        }
    }

    // 📌 Obtener los datos únicos por cada código
    $datos_unicos = [];
    foreach ($codigos as $cod) {
        foreach ($tablas_menu as $tabla) {
            $query = "SELECT cod, nombre FROM $tabla WHERE cod = '$cod' LIMIT 1";
            $resultado = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($resultado)) {
                if (!isset($datos_unicos[$cod])) {
                    $datos_unicos[$cod] = $row;
                    break;
                }
            }
        }
    }

    // 📌 Guardar los nombres de las tablas seleccionadas
    $tabla = null;
    if (!empty($_POST['seleccionados'])) {
        $tablaNombres = [];
        foreach ($_POST['seleccionados'] as $cod) {
            if (isset($datos_unicos[$cod])) {
                $tablaNombres[] = $datos_unicos[$cod]['nombre'];
            }
        }
        $tabla = implode(',', $tablaNombres);
    }
     // 📌 Manejo de imagen o enlace de imagen
     $imagen_link = $_POST['imagen_link'] ?? null; // ✅ Solo usamos `imagen_link`
     
        // 📌 Convertir `cod` seleccionados a `nombre` antes de guardar en `tabla`
        $tabla = null;
        if (!empty($_POST['seleccionados'])) {
            $tablaNombres = [];
            foreach ($_POST['seleccionados'] as $cod) {
                if (isset($datos_unicos[$cod])) {
                    $tablaNombres[] = $datos_unicos[$cod]['nombre'];
                }
            }
            $tabla = implode(',', $tablaNombres);
        }
        $id = isset($_POST['id']) && $_POST['id'] !== 'null' && is_numeric($_POST['id']) ? intval($_POST['id']) : null;
    
        // ✅ Verificar si ID existe en la BD antes de decidir `UPDATE` o `INSERT`
    $existe = false;
    if ($id !== null) {
        $query_check = "SELECT COUNT(*) as total FROM tablero WHERE id = $id";
        $resultado_check = $conn->query($query_check);
        $fila_check = $resultado_check->fetch_assoc();
        $existe = $fila_check['total'] > 0;

    }
    
    // 📌 Guardado según el tipo de formulario
    if ($tipoFormulario == "Imagen") {
        $link = $_POST['link'] ?? null;
        if ($existe) {
            // ✅ Si el `id` existe, actualizar el registro
            $sql = "UPDATE tablero SET 
                        nombre = '$nombre', link = '$link', imagen = '$imagen_link', 
                        tabla = '$tabla', ubicacion = '$ubicacion', orden = '$orden', 
                        columnas = '$columnas', columnas_moviles = '$columnas_moviles', 
                        estilo = '$estilo', margen = '$margen', 
                        fecha_inicio = '$fecha_inicio', fecha_final = '$fecha_final'
                    WHERE id = $id";
        } else {
            // ✅ Si el `id` NO existe, insertar un nuevo registro
            $sql = "INSERT INTO tablero (formu, nombre, link, imagen, tabla, ubicacion, orden, columnas, columnas_moviles, estilo, margen, fecha_inicio, fecha_final)
                    VALUES ('$tipoFormulario', '$nombre', '$link', '$imagen_link', '$tabla', '$ubicacion', '$orden', '$columnas', '$columnas_moviles', '$estilo', '$margen', '$fecha_inicio', '$fecha_final')";
        }
    }
    
    elseif ($tipoFormulario == "HTML") {
        if ($existe) {
            // ✅ Si el `id` existe, actualizar el registro
            $sql = "UPDATE tablero SET 
                        nombre = '$nombre', codigo = '$codigo',  
                        tabla = '$tabla', ubicacion = '$ubicacion', orden = '$orden', 
                        columnas = '$columnas', columnas_moviles = '$columnas_moviles', 
                        estilo = '$estilo', margen = '$margen', 
                        fecha_inicio = '$fecha_inicio', fecha_final = '$fecha_final'
                    WHERE id = $id";
        } else {
            // ✅ Si el `id` NO existe, insertar un nuevo registro
            $sql = "INSERT INTO tablero (formu, nombre, codigo, tabla, ubicacion, orden, columnas, columnas_moviles, estilo, margen, fecha_inicio, fecha_final)
                VALUES ('$formu', '$nombre', '$codigo', '$tabla', '$ubicacion', '$orden', '$columnas', '$columnas_moviles', '$estilo', '$margen', '$fecha_inicio', '$fecha_final')";
        }
    }

    elseif ($tipoFormulario == "Contenidos") {
        $modulo = $_POST['modulo'] ?? null;
        $seccion = $_POST['seccion'] ?? null;
        $categoria = $_POST['categoria'] ?? null;
        $nro_items = $_POST['nro_items'] ?? null;
        $items_visibles = $_POST['items_visibles'] ?? null;
        $ordennum = $_POST['ordennum'] ?? null;
        $estilocheck = $_POST['estilocheck'] ?? null;
        $mostrar = isset($_POST['mostrar']) ? implode(',', $_POST['mostrar']) : null;

        if ($existe) {
            // ✅ Si el `id` existe, actualizar el registro
            $sql = "UPDATE tablero SET 
                        nombre = '$nombre', modulo = '$modulo', seccion = '$seccion',
                        categoria = '$categoria', nro_items = '$nro_items', items_visibles = '$items_visibles',
                        ordennum = '$ordennum', estilocheck = '$estilocheck', mostrar = '$mostrar',
                        tabla = '$tabla', ubicacion = '$ubicacion', orden = '$orden', 
                        columnas = '$columnas', columnas_moviles = '$columnas_moviles', 
                        estilo = '$estilo', margen = '$margen', 
                        fecha_inicio = '$fecha_inicio', fecha_final = '$fecha_final'
                    WHERE id = $id";
        } else {
            // ✅ Si el `id` NO existe, insertar un nuevo registro
            $sql = "INSERT INTO tablero (formu, nombre, modulo, seccion, categoria, nro_items, items_visibles, ordennum, estilocheck, mostrar, tabla, ubicacion, orden, columnas, columnas_moviles, estilo, margen, fecha_inicio, fecha_final)
                VALUES ('$formu', '$nombre', '$modulo', '$seccion', '$categoria', '$nro_items', '$items_visibles', '$ordennum', '$estilocheck', '$mostrar', '$tabla', '$ubicacion', '$orden', '$columnas', '$columnas_moviles', '$estilo', '$margen', '$fecha_inicio', '$fecha_final')";
        }
    }

    elseif ($tipoFormulario == "Banner") {
        $altura = $_POST['altura'] ?? null;

        $sql = "INSERT INTO tablero (formu, nombre, altura, tabla, ubicacion, orden, columnas, columnas_moviles, estilo, margen, fecha_inicio, fecha_final)
                VALUES ('$formu', '$nombre', '$altura', '$tabla', '$ubicacion', '$orden', '$columnas', '$columnas_moviles', '$estilo', '$margen', '$fecha_inicio', '$fecha_final')";
    }

    elseif ($tipoFormulario == "Apps") {
        $apps = $_POST['apps'] ?? null;
        if ($existe) {
            // ✅ Si el `id` existe, actualizar el registro
            $sql = "UPDATE tablero SET 
                        nombre = '$nombre', apps = '$apps',  
                        tabla = '$tabla', ubicacion = '$ubicacion', orden = '$orden', 
                        columnas = '$columnas', columnas_moviles = '$columnas_moviles', 
                        estilo = '$estilo', margen = '$margen', 
                        fecha_inicio = '$fecha_inicio', fecha_final = '$fecha_final'
                    WHERE id = $id";
        } else {
            // ✅ Si el `id` NO existe, insertar un nuevo registro
            $sql = "INSERT INTO tablero (formu, nombre, apps, tabla, ubicacion, orden, columnas, columnas_moviles, estilo, margen, fecha_inicio, fecha_final)
                VALUES ('$formu', '$nombre', '$apps', '$tabla', '$ubicacion', '$orden', '$columnas', '$columnas_moviles', '$estilo', '$margen', '$fecha_inicio', '$fecha_final')";
        }
        
    }

    elseif ($tipoFormulario == "Ventana") {
        if ($existe) {
            // ✅ Si el `id` existe, actualizar el registro
            $sql = "UPDATE tablero SET 
                        nombre = '$nombre', codigo = '$codigo', imagen = '$imagen_link',
                        tabla = '$tabla', ubicacion = '$ubicacion', orden = '$orden', 
                        columnas = '$columnas', columnas_moviles = '$columnas_moviles', 
                        estilo = '$estilo', margen = '$margen', 
                        fecha_inicio = '$fecha_inicio', fecha_final = '$fecha_final'
                    WHERE id = $id";
        } else {
            // ✅ Si el `id` NO existe, insertar un nuevo registro
            $sql = "INSERT INTO tablero (formu, nombre, codigo, imagen, tabla, ubicacion, orden, columnas, columnas_moviles, estilo, margen, fecha_inicio, fecha_final)
                VALUES ('$formu', '$nombre', '$codigo', '$imagen_link', '$tabla', '$ubicacion', '$orden', '$columnas', '$columnas_moviles', '$estilo', '$margen', '$fecha_inicio', '$fecha_final')";
        }

    }

    elseif ($tipoFormulario == "Seccion") {
    $cod = $_POST["cod"] ?? null; // Puede venir vacío
    $codtab = null; // Se generará si hay múltiples tablas
    $nombre = $_POST["nombre"] ?? null;
    $link = $_POST["link"] ?? null;
    $modulo = $_POST["modulo"] ?? null;
    $estilos = !empty($_POST["estilos"]) ? (is_array($_POST["estilos"]) ? implode(',', $_POST["estilos"]) : $_POST["estilos"]) : null;
    $publicar = isset($_POST["publicar"]) ? $_POST["publicar"] : [];
    $sef_seccion = true;


    if (empty($publicar)) {
        echo "Error: No se ha seleccionado ninguna tabla.";
        exit();
    }

    // 🔍 **Buscar todas las tablas que comienzan con 'menu_'**
    $sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
    $result_tablas = $conn->query($sql_buscar_tablas);
    $tablas_existentes = [];
    $mantener_cod = [];

    if ($result_tablas) {
        while ($fila = $result_tablas->fetch_array()) {
            $tabla = $fila[0];

            // Verificar si el registro existe en la tabla
            $sql_check = "SELECT codtab FROM $tabla WHERE cod = ?";
            $stmt_check = $conn->prepare($sql_check);
            if ($stmt_check) {
                $stmt_check->bind_param("s", $cod);
                $stmt_check->execute();
                $stmt_check->bind_result($codtab_existente);
                $stmt_check->fetch();
                $stmt_check->close();

                if ($codtab_existente) {
                    $tablas_existentes[] = $tabla;
                    $mantener_cod[] = $tabla;
                    $codtab = $codtab_existente; // Usar el mismo codtab si ya existe
                }
            }
        }
    }

    // 🆕 **Generar `codtab` si se guarda en varias tablas y no tiene uno**
    if (!$codtab && count($publicar) > 1) {
        $tabla_base = reset($publicar);
        $prefijo = strtolower(substr($tabla_base, 5, 3)); // Extrae los 3 caracteres después de "menu_"

        // Buscar el mayor codtab en las tablas seleccionadas
        $max_cod = 0;
        foreach ($publicar as $tabla) {
            $sql_codigo = "SELECT MAX(CAST(SUBSTRING(codtab, 4) AS UNSIGNED)) AS max_cod FROM `$tabla` WHERE codtab LIKE '$prefijo%'";
            $result_codigo = $conn->query($sql_codigo);
            if ($result_codigo && $row = $result_codigo->fetch_assoc()) {
                $max_cod = max($max_cod, (int) $row["max_cod"]);
            }
        }

        // Generar nuevo codtab incrementado
        $nuevo_codigo = $max_cod + 1;
        $codtab = $prefijo . str_pad($nuevo_codigo, 2, "0", STR_PAD_LEFT); // Formato: xyz01
    }

    // 📝 **Actualizar registros en tablas existentes**
    foreach ($tablas_existentes as $tabla) {
        $sql_update = "UPDATE $tabla SET nombre = ?, link = ?, modulo = ?, estilos = ? WHERE cod = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update) {
            $stmt_update->bind_param("sssss", $nombre, $link, $modulo, $estilos, $cod);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }

    // 🗑️ **Eliminar registros de tablas no seleccionadas**
    foreach ($tablas_existentes as $tabla) {
        if (!in_array($tabla, $publicar)) {
            $sql_delete = "DELETE FROM $tabla WHERE cod = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            if ($stmt_delete) {
                $stmt_delete->bind_param("s", $cod);
                $stmt_delete->execute();
                $stmt_delete->close();
            }
        }
    }

    // ✅ **Insertar en nuevas tablas**
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

        // Validar que el nombre no esté vacío
    if (empty($nombre)) {
        die("Error: Nombre inválido.");
    }

    // Sanitizar el nombre del archivo y la carpeta (permitir solo letras, números, guiones y guiones bajos)
$nombreLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombre);

// Definir la carpeta y la ruta completa del archivo
$directorioBase = __DIR__ . '/../../';  
$directorio = $directorioBase . $nombreLimpio; // Carpeta con el nombre limpio
$rutaArchivo = $directorioBase . $nombreLimpio . '.php'; // Archivo al mismo nivel que la carpeta

// Crear la carpeta si no existe
if (!is_dir($directorio)) {
    mkdir($directorio, 0777, true);
}

$contenido = <<<PHP
<?php
include('estilos/header.php');
include __DIR__ . '/estilos/generar_design.php';
include ('contador_visitas.php');
// Obtener el nombre del archivo actual
\$nombreArchivo = basename(__FILE__, '.php');
\$contador = manejar_contador_por_pagina(\$nombreArchivo);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diseño Dinámico - <?php echo \$nombreArchivo; ?></title>
    <link rel="stylesheet" href="estilos/css/styles.css">
</head>
<body>
    <div class="layout">
        <?php generarDiseno(\$nombreArchivo); ?>
    </div>
</body>
</html>
PHP;

// Crear el archivo al mismo nivel que la carpeta
if (file_put_contents($rutaArchivo, $contenido) !== false) {
    echo "Página creada exitosamente en <a href='../$nombreLimpio.php' target='_blank'>$nombreLimpio.php</a>";
} else {
    echo "Error al crear el archivo.";
}

}
elseif ($tipoFormulario == "Editseccion") {
    
    $cod = $_POST["cod"];
    $codtab = isset($_POST["codtab"]) ? $_POST["codtab"] : null;
    $nombre = trim($_POST["nombre"]); // Se usará también para la carpeta
    $link = $_POST["link"];
    $modulo = $_POST["modulo"];
    $estilos = !empty($_POST["estilos"]) ? (is_array($_POST["estilos"]) ? implode(',', $_POST["estilos"]) : $_POST["estilos"]) : '';
    $publicar = isset($_POST["publicar"]) ? $_POST["publicar"] : [];
    $sef_seccion = true;
    $nameold = trim($_POST["nameold"]); // Nombre anterior de la carpeta
    
    if (empty($publicar)) {
        echo "Error: No se ha seleccionado ninguna tabla.";
        exit();
    }
    
    // 📌 Buscar todas las tablas que empiezan con "menu_"
    $sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
    $result_tablas = $conn->query($sql_buscar_tablas);
    $tablas_existentes = [];
    
    if ($result_tablas) {
        while ($fila = $result_tablas->fetch_array()) {
            $tabla = $fila[0];
            $tablas_existentes[] = $tabla;
        }
    }
    
    // 🔹 1️⃣ Eliminar registros de tablas donde ya no está seleccionado
    foreach ($tablas_existentes as $tabla) {
        // Si la tabla no está en `$publicar`, eliminar el registro de esa tabla
        if (!in_array($tabla, $publicar)) {
            $sql_delete = "DELETE FROM $tabla WHERE cod = ? OR codtab = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            if ($stmt_delete) {
                $stmt_delete->bind_param("ss", $cod, $codtab);
                $stmt_delete->execute();
                $stmt_delete->close();
            }
        }
    }
    
    // 🔹 2️⃣ Insertar o actualizar registros en las tablas seleccionadas
    foreach ($publicar as $tabla) {
        $tabla = preg_replace('/[^a-zA-Z0-9_]/', '', $tabla); // Limpiar nombre de la tabla por seguridad
    
        // Verificar si el registro ya existe en la tabla
        $sql_check = "SELECT COUNT(*) FROM $tabla WHERE cod = ? OR codtab = ?";
        $stmt_check = $conn->prepare($sql_check);
        if ($stmt_check) {
            $stmt_check->bind_param("ss", $cod, $codtab);
            $stmt_check->execute();
            $stmt_check->bind_result($existe);
            $stmt_check->fetch();
            $stmt_check->close();
    
            if ($existe > 0) {
                // 🔹 Actualizar si ya existe
                $sql_update = "UPDATE $tabla SET nombre = ?, link = ?, modulo = ?, estilos = ? WHERE cod = ? OR codtab = ?";
                $stmt_update = $conn->prepare($sql_update);
                if ($stmt_update) {
                    $stmt_update->bind_param("ssssss", $nombre, $link, $modulo, $estilos, $cod, $codtab);
                    $stmt_update->execute();
                    $stmt_update->close();
                }
            } else {
                // 🔹 Insertar si no existe
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
    }

// 🔹 1️⃣ Configuración de Rutas
$directorioBase = __DIR__ . '/../../';  
$nameOldLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '', $nameold); // Sanitiza el nombre viejo
$nameNuevoLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '', $nombre); // Sanitiza el nombre nuevo

$directorioActual = $directorioBase . $nameOldLimpio; // Carpeta vieja
$directorioNuevo = $directorioBase . $nameNuevoLimpio; // Carpeta nueva

$archivoViejo = $directorioBase . $nameOldLimpio . '.php'; // Archivo viejo
$archivoNuevo = $directorioBase . $nameNuevoLimpio . '.php'; // Archivo nuevo

// 📌 Debugging - Verificar si las rutas son correctas
echo "Directorio Actual: $directorioActual <br>";
echo "Directorio Nuevo: $directorioNuevo <br>";
echo "Archivo Viejo: $archivoViejo <br>";
echo "Archivo Nuevo: $archivoNuevo <br>";

    // 🔍 Validar existencia de la carpeta y archivo
if (!is_dir($directorioActual)) {
        echo "⚠️ Error: La carpeta no existe. Verifica la ruta.<br>";
    } elseif (!file_exists($archivoViejo)) {
        echo "⚠️ Error: El archivo no existe. Verifica la ruta.<br>";
    } else {
        // 🚀 Renombrar Carpeta
        if (rename($directorioActual, $directorioNuevo)) {
            echo "✅ Carpeta renombrada con éxito.<br>";

            // 🚀 Renombrar Archivo
            if (rename($archivoViejo, $archivoNuevo)) {
                echo "✅ Archivo renombrado con éxito.<br>";
            } else {
                echo "❌ Error al renombrar el archivo. Verifica permisos.<br>";
            }
        } else {
            echo "❌ Error al renombrar la carpeta. Verifica permisos.<br>";
        }
    }

    }
    elseif ($tipoFormulario == "Subseccion") {
        $cod = $_POST["cod"] ?? null; // Puede venir vacío
        $codtab = null; // Se generará si hay múltiples tablas
        $nombre = $_POST["nombre"] ?? null;
        $link = $_POST["link"] ?? null;
        $modulo = $_POST["modulo"] ?? null;
        $estilos = !empty($_POST["estilos"]) ? (is_array($_POST["estilos"]) ? implode(',', $_POST["estilos"]) : $_POST["estilos"]) : null;
        $publicar = isset($_POST["publicar"]) ? $_POST["publicar"] : [];
        $secciones = $_POST["secciones"] ?? null;
        $sef_seccion = true;

    
        if (empty($publicar)) {
            echo "Error: No se ha seleccionado ninguna tabla.";
            exit();
        }
    
        // 🔍 **Buscar todas las tablas que comienzan con 'menu_'**
        $sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
        $result_tablas = $conn->query($sql_buscar_tablas);
        $tablas_existentes = [];
        $mantener_cod = [];
    
        if ($result_tablas) {
            while ($fila = $result_tablas->fetch_array()) {
                $tabla = $fila[0];
    
                // Verificar si el registro existe en la tabla
                $sql_check = "SELECT codtab FROM $tabla WHERE cod = ?";
                $stmt_check = $conn->prepare($sql_check);
                if ($stmt_check) {
                    $stmt_check->bind_param("s", $cod);
                    $stmt_check->execute();
                    $stmt_check->bind_result($codtab_existente);
                    $stmt_check->fetch();
                    $stmt_check->close();
    
                    if ($codtab_existente) {
                        $tablas_existentes[] = $tabla;
                        $mantener_cod[] = $tabla;
                        $codtab = $codtab_existente; // Usar el mismo codtab si ya existe
                    }
                }
            }
        }
    
        // 🆕 **Generar `codtab` si se guarda en varias tablas y no tiene uno**
        if (!$codtab && count($publicar) > 1) {
            $tabla_base = reset($publicar);
            $prefijo = strtolower(substr($tabla_base, 5, 3)); // Extrae los 3 caracteres después de "menu_"
    
            // Buscar el mayor codtab en las tablas seleccionadas
            $max_cod = 0;
            foreach ($publicar as $tabla) {
                $sql_codigo = "SELECT MAX(CAST(SUBSTRING(codtab, 4) AS UNSIGNED)) AS max_cod FROM `$tabla` WHERE codtab LIKE '$prefijo%'";
                $result_codigo = $conn->query($sql_codigo);
                if ($result_codigo && $row = $result_codigo->fetch_assoc()) {
                    $max_cod = max($max_cod, (int) $row["max_cod"]);
                }
            }
    
            // Generar nuevo codtab incrementado
            $nuevo_codigo = $max_cod + 1;
            $codtab = $prefijo . str_pad($nuevo_codigo, 2, "0", STR_PAD_LEFT); // Formato: xyz01
        }
    
        // 📝 **Actualizar registros en tablas existentes**
        foreach ($tablas_existentes as $tabla) {
            $sql_update = "UPDATE $tabla SET nombre = ?, link = ?, modulo = ?, estilos = ? WHERE cod = ?";
            $stmt_update = $conn->prepare($sql_update);
            if ($stmt_update) {
                $stmt_update->bind_param("sssss", $nombre, $link, $modulo, $estilos, $cod);
                $stmt_update->execute();
                $stmt_update->close();
            }
        }
    
        // 🗑️ **Eliminar registros de tablas no seleccionadas**
        foreach ($tablas_existentes as $tabla) {
            if (!in_array($tabla, $publicar)) {
                $sql_delete = "DELETE FROM $tabla WHERE cod = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                if ($stmt_delete) {
                    $stmt_delete->bind_param("s", $cod);
                    $stmt_delete->execute();
                    $stmt_delete->close();
                }
            }
        }
    
        // ✅ **Insertar en nuevas tablas**
        foreach ($publicar as $tabla) {
            $tabla = preg_replace('/[^a-zA-Z0-9_]/', '', $tabla);
    
            if (!in_array($tabla, $tablas_existentes)) {
                if ($codtab) {
                    $sql_insert = "INSERT INTO $tabla (cod, codtab, nombre, link, modulo, Num_nivel, estilos,secciones) 
                                   VALUES (?, ?, ?, ?, ?, '2', ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    if ($stmt_insert) {
                        $stmt_insert->bind_param("sssssss", $cod, $codtab, $nombre, $link, $modulo, $estilos, $secciones);
                        $stmt_insert->execute();
                        $stmt_insert->close();
                    }
                } else {
                    $sql_insert = "INSERT INTO $tabla (cod, nombre, link, modulo, Num_nivel, estilos,secciones) 
                                   VALUES (?, ?, ?, ?, '2', ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    if ($stmt_insert) {
                        $stmt_insert->bind_param("ssssss", $cod, $nombre, $link, $modulo, $estilos, $secciones);
                        $stmt_insert->execute();
                        $stmt_insert->close();
                    }
                }
            }
        }

            // Validar que el nombre no esté vacío
        if (empty($nombre)) {
            die("Error: Nombre inválido.");
        }

        // Sanitizar el nombre del archivo y la carpeta (permitir solo letras, números, guiones y guiones bajos)
        $nombreLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombre);

// Definir la carpeta y la ruta completa del archivo
$directorioBase = __DIR__ . '/../../';  
$directorio = $directorioBase . $nombreLimpio; // Carpeta con el nombre limpio
$rutaArchivo = $directorioBase . $nombreLimpio . '.php'; // Archivo al mismo nivel que la carpeta

// Crear la carpeta si no existe
if (!is_dir($directorio)) {
    mkdir($directorio, 0777, true);
}

// Contenido del archivo
$contenido = <<<PHP
<?php
include('estilos/header.php');
include __DIR__ . '/estilos/generar_design.php';
include ('contador_visitas.php');
// Obtener el nombre del archivo actual
\$nombreArchivo = basename(__FILE__, '.php');
\$contador = manejar_contador_por_pagina(\$nombreArchivo);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diseño Dinámico - <?php echo \$nombreArchivo; ?></title>
    <link rel="stylesheet" href="estilos/css/styles.css">
</head>
<body>
    <div class="layout">
        <?php generarDiseno(\$nombreArchivo); ?>
    </div>
</body>
</html>
PHP;

// Crear el archivo al mismo nivel que la carpeta
if (file_put_contents($rutaArchivo, $contenido) !== false) {
    echo "Página creada exitosamente en <a href='../$nombreLimpio.php' target='_blank'>$nombreLimpio.php</a>";
} else {
    echo "Error al crear el archivo.";
}

    }
    elseif ($tipoFormulario == "SeccionPag") {
        $titulo = $_POST['nombreT'] ?? '';
        $contenido = $_POST['contenido'] ?? '';
        $tituloS = $_POST['nombreS'] ?? '';
        $descripcion = $_POST['descrip'] ?? '';
        $cod = $_POST['cod'] ?? '';
        $metatags = $_POST['meta'] ?? '';
        $imagen_referencia = $_POST['imagen_link2'] ?? '';
        $imagen_social = $_POST['imagen_link3'] ?? '';
        $sef_seccion = true;

    
        // 🔹 Verificar si `cod` ya existe en la base de datos
        $sql_check = "SELECT cod FROM paginas WHERE cod = ?";
        if ($stmt_check = $conn->prepare($sql_check)) {
            $stmt_check->bind_param("s", $cod);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            $exists = $result->num_rows > 0; // 🔹 Si hay resultados, `cod` existe
            $stmt_check->close();
        }
    
        if ($exists) {
            // 🔹 Si `cod` ya existe, actualizar los datos
            $sql_update = "UPDATE paginas SET titulo=?, contenido=?, tituloS=?, descripcion=?, metatags=?, imagen_referencia=?, imagen_social=? 
                           WHERE cod=?";
            
            if ($stmt_update = $conn->prepare($sql_update)) {
                $stmt_update->bind_param("ssssssss", $titulo, $contenido, $tituloS, $descripcion, $metatags, $imagen_referencia, $imagen_social, $cod);
                if ($stmt_update->execute()) {
                    echo "✅ Página actualizada correctamente.";
                } else {
                    echo "❌ Error al actualizar: " . $stmt_update->error;
                }
                $stmt_update->close();
            }
        } else {
            // 🔹 Si `cod` no existe, insertar un nuevo registro
            $sql_insert = "INSERT INTO paginas (titulo, contenido, tituloS, descripcion, cod, metatags, imagen_referencia, imagen_social) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
            if ($stmt_insert = $conn->prepare($sql_insert)) {
                $stmt_insert->bind_param("ssssssss", $titulo, $contenido, $tituloS, $descripcion, $cod, $metatags, $imagen_referencia, $imagen_social);
                if ($stmt_insert->execute()) {
                    echo "✅ Nueva página guardada correctamente.";
                } else {
                    echo "❌ Error al guardar: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            }
        }
    }
    elseif ($tipoFormulario == "SeccionPar") {
        $cod = $_POST['cod'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $estructsecc = $_POST['estructsecc'] ?? '';
        $mostrar = isset($_POST['mostrar']) ? implode(',', $_POST['mostrar']) : null;
        $estilosubsec = $_POST['estilosubsec'] ?? '';
        $fondsecc = $_POST['fondsecc'] ?? '';
        $galeria = $_POST['galeria'] ?? '';
        $barrasubmenu = $_POST['barrasubmenu'] ?? '';
        $ordensecc = $_POST['ordensecc'] ?? '';
        $orden = $_POST['orden'] ?? '';
        $ordencont = $_POST['ordencont'] ?? '';
        $sef_seccion = true;


    
        // 🔹 Verificar si `cod` ya existe en la base de datos
        $sql_check = "SELECT cod FROM detalles WHERE cod = ?";
        if ($stmt_check = $conn->prepare($sql_check)) {
            $stmt_check->bind_param("s", $cod);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            $exists = $result->num_rows > 0; // 🔹 Si hay resultados, `cod` existe
            $stmt_check->close();
        }
    
        if ($exists) {
            // 🔹 Si `cod` ya existe, actualizar los datos
            $sql_update = "UPDATE detalles SET estructsecc=?, nombre=?, mostrar=?, estilosubsec=?, fondsecc=?, galeria=?, barrasubmenu=?, ordensecc=?, orden=?, ordencont=? 
                           WHERE cod=?";
            
            if ($stmt_update = $conn->prepare($sql_update)) {
                $stmt_update->bind_param("sssssssssss", $estructsecc, $nombre, $mostrar, $estilosubsec, $fondsecc, $galeria, $barrasubmenu, $ordensecc, $orden, $ordencont, $cod);
                if ($stmt_update->execute()) {
                    echo "✅ Página actualizada correctamente.";
                } else {
                    echo "❌ Error al actualizar: " . $stmt_update->error;
                }
                $stmt_update->close();
            }
        } else {
            // 🔹 Si `cod` no existe, insertar un nuevo registro
            $sql_insert = "INSERT INTO detalles (cod, nombre, estructsecc, mostrar, estilosubsec, fondsecc, galeria, barrasubmenu, ordensecc, orden, ordencont) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
            if ($stmt_insert = $conn->prepare($sql_insert)) {
                $stmt_insert->bind_param("sssssssssss", $cod, $nombre, $estructsecc, $mostrar, $estilosubsec, $fondsecc, $galeria, $barrasubmenu, $ordensecc, $orden, $ordencont);
                if ($stmt_insert->execute()) {
                    echo "✅ Nueva página guardada correctamente.";
                } else {
                    echo "❌ Error al guardar: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            }
        }
    }
    elseif ($tipoFormulario == "Imagenes_Tablero") {
        $nombre = $_POST['nombre'] ?? '';
        $imagen_1 = $_POST['imagen_1'] ?? '';
        $transicion = $_POST['transicion'] ?? '';
        $altura = $_POST['altura'] ?? 0;
        $orden = $_POST['orden'] ?? 0;
        $img = true;

        // Convertir a enteros (seguridad)
        $altura = is_numeric($altura) ? intval($altura) : 0;
        $orden = is_numeric($orden) ? intval($orden) : 0;

        // Consulta SQL
        $sql_img = "INSERT INTO Imagenes (nombre, imagen_1, transicion, altura, orden) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql_img)) {
            $stmt->bind_param("sssii", $nombre, $imagen_1, $transicion, $altura, $orden);
            if ($stmt->execute()) {
                echo "✅ Imagen guardada correctamente.";
            } else {
                echo "❌ Error al guardar: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "❌ Error en la consulta: " . $conn->error;
        }
    } 
    elseif ($tipoFormulario == "ElementoImg") {
     // Recibir los datos del formulario
        $padre = $_POST['nombre'] ?? NULL;
        $titulo = $_POST['titulo'] ?? '';
        $tipo = $_POST['tipo'] ?? '';
        $imagen_2 = $_POST['imagen_link2'] ?? '';
        $link = $_POST['link'] ?? '';
        $PosX = (float) ($_POST['PosX'] ?? 0);
        $PosY = (float) ($_POST['PosY'] ?? 0);
        $estilo = $_POST['estilo'] ?? '';
        $orden_2 = (int) ($_POST['orden_2'] ?? 0);

        // Verificar si el registro ya existe (por ejemplo, usando el campo `titulo`)
        $sql_check = "SELECT id_img FROM Imagenes2 WHERE titulo = ?";
        if ($stmt_check = $conn->prepare($sql_check)) {
            $stmt_check->bind_param("s", $titulo);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            $exists = $result->num_rows > 0; // Si hay resultados, el título ya existe
            $stmt_check->close();
        }

        // Si el registro existe, actualizar
        if ($exists) {
            $sql_update = "UPDATE Imagenes2 SET tipo=?, imagen_2=?, link=?, PosX=?, PosY=?, estilo=?, orden_2=? WHERE titulo=?";
            
            if ($stmt_update = $conn->prepare($sql_update)) {
                $stmt_update->bind_param("ssssddis", $tipo, $imagen_2, $link, $PosX, $PosY, $estilo, $orden_2, $titulo);
                if ($stmt_update->execute()) {
                    echo "✅ Registro actualizado correctamente.";
                } else {
                    echo "❌ Error al actualizar: " . $stmt_update->error;
                }
                $stmt_update->close();
            }
        } else {
            // Si no existe, insertar un nuevo registro
            $sql_insert = "INSERT INTO Imagenes2 (padre, titulo, tipo, imagen_2, link, PosX, PosY, estilo, orden_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            if ($stmt_insert = $conn->prepare($sql_insert)) {
                $stmt_insert->bind_param("sssssddsi", $padre, $titulo, $tipo, $imagen_2, $link, $PosX, $PosY, $estilo, $orden_2);
                if ($stmt_insert->execute()) {
                    echo "✅ Nuevo registro guardado correctamente.";
                } else {
                    echo "❌ Error al guardar: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            }
        }
    }
    elseif ($tipoFormulario == "Webconfig") {
        // Recibir datos del formulario
        $url_pagina = $_POST['url_pagina'];
        $nombre = $_POST['nombre'];
        $idioma = $_POST['idioma'];
        $logo = $_POST['logo'];
        $favicon = $_POST['favicon'];
        $seo_titulo = $_POST['seo_titulo'];
        $seo_descripcion = $_POST['seo_descripcion'];
        $seo_metatags = $_POST['seo_metatags'];
        $pie_pagina = $_POST['pie_pagina'];
        $imgcabe = $_POST['imgcabe'];   
        $cabfondo = $_POST['cabfondo'];   
        $piefondo = $_POST['piefondo'];   
        $empresa = $_POST['empresa'];
        $ruc = $_POST['ruc'];
        $descripcion = $_POST['descripcion'];
        $pais = $_POST['pais'];
        $dpto = $_POST['dpto'];
        $city = $_POST['city'];
        $direccion_principal = $_POST['direccion_principal'];
        $email_contactos = $_POST['email_contactos'];
        $email_ventas = $_POST['email_ventas'];
        $telefono_fijo = $_POST['telefono_fijo'];
        $telefono_movil = $_POST['telefono_movil'];
        $moneda = $_POST['moneda']; 
        $precios = $_POST['precios'];
        $carrito_compras = $_POST['carrito_compras'];
        $zona_usuarios = $_POST['zona_usuarios'];
        $terminos_condiciones = $_POST['terminos_condiciones'];
        $panel_post = true;

        // Verificar si ya existe una empresa en la base de datos
        $sql_check = "SELECT COUNT(*) AS total FROM Empresa";
        $result = $conn->query($sql_check);
        $row = $result->fetch_assoc();

        if ($row['total'] > 0) {
            // Si ya existe una empresa, actualizar sus datos
            $sql_update = "UPDATE Empresa SET 
                url_pagina='$url_pagina', nombre='$nombre', idioma='$idioma', logo='$logo', favicon='$favicon', 
                seo_titulo='$seo_titulo', seo_descripcion='$seo_descripcion', seo_metatags='$seo_metatags', 
                empresa='$empresa', pie_pagina='$pie_pagina', imgcabe='$imgcabe', cabfondo='$cabfondo', piefondo='$piefondo', 
                ruc='$ruc', descripcion='$descripcion', pais='$pais', dpto='$dpto', city='$city', 
                direccion_principal='$direccion_principal', email_contactos='$email_contactos', 
                email_ventas='$email_ventas', telefono_fijo='$telefono_fijo', telefono_movil='$telefono_movil', 
                moneda='$moneda', precios='$precios', carrito_compras='$carrito_compras', 
                zona_usuarios='$zona_usuarios', terminos_condiciones='$terminos_condiciones'";

            if ($conn->query($sql_update) === TRUE) {
                echo "Configuración actualizada con éxito.";
            } else {
                echo "Error al actualizar: " . $conn->error;
            }
        } else {
            // Si no hay ninguna empresa, insertar una nueva
            $sql_insert = "INSERT INTO Empresa (url_pagina, nombre, idioma, logo, favicon, seo_titulo, 
                seo_descripcion, seo_metatags, empresa, pie_pagina, imgcabe, cabfondo, piefondo, ruc, descripcion, pais, dpto, 
                city, direccion_principal, email_contactos, email_ventas, telefono_fijo, 
                telefono_movil, moneda, precios, carrito_compras, zona_usuarios, terminos_condiciones) 
                VALUES ('$url_pagina', '$nombre', '$idioma', '$logo', '$favicon', '$seo_titulo', 
                '$seo_descripcion', '$seo_metatags', '$empresa','$pie_pagina', '$imgcabe', '$cabfondo', '$piefondo', '$ruc', '$descripcion', '$pais', 
                '$dpto', '$city', '$direccion_principal', '$email_contactos', 
                '$email_ventas', '$telefono_fijo', '$telefono_movil', '$moneda', '$precios', 
                '$carrito_compras', '$zona_usuarios', '$terminos_condiciones')";

            if ($conn->query($sql_insert) === TRUE) {
                echo "Configuración guardada con éxito.";
            } else {
                echo "Error al insertar: " . $conn->error;
            }
        }
    } 
    
   
    // 📌 Ejecutar la consulta
    if ($sql != "") {
        if ($conn->query($sql) === TRUE) {
            echo "Datos guardados correctamente. Formulario enviado: " . $formu;
        } else {
            echo "Error al guardar en la base de datos: " . $conn->error;
        }
    }

    // 📌 Cerrar la conexión solo si está definida
    if (isset($conn)) {
        $conn->close();
    }
} else {
    echo "Acceso no permitido.";
}
if ($sef_seccion) {
    header("Location: ../secciones.php");
} 
elseif($panel_post) {
    header("Location: ../panel.php");
}
elseif($img) {
    header("Location: ../new_img.php");
}
else {
    header("Location: ../tablero.php");
}
exit();
?>