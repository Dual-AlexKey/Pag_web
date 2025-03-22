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
    file_put_contents($archivo_contador_global, json_encode($contadores));

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
?>
