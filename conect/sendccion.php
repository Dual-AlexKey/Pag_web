<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = $_POST["nombre"];
    $link = $_POST["link"];
    $modulo = $_POST["modulo"];
    $estilos = !empty($_POST["estilos"]) ? $_POST["estilos"] : null;
    $publicar = isset($_POST["publicar"]) ? $_POST["publicar"] : [];

    if (empty($publicar)) {
        echo "Error: No se ha seleccionado ninguna tabla.";
        exit();
    }

    // 🔍 **Buscar todas las tablas que comienzan con 'menu_'**
    $sql_buscar_tablas = "SHOW TABLES LIKE 'menu_%'";
    $result_tablas = $conn->query($sql_buscar_tablas);
    $tablas_existentes = [];

    if ($result_tablas) {
        while ($fila = $result_tablas->fetch_array()) {
            $tablas_existentes[] = $fila[0];
        }
    }

    // 🚀 **Filtrar solo las tablas que existen en la BD**
    $tablas_validas = array_intersect($publicar, $tablas_existentes);

    if (empty($tablas_validas)) {
        echo "Error: Ninguna de las tablas seleccionadas existe.";
        exit();
    }

    // 🔢 **Si hay más de una tabla, generar código basado en la última inserción en TODAS las tablas con ese prefijo**
    $codigo_generado = null;
    if (count($tablas_validas) > 1) {
        // Tomar la primera tabla válida y obtener su prefijo en minúsculas
        $tabla_base = reset($tablas_validas);
        $prefijo = strtolower(substr($tabla_base, 5, 3)); // Extrae los 3 caracteres después de "menu_" y los pone en minúsculas

        // 🔍 Buscar el mayor código en todas las tablas que inicien con ese prefijo
        $max_cod = 0;
        foreach ($tablas_validas as $tabla) {
            $sql_codigo = "SELECT MAX(CAST(SUBSTRING(codtab, 4) AS UNSIGNED)) AS max_cod FROM `$tabla` WHERE codtab LIKE '$prefijo%'";
            $result_codigo = $conn->query($sql_codigo);
            if ($result_codigo && $row = $result_codigo->fetch_assoc()) {
                $max_cod = max($max_cod, (int) $row["max_cod"]); // Obtener el número más alto encontrado
            }
        }

        // Generar el nuevo código incrementando el más alto encontrado
        $nuevo_codigo = $max_cod + 1;
        $codigo_generado = $prefijo . str_pad($nuevo_codigo, 2, "0", STR_PAD_LEFT); // Formato: xyz001
    }

    // 🔄 **Insertar en las tablas seleccionadas**
    foreach ($tablas_validas as $tabla) {
        if ($codigo_generado) {
            $sql = "INSERT INTO `$tabla` (codtab, nombre, link, modulo, Num_nivel, estilos) VALUES (?, ?, ?, ?, '1', ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sssss", $codigo_generado, $nombre, $link, $modulo, $estilos);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Error en la consulta: " . $conn->error;
            }
        } else {
            // Si solo hay una tabla, no se envía código
            $sql = "INSERT INTO `$tabla` (nombre, link, modulo, Num_nivel, estilos) VALUES (?, ?, ?, '1', ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssss", $nombre, $link, $modulo, $estilos);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Error en la consulta: " . $conn->error;
            }
        }
    }

    echo "Datos insertados correctamente.";
    header("Location: ../secciones.php");
    exit();
}
?>
