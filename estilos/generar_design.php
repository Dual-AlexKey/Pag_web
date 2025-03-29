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
        $estructsecc = $rowDetalles['estructsecc'];  // Diseño de la sección
        $fondsecc = $rowDetalles['fondsecc'];        // Fondo de la sección
        $mostrar = $rowDetalles['mostrar'];          // Información adicional
        $cod = $rowDetalles['cod'];                 // Relación con "paginas"

        // Configurar qué columnas mostrar según `estructsecc`
        $columnas = [];
        if (in_array($estructsecc, ['Estilo Izquierda', 'Estilo 3Columnas'])) {
            $columnas['Columna_Izquierda'] = true;
        }
        if (in_array($estructsecc, ['Estilo Derecha', 'Estilo 3Columnas'])) {
            $columnas['Columna_Derecha'] = true;
        }
        $columnas['Columna_Central'] = true; // Siempre presente

        // 🔍 Buscar datos en la tabla "paginas"
        $sqlPaginas = "SELECT titulo, contenido FROM paginas WHERE cod = ?";
        $stmtPaginas = $conn->prepare($sqlPaginas);
        
        if (!$stmtPaginas) {
            die("❌ Error en la preparación de la consulta: " . $conn->error);
        }

        $stmtPaginas->bind_param("s", $cod);
        $stmtPaginas->execute();
        $resultadoPaginas = $stmtPaginas->get_result();
        $contenidoCentral = $resultadoPaginas->fetch_assoc() ?: ['titulo' => null, 'contenido' => null];

        if (!empty($contenidoCentral['contenido'])) {
            $contenidoCentral['contenido'] = eliminarPrefijoImg($contenidoCentral['contenido']);
        }

        // Generar la estructura de columnas con flexbox
        echo "<div class='Columna-Container' style='background-color: $fondsecc; display: flex; flex-wrap: nowrap;'>\n";
        
        // Imprimir las columnas en el orden correcto
        foreach (['Columna_Izquierda', 'Columna_Central', 'Columna_Derecha'] as $clase) {
            if (!empty($columnas[$clase])) {
                $width = ($clase === 'Columna_Central') ? 'flex: 1;' : 'width: 20%;';
                echo "    <div class='$clase' style='$width padding: 10px;'>\n";
                
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

    $stmtDetalles->close();
    if (isset($stmtPaginas)) $stmtPaginas->close();
}

// Función para eliminar el prefijo "../img/"
function eliminarPrefijoImg($contenido) {
    return str_replace("../img/", "img/", $contenido);
}
?>