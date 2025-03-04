<?php
include 'conexion.php';          // 🔹 Se encuentra en la misma carpeta que `guardar.php`
include '../img/subir_imagen.php'; // 🔹 `subir_imagen.php` está fuera de `/conect` en `/subir/`

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipoFormulario = $_POST['formulario_tipo'] ?? null;
    $sql = "";

    // 📌 Campos generales (aplican a todos los formularios)
    $nombre = $_POST['nombre'] ?? null; 
    $ubicacion = $_POST['ubicacion'] ?? null;
    $orden = $_POST['Orden'] ?? null;
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
     $imagen_link = $_POST['imagen_link'] ?? null;
     $rutaImagen = null;
 
     if (!empty($imagen_link)) {
         // 🔹 Si se ingresó un enlace, se guarda el link
         $rutaImagen = $imagen_link;
     } else {
         // 🔹 Si no hay link, intenta subir una imagen local
         $rutaImagen = subirImagen($_FILES['imagen'] ?? null);
         if ($rutaImagen) {
             $rutaImagen = str_replace("C:/xampp/htdocs/hub/", "", $rutaImagen);
         }
     }

    // 📌 Guardado según el tipo de formulario
    if ($tipoFormulario == "Imagen") {
        $link = $_POST['link'] ?? null;

        $sql = "INSERT INTO tablero (formu, nombre, link, imagen, tabla, ubicacion, orden, columnas, columnas_moviles, estilo, margen, fecha_inicio, fecha_final)
                VALUES ('$formu', '$nombre', '$link', '$rutaImagen', '$tabla', '$ubicacion', '$orden', '$columnas', '$columnas_moviles', '$estilo', '$margen', '$fecha_inicio', '$fecha_final')";
    } 
    
    elseif ($tipoFormulario == "HTML") {

        $sql = "INSERT INTO tablero (formu, nombre, codigo, tabla, ubicacion, orden, columnas, columnas_moviles, estilo, margen, fecha_inicio, fecha_final)
                VALUES ('$formu', '$nombre', '$codigo', '$tabla', '$ubicacion', '$orden', '$columnas', '$columnas_moviles', '$estilo', '$margen', '$fecha_inicio', '$fecha_final')";
    }

    elseif ($tipoFormulario == "Contenido") {
        $modulo = $_POST['modulo'] ?? null;
        $seccion = $_POST['seccion'] ?? null;
        $categoria = $_POST['categoria'] ?? null;
        $nro_items = $_POST['nro_items'] ?? null;
        $items_visibles = $_POST['items_visibles'] ?? null;
        $ordennum = $_POST['ordennum'] ?? null;
        $estilocheck = isset($_POST['estilocheck']) ? 1 : 0;
        $mostrar = isset($_POST['mostrar']) ? 1 : 0;

        $sql = "INSERT INTO tablero (formu, nombre, modulo, seccion, categoria, nro_items, items_visibles, ordennum, estilocheck, mostrar, tabla, ubicacion, orden, columnas, columnas_moviles, estilo, margen, fecha_inicio, fecha_final)
                VALUES ('$formu', '$nombre', '$modulo', '$seccion', '$categoria', '$nro_items', '$items_visibles', '$ordennum', '$estilocheck', '$mostrar', '$tabla', '$ubicacion', '$orden', '$columnas', '$columnas_moviles', '$estilo', '$margen', '$fecha_inicio', '$fecha_final')";
    }

    elseif ($tipoFormulario == "Banner") {
        $altura = $_POST['altura'] ?? null;

        $sql = "INSERT INTO tablero (formu, nombre, altura, tabla, ubicacion, orden, columnas, columnas_moviles, estilo, margen, fecha_inicio, fecha_final)
                VALUES ('$formu', '$nombre', '$altura', '$tabla', '$ubicacion', '$orden', '$columnas', '$columnas_moviles', '$estilo', '$margen', '$fecha_inicio', '$fecha_final')";
    }

    elseif ($tipoFormulario == "Apps") {
        $apps = $_POST['apps'] ?? null;

        $sql = "INSERT INTO tablero (formu, nombre, apps, tabla, ubicacion, orden, columnas, columnas_moviles, estilo, margen, fecha_inicio, fecha_final)
                VALUES ('$formu', '$nombre', '$apps', '$tabla', '$ubicacion', '$orden', '$columnas', '$columnas_moviles', '$estilo', '$margen', '$fecha_inicio', '$fecha_final')";
    }

    elseif ($tipoFormulario == "Ventana") {

        $sql = "INSERT INTO tablero (formu, nombre, codigo, imagen, tabla, ubicacion, orden, columnas, columnas_moviles, estilo, margen, fecha_inicio, fecha_final)
                VALUES ('$formu', '$nombre', '$codigo', '$rutaImagen', '$tabla', '$ubicacion', '$orden', '$columnas', '$columnas_moviles', '$estilo', '$margen', '$fecha_inicio', '$fecha_final')";
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
header("Location: ../tablero.php");
exit;
?>
