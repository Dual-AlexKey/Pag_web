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
    $rutaArchivo = $directorio . '/' . $nombreLimpio . '.php'; // Archivo dentro de la carpeta

    // Crear la carpeta si no existe
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // Contenido del archivo
    $contenido = "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>$nombreLimpio</title>
</head>
<body>
    <h1>Bienvenido a la página de $nombreLimpio</h1>
    <p>Esta es una página creada automáticamente.</p>
</body>
</html>";

    // Crear el archivo dentro de la carpeta
    if (file_put_contents($rutaArchivo, $contenido) !== false) {
        echo "Página creada exitosamente en <a href='../$nombreLimpio/$nombreLimpio.php' target='_blank'>$nombreLimpio.php</a>";
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

    // 🔹 1️⃣ Renombrar carpeta y archivo
$directorioBase = __DIR__ . '/../../';  
$nombreLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '', $nameold); // Sanitiza el nombre viejo
$nombreNuevoLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '', $nombre); // Sanitiza el nombre nuevo

$directorioActual = $directorioBase . $nombreLimpio;
$directorioNuevo = $directorioBase . $nombreNuevoLimpio;

$archivoNombreViejo = $nombreLimpio . '.php';
$archivoNombreNuevo = $nombreNuevoLimpio . '.php';

$archivoActual = $directorioActual . '/' . $archivoNombreViejo;
$archivoNuevo = $directorioNuevo . '/' . $archivoNombreNuevo;

// 📌 Debugging - Verificar si las rutas son correctas
echo "Directorio Actual: $directorioActual <br>";
echo "Directorio Nuevo: $directorioNuevo <br>";
echo "Archivo Actual: $archivoActual <br>";
echo "Archivo Nuevo: $archivoNuevo <br>";

if (!is_dir($directorioActual)) {
    echo "⚠️ Error: La carpeta no existe. Verifica la ruta.";
} elseif (!file_exists($archivoActual)) {
    echo "⚠️ Error: El archivo no existe dentro de la carpeta.";
} else {
    // 🚀 Primero, renombramos la carpeta
    if (rename($directorioActual, $directorioNuevo)) {
        echo "✅ Carpeta renombrada con éxito.<br>";

        // 🔄 ACTUALIZAMOS la ruta del archivo después de renombrar la carpeta
        $archivoActual = $directorioNuevo . '/' . $archivoNombreViejo;

        if (file_exists($archivoActual)) {
            if (rename($archivoActual, $archivoNuevo)) {
                echo "✅ Archivo renombrado con éxito.<br>";
            } else {
                echo "❌ Error al renombrar el archivo. Verifica permisos.";
            }
        } else {
            echo "⚠️ Error: El archivo no se encontró después de renombrar la carpeta.";
        }
    } else {
        echo "❌ Error al renombrar la carpeta. Verifica permisos.";
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
        $rutaArchivo = $directorio . '/' . $nombreLimpio . '.php'; // Archivo dentro de la carpeta

        // Crear la carpeta si no existe
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        // Contenido del archivo
        $contenido = "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$nombreLimpio</title>
    </head>
    <body>
        <h1>Bienvenido a la página de $nombreLimpio</h1>
        <p>Esta es una página creada automáticamente.</p>
    </body>
    </html>";

        // Crear el archivo dentro de la carpeta
        if (file_put_contents($rutaArchivo, $contenido) !== false) {
            echo "Página creada exitosamente en <a href='../$nombreLimpio/$nombreLimpio.php' target='_blank'>$nombreLimpio.php</a>";
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
        $estructsecc = $_POST['estructsecc'] ?? '';
        $estructcont = $_POST['estructcont'] ?? '';
        $mostrar = isset($_POST['mostrar']) ? implode(',', $_POST['mostrar']) : null;
        $estilosubsec = $_POST['estilosubsec'] ?? '';
        $fondsecc = $_POST['fondsecc'] ?? '';
        $galeria = $_POST['galeria'] ?? '';
        $barrasubmenu = $_POST['barrasubmenu'] ?? '';
        $barramenu = $_POST['barramenu'] ?? '';
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
            $sql_update = "UPDATE detalles SET estructsecc=?, estructcont=?, mostrar=?, estilosubsec=?, fondsecc=?, galeria=?, barrasubmenu=?, barramenu=?, ordensecc=?, orden=?, ordencont=? 
                           WHERE cod=?";
            
            if ($stmt_update = $conn->prepare($sql_update)) {
                $stmt_update->bind_param("ssssssssssss", $estructsecc, $estructcont, $mostrar, $estilosubsec, $fondsecc, $galeria, $barrasubmenu, $barramenu, $ordensecc, $orden, $ordencont, $cod);
                if ($stmt_update->execute()) {
                    echo "✅ Página actualizada correctamente.";
                } else {
                    echo "❌ Error al actualizar: " . $stmt_update->error;
                }
                $stmt_update->close();
            }
        } else {
            // 🔹 Si `cod` no existe, insertar un nuevo registro
            $sql_insert = "INSERT INTO detalles (cod, estructsecc, estructcont, mostrar, estilosubsec, fondsecc, galeria, barrasubmenu, barramenu, ordensecc, orden, ordencont) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
            if ($stmt_insert = $conn->prepare($sql_insert)) {
                $stmt_insert->bind_param("ssssssssssss", $cod, $estructsecc, $estructcont, $mostrar, $estilosubsec, $fondsecc, $galeria, $barrasubmenu, $barramenu, $ordensecc, $orden, $ordencont);
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
        $imagen_link = $_POST['imagen_link'] ?? '';
        $transicion = $_POST['modulo'] ?? '';
        $altura = $_POST['altura'] ?? 0;
        $orden = $_POST['Orden'] ?? 0;

        // Convertir a enteros (seguridad)
        $altura = is_numeric($altura) ? intval($altura) : 0;
        $orden = is_numeric($orden) ? intval($orden) : 0;

        // Consulta SQL
        $sql_img = "INSERT INTO Imagenes (nombre, imagen_link, transicion, altura, orden) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql_img)) {
            $stmt->bind_param("sssii", $nombre, $imagen_link, $transicion, $altura, $orden);
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
} else {
    header("Location: ../tablero.php");
}
exit();
?>