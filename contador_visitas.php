<?php
// Archivo: contador_visitas.php

// Función para incrementar y manejar el contador de visitas por página
function manejar_contador_por_pagina($nombre_pagina) {
    // Ruta del archivo único que almacena los contadores de todas las páginas
    $archivo_contador_global = __DIR__ . '/contador_paginas.txt';

    // Si el archivo global no existe, crearlo
    if (!file_exists($archivo_contador_global)) {
        file_put_contents($archivo_contador_global, json_encode([])); // Crear archivo vacío en formato JSON
    }

    // Leer los datos actuales del archivo global
    $contenido = file_get_contents($archivo_contador_global);
    $contadores = json_decode($contenido, true) ?: []; // Decodificar JSON, usar array vacío si está vacío

    // Incrementar el contador para la página actual
    if (!isset($contadores[$nombre_pagina])) {
        $contadores[$nombre_pagina] = 0; // Inicializar si no existe
    }
    $contadores[$nombre_pagina]++;

    // Guardar el nuevo estado de los contadores en el archivo global
    file_put_contents($archivo_contador_global, json_encode($contadores, JSON_PRETTY_PRINT));

    // Retornar el contador actualizado para la página actual
    return $contadores[$nombre_pagina];
}

// Función para obtener el contador de una página sin incrementarlo
function obtener_contador_por_pagina($nombre_pagina) {
    // Ruta del archivo único que almacena los contadores de todas las páginas
    $archivo_contador_global = __DIR__ . '/contador_paginas.txt';

    // Si el archivo global no existe, retornar 0
    if (!file_exists($archivo_contador_global)) {
        return 0;
    }

    // Leer los datos actuales del archivo global
    $contenido = file_get_contents($archivo_contador_global);
    $contadores = json_decode($contenido, true) ?: []; // Decodificar JSON, usar array vacío si está vacío

    // Retornar el contador de la página actual, o 0 si no existe
    return $contadores[$nombre_pagina] ?? 0;
}

// Función para eliminar un archivo PHP y su registro del contador
function eliminar_archivo_y_contador($nombre_archivo) {
    // Ruta del archivo único que almacena los contadores de todas las páginas
    $archivo_contador_global = __DIR__ . '/contador_paginas.txt';

    // Eliminar el archivo PHP
    if (file_exists($nombre_archivo)) {
        unlink($nombre_archivo); // Eliminar el archivo físico
        echo "✅ Archivo eliminado: $nombre_archivo<br>";
    } else {
        echo "⚠️ El archivo no existe: $nombre_archivo<br>";
    }

    // Leer y actualizar los datos en el archivo de contadores
    if (file_exists($archivo_contador_global)) {
        $contenido = file_get_contents($archivo_contador_global);
        $contadores = json_decode($contenido, true) ?: []; // Decodificar JSON, usar array vacío si está vacío

        // Obtener el nombre del archivo (sin la extensión)
        $nombre_sin_extension = basename($nombre_archivo, '.php');

        // Eliminar el registro correspondiente del contador
        if (isset($contadores[$nombre_sin_extension])) {
            unset($contadores[$nombre_sin_extension]); // Eliminar el registro del array
            echo "✅ Registro eliminado del contador: $nombre_sin_extension<br>";

            // Si el array de contadores está vacío después de eliminar
            if (empty($contadores)) {
                unlink($archivo_contador_global); // Eliminar el archivo .txt si no quedan registros
                echo "✅ Archivo de contadores eliminado porque está vacío: $archivo_contador_global<br>";
            } else {
                // Guardar los datos actualizados en el archivo global
                file_put_contents($archivo_contador_global, json_encode($contadores, JSON_PRETTY_PRINT));
                echo "✅ Contadores actualizados en: $archivo_contador_global<br>";
            }
        } else {
            echo "⚠️ No se encontró un registro en el contador para: $nombre_sin_extension<br>";
        }
    }
}
?>
