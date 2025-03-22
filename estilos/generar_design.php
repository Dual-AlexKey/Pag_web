<?php
include __DIR__ . '/../websystem/conect/conexion.php'; // Incluye la conexión (mysqli)

function generarDiseno($nombreArchivo) {
    global $conn; // Usar la conexión global con mysqli

    // 🔍 Buscar el diseño en la tabla "detalles"
    $sqlDetalles = "SELECT * FROM detalles WHERE nombre = ?";
    $stmtDetalles = $conn->prepare($sqlDetalles);

    if (!$stmtDetalles) {
        die("❌ Error en la preparación de la consulta: " . $conn->error);
    }

    // Bind del parámetro para la tabla "detalles"
    $stmtDetalles->bind_param("s", $nombreArchivo);
    $stmtDetalles->execute();
    $resultadoDetalles = $stmtDetalles->get_result();

    if ($resultadoDetalles && $rowDetalles = $resultadoDetalles->fetch_assoc()) {
        // Extraer directrices de la tabla "detalles"
        $estructsecc = $rowDetalles['estructsecc'];  // Diseño de la sección
        $fondsecc = $rowDetalles['fondsecc'];        // Fondo de la sección
        $mostrar = $rowDetalles['mostrar'];          // Información adicional
        $cod = $rowDetalles['cod'];                 // Relación con "paginas"

        // Inicializar columnas en el orden correcto: izquierda, centro, derecha
        $columnas = [
            'Columna_Izquierda' => false,
            'Columna_Central' => false,
            'Columna_Derecha' => false
        ];

        // Configurar qué columnas mostrar según `estructsecc`
        switch ($estructsecc) {
            case 'Estilo Izquierda':
                $columnas['Columna_Izquierda'] = true;
                $columnas['Columna_Central'] = true;
                break;
            case 'Estilo Derecha':
                $columnas['Columna_Central'] = true;
                $columnas['Columna_Derecha'] = true;
                break;
            case 'Estilo 3Columnas':
                $columnas['Columna_Izquierda'] = true;
                $columnas['Columna_Central'] = true;
                $columnas['Columna_Derecha'] = true;
                break;
            case 'Estilo Full':
                $columnas['Columna_Central'] = true;
                break;
        }

        // 🔍 Buscar datos en la tabla "paginas" usando "cod" como referencia
        $sqlPaginas = "SELECT titulo, contenido FROM paginas WHERE cod = ?";
        $stmtPaginas = $conn->prepare($sqlPaginas);

        if (!$stmtPaginas) {
            die("❌ Error en la preparación de la consulta: " . $conn->error);
        }

        $stmtPaginas->bind_param("s", $cod);
        $stmtPaginas->execute();
        $resultadoPaginas = $stmtPaginas->get_result();
        $contenidoCentral = $resultadoPaginas->fetch_assoc() ?: ['titulo' => null, 'contenido' => null];

        // Modificar el contenido para eliminar "../img/"
        if (!empty($contenidoCentral['contenido'])) {
            $contenidoOriginal = $contenidoCentral['contenido'];
            $contenidoModificado = eliminarPrefijoImg($contenidoOriginal);

            // Actualizar el contenido central con el texto modificado
            $contenidoCentral['contenido'] = $contenidoModificado;
        }

        // Generar el contenedor de columnas con el fondo dinámico
        echo "<div class='Columna-Container' style='background-color: $fondsecc;'>\n";

        // Imprimir las columnas activas en el orden correcto
        foreach ($columnas as $clase => $activo) {
            if ($activo) {
                echo "    <div class='$clase'>\n";

                // Columna Central: Mostrar título y contenido
                if ($clase === 'Columna_Central') {
                    if (!empty($contenidoCentral['titulo'])) {
                        echo "        <h1>{$contenidoCentral['titulo']}</h1>\n";
                    }
                    if (!empty($contenidoCentral['contenido'])) {
                        echo "        <p>{$contenidoCentral['contenido']}</p>\n";
                    }
                } else {
                    echo "        Contenido dinámico para $clase\n";
                }

                // Información adicional (mostrar)
                if (!empty($mostrar) && $clase !== 'Columna_Central') {
                    echo "        <div class='info'>$mostrar</div>\n";
                }

                echo "    </div>\n";
            }
        }

        echo "</div>\n";

    } else {
        echo "❌ No se encontró información para el archivo $nombreArchivo.";
    }

    // Cerrar los statements
    $stmtDetalles->close();
    if (isset($stmtPaginas)) $stmtPaginas->close();
}

// Función para eliminar el prefijo "../" en todas las ocurrencias de "../img/"
function eliminarPrefijoImg($contenido) {
    return str_replace("../img/", "img/", $contenido);
}
?>
