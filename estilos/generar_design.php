<?php
include __DIR__ . '/../websystem/conect/conexion.php'; // Incluye la conexión (mysqli)

function generarDiseno($nombreArchivo) {
    global $conn; // Usar la conexión global con mysqli

    // Buscar en la base de datos el diseño basado en el nombre del archivo
    $sql = "SELECT * FROM detalles WHERE nombre = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("❌ Error en la preparación de la consulta: " . $conn->error);
    }

    // Bind del parámetro
    $stmt->bind_param("s", $nombreArchivo);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado
    $resultado = $stmt->get_result();

    if ($resultado && $row = $resultado->fetch_assoc()) {
        // Extraer directrices
        $estructsecc = $row['estructsecc'];  // Diseño de la sección
        $fondsecc = $row['fondsecc'];        // Fondo de la sección
        $mostrar = $row['mostrar'];          // Información adicional

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

        // Generar el contenedor de columnas con el fondo dinámico
        echo "<div class='Columna-Container' style='background-color: $fondsecc;'>\n";

        // Imprimir las columnas activas en el orden correcto
        foreach ($columnas as $clase => $activo) {
            if ($activo) {
                echo "    <div class='$clase'>\n";
                echo "        Contenido dinámico para $clase\n";

                if (!empty($mostrar)) {
                    echo "        <div class='info'>$mostrar</div>\n";
                }

                echo "    </div>\n";
            }
        }

        echo "</div>\n";

    } else {
        echo "❌ No se encontró información para el archivo $nombreArchivo.";
    }

    // Cerrar el statement
    $stmt->close();
}
?>
