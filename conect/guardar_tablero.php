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

    elseif ($tipoFormulario == "Contenidos") {
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

    elseif ($tipoFormulario == "Seccion") {
        $cod = $_POST["cod"];
        $codtab = isset($_POST["codtab"]) ? $_POST["codtab"] : null;
        $nombre = $_POST["nombre"];
        $link = $_POST["link"];
        $modulo = $_POST["modulo"];
        $estilos = !empty($_POST["estilos"]) ? (is_array($_POST["estilos"]) ? implode(',', $_POST["estilos"]) : $_POST["estilos"]) : '';
        $publicar = isset($_POST["publicar"]) ? $_POST["publicar"] : [];
        $sef_seccion = true;

    
        if (empty($publicar)) {
            echo "Error: No se ha seleccionado ninguna tabla.";
            exit();
        }
    
        // Buscar todas las tablas que comienzan con 'menu_'
        $sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
        $result_tablas = $conn->query($sql_buscar_tablas);
        $tablas_existentes = [];
        $mantener_cod = [];
    
        if ($result_tablas) {
            while ($fila = $result_tablas->fetch_array()) {
                $tabla = $fila[0];
    
                // Verificar si el registro existe en la tabla
                $sql_check = "SELECT COUNT(*) FROM $tabla WHERE cod = ? OR codtab = ?";
                $stmt_check = $conn->prepare($sql_check);
                if ($stmt_check) {
                    $stmt_check->bind_param("ss", $cod, $codtab);
                    $stmt_check->execute();
                    $stmt_check->bind_result($existe);
                    $stmt_check->fetch();
                    $stmt_check->close();
    
                    if ($existe > 0) {
                        $tablas_existentes[] = $tabla;
                        $mantener_cod[] = $tabla;
                    }
                }
            }
        }
    
        // 1️⃣ Actualizar registros en las tablas existentes
        foreach ($tablas_existentes as $tabla) {
            $sql_update = "UPDATE $tabla SET nombre = ?, link = ?, modulo = ?, estilos = ? WHERE cod = ? OR codtab = ?";
            $stmt_update = $conn->prepare($sql_update);
            if ($stmt_update) {
                $stmt_update->bind_param("ssssss", $nombre, $link, $modulo, $estilos, $cod, $codtab);
                $stmt_update->execute();
                $stmt_update->close();
            }
        }
    
        // 2️⃣ Eliminar registros si ya no están en ninguna tabla seleccionada
        foreach ($tablas_existentes as $tabla) {
            if (!in_array($tabla, $publicar) && !array_intersect($mantener_cod, $publicar)) {
                $sql_delete = "DELETE FROM $tabla WHERE cod = ? OR codtab = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                if ($stmt_delete) {
                    $stmt_delete->bind_param("ss", $cod, $codtab);
                    $stmt_delete->execute();
                    $stmt_delete->close();
                }
            }
        }
    
        // 3️⃣ Insertar en nuevas tablas
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
    }
    elseif ($tipoFormulario == "Editseccion") {
        
        $cod = $_POST["cod"];
        $codtab = isset($_POST["codtab"]) ? $_POST["codtab"] : null;
        $nombre = $_POST["nombre"];
        $link = $_POST["link"];
        $modulo = $_POST["modulo"];
        $estilos = !empty($_POST["estilos"]) ? (is_array($_POST["estilos"]) ? implode(',', $_POST["estilos"]) : $_POST["estilos"]) : '';
        $publicar = isset($_POST["publicar"]) ? $_POST["publicar"] : [];
        $sef_seccion = true;

        if (empty($publicar)) {
            echo "Error: No se ha seleccionado ninguna tabla.";
            exit();
        }

        // Buscar todas las tablas que comienzan con 'menu_'
        $sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
        $result_tablas = $conn->query($sql_buscar_tablas);
        $tablas_existentes = [];
        $mantener_cod = [];

        if ($result_tablas) {
            while ($fila = $result_tablas->fetch_array()) {
                $tabla = $fila[0];

                // Verificar si el registro existe en la tabla
                $sql_check = "SELECT COUNT(*) FROM $tabla WHERE cod = ? OR codtab = ?";
                $stmt_check = $conn->prepare($sql_check);
                if ($stmt_check) {
                    $stmt_check->bind_param("ss", $cod, $codtab);
                    $stmt_check->execute();
                    $stmt_check->bind_result($existe);
                    $stmt_check->fetch();
                    $stmt_check->close();

                    if ($existe > 0) {
                        $tablas_existentes[] = $tabla;
                        $mantener_cod[] = $tabla;
                    }
                }
            }
        }

        // 1️⃣ Actualizar registros en las tablas existentes
        foreach ($tablas_existentes as $tabla) {
            $sql_update = "UPDATE $tabla SET nombre = ?, link = ?, modulo = ?, estilos = ? WHERE cod = ? OR codtab = ?";
            $stmt_update = $conn->prepare($sql_update);
            if ($stmt_update) {
                $stmt_update->bind_param("ssssss", $nombre, $link, $modulo, $estilos, $cod, $codtab);
                $stmt_update->execute();
                $stmt_update->close();
            }
        }

        // 2️⃣ Eliminar registros si ya no están en ninguna tabla seleccionada
        foreach ($tablas_existentes as $tabla) {
            if (!in_array($tabla, $publicar) && !array_intersect($mantener_cod, $publicar)) {
                $sql_delete = "DELETE FROM $tabla WHERE cod = ? OR codtab = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                if ($stmt_delete) {
                    $stmt_delete->bind_param("ss", $cod, $codtab);
                    $stmt_delete->execute();
                    $stmt_delete->close();
                }
            }
        }

        // 3️⃣ Insertar en nuevas tablas
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
