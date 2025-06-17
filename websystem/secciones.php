<?php
include 'conect/conexion.php';
include('estilo/data.php');
include '../contador_visitas.php';
include('estilo/header.php');
include('estilo/menu.php');

function arbol($conn) {
    // 1️⃣ Cargar tablas `menu_%` (nivel 1 - padres)
    $menu_tables = [];
    $sql = "SHOW TABLES LIKE 'menu_%'";
    $result = $conn->query($sql);
    while ($row = $result->fetch_array()) {
        $menu_tables[] = $row[0];
    }

    // 2️⃣ Mapas auxiliares
    $detalles_map = [];
    $result = $conn->query("SELECT nombre, orden, ordensecc, codp FROM detalles");
    while ($row = $result->fetch_assoc()) {
        $detalles_map[trim($row['nombre'])] = $row;
    }

    $paginas_map = [];
    $result = $conn->query("SELECT titulo, codp FROM paginas");
    while ($row = $result->fetch_assoc()) {
        $paginas_map[trim($row['titulo'])] = $row;
    }

    // 3️⃣ Obtener registros nivel 1 (menu_%)
    $registros = [];
    foreach ($menu_tables as $table) {
        $result = $conn->query("SELECT * FROM `$table`");
        while ($row = $result->fetch_assoc()) {
            $nombre = trim($row['nombre']);
            $row['fuente'] = 'menu';
            $row['secciones'] = ''; // nivel 1 no tiene padre

            $row['orden'] = $detalles_map[$nombre]['orden'] ?? 0;
            $row['ordensecc'] = $detalles_map[$nombre]['ordensecc'] ?? 0;
            $row['codpD'] = $detalles_map[$nombre]['codp'] ?? '';
            $row['codpP'] = $paginas_map[$nombre]['codp'] ?? '';
            $row['codpS'] = '';
            $registros[] = $row;
        }
    }

    // 4️⃣ Obtener registros de `subnivel` (niveles 2 y 3)
    $result = $conn->query("SELECT * FROM subnivel ORDER BY Num_nivel ASC");
    while ($row = $result->fetch_assoc()) {
        $nombre = trim($row['nombre']);
        $row['fuente'] = 'subnivel';
        $row['orden'] = $detalles_map[$nombre]['orden'] ?? 0;
        $row['ordensecc'] = $detalles_map[$nombre]['ordensecc'] ?? 0;
        $row['codpD'] = $detalles_map[$nombre]['codp'] ?? '';
        $row['codpP'] = $paginas_map[$nombre]['codp'] ?? '';
        $row['codpS'] = $row['codp'] ?? '';
        $registros[] = $row;
    }

    // 5️⃣ Construir árbol jerárquico
    $index = [];
    $tree = [];

    foreach ($registros as $row) {
        $ruta = trim($row['secciones'] ?? '', '/');
        $clave = $ruta !== '' ? $ruta . '/' . $row['nombre'] : $row['nombre'];
        $row['ruta'] = $clave;
        $row['hijos'] = [];
        $index[$clave] = $row;
    }

    foreach ($index as $clave => &$nodo) {
        $ruta = trim($nodo['secciones'] ?? '', '/');

        if (empty($ruta)) {
            // Es un nodo raíz
            $tree[] = &$nodo;
        } else {
            $clave_padre = $ruta;
            if (isset($index[$clave_padre])) {
                $index[$clave_padre]['hijos'][] = &$nodo;
            } else {
                // Padre no encontrado, agregar como raíz
                $tree[] = &$nodo;
            }
        }
    }

    return $tree;
}


$tree = arbol($conn);
?>

<div class="contenido-derecha">
    <a href="panel.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Secciones</h2></div>
    <a href="newseccion.php"><button class="boton-nvpag">Nueva sección</button></a>
    <div class="bloque-gris"><h3>Insertar</h3></div>
    <table class="tableborderfull" style="width: 100%; border-collapse: collapse;">
        <tr>
            <th style="width: 2%; text-align: left; padding-left: 10px;">||</th>
            <th style="width: 60%; text-align: left; padding-left: 10px;">Sección</th>
            <th style="width: 10%; text-align: left; padding-left: 10px;">Módulo</th>
            <th style="width: 10%; text-align: left; padding-left: 10px;">Orden</th>
            <th style="width: 10%; text-align: left; padding-left: 10px;">Nro de ítems</th>
            <th style="width: 10%; text-align: left; padding-left: 10px;">Vistas</th>
            <th style="width: 5%; text-align: center;" colspan="5">Opciones</th>
        </tr>

        <?php
        // ✅ Renderizar el árbol jerárquico en la tabla
        function renderizarArbol($nodos, $nivel = 0) {
            global $conn;

            foreach ($nodos as $nodo) {
                $nombre = htmlspecialchars($nodo['nombre']);
                $modulo = htmlspecialchars($nodo['modulo'] ?? '');
                $orden = htmlspecialchars($nodo['ordensecc'] ?? 0);
                $nro_item = htmlspecialchars($nodo['orden'] ?? 0);
                $vistas = obtener_contador_por_pagina($nombre);

                // Codp separados por tipo
$codpS = htmlspecialchars($nodo['codpS'] ?? $nodo['codp'] ?? '');
$codpP = htmlspecialchars($nodo['codpP'] ?? '');
$codpD = htmlspecialchars($nodo['codpD'] ?? '');

                // Aplicar estilos en negrita si es raíz (nivel 0)
                $seccion_clase = $nivel == 0 ? 'style="font-weight: bold;"' : '';

                // Calcular espacios o tabulaciones basados en el nivel
                $espacios = str_repeat('&nbsp;&nbsp;', $nivel);

                // Construir enlace de acción
                $cod = urlencode($nodo['cod'] ?? '');
                $codtab = urlencode($nodo['codtab'] ?? '');
                $codtab = urlencode($nodo['codtab'] ?? '');
                $nombre_url = urlencode($nodo['nombre']);
                $seccion_url = urlencode($nodo['secciones']);
                $seccion_destino = empty($nodo['secciones']) ? "editccion.php" : "subseccion.php";
                $accion_param = empty($nodo['secciones']) ? "" : "&accion=subseccion";

                // Para elementos subnivel, buscar el ID y usarlo en editccion también
                $id_subnivel = '';
                if ($nodo['fuente'] === 'subnivel') {
                    $stmt_id = $conn->prepare("SELECT id FROM subnivel WHERE nombre = ? LIMIT 1");
                    if ($stmt_id) {
                        $stmt_id->bind_param("s", $nodo['nombre']);
                        $stmt_id->execute();
                        $stmt_id->bind_result($id_result);
                        if ($stmt_id->fetch()) {
                            $id_subnivel = $id_result;
                        }
                        $stmt_id->close();
                    }
                }

                // Imprimir la fila
                echo "<tr>";
                echo "<td>||</td>";
                echo "<td $seccion_clase>{$espacios}<a href='$seccion_destino?cod=$cod&nombre=$nombre_url&codpS=$codpS&codtab=$codtab$accion_param' style='color: black; text-decoration: none;'>$nombre</a></td>";
                echo "<td>$modulo</td>";
                echo "<td>$orden</td>";
                echo "<td>$nro_item</td>";
                echo "<td>$vistas</td>";

                // Botón de edición dinámica (dependiendo de "secciones")
                $extra_id_sub = ($nodo['fuente'] === 'subnivel') ? "&idSub=$id_subnivel" : '';
                echo "<td><a href='$seccion_destino?cod=$cod&nombre=$nombre_url&codtab=$codtab$accion_param$extra_id_sub' class='btn_st'>
                <img src='https://i.ibb.co/nNQjXb7b/wp-editar.png' alt='Botón Editar' style='width: 25px; height: 25px; vertical-align: middle; padding-right: 5px;'>
                </a> </td>";

                if ($nivel == 2) {
                    // Agregar 5 espacios como proporción
                    echo "<td><a></a></td>";;
                } else {
                    echo "<td><a href='subseccion.php?cod=$cod&nombre=$nombre_url&codtab=$codtab' class='btn_st'>
                            <img src='https://i.ibb.co/hPQ0zQ5/ws-menu.png' alt='Botón Crear Subsección' style='width: 25px; height: 15px; vertical-align: middle; padding-right: 5px;'>
                            </a></td>";
                }

                // Otros botones
                echo "<td><a href='seccionpagina.php?cod=$cod&nombre=$nombre_url$extra_id_sub&codpD=$codpD' class='btn_st'>
                        <img src='https://i.ibb.co/VYrngfWv/wp-page.png' alt='Botón Página' style='width: 25px; height: 25px; vertical-align: middle; padding-right: 5px;'>
                      </a></td>";
                echo "<td><a href='secciondetalle.php?cod=$cod&nombre=$nombre_url$extra_id_sub&codpD=$codpD' class='btn_st'>
                        <img src='https://i.ibb.co/Fq6n7h1M/wp-tools.png' alt='Botón Detalle' style='width: 25px; height: 25px; vertical-align: middle; padding-right: 5px;'>
                      </a></td>";
                      $id_eliminar = $nombre; // Por defecto, usamos cod (para tablas menu_)
                      $tabla_origen = ($nodo['fuente'] === 'subnivel') ? 'subnivel' : 'menu';
                      
                      if ($tabla_origen === 'subnivel') {
                          // Buscar el ID real en subnivel
                          $stmt_id = $conn->prepare("SELECT id FROM subnivel WHERE nombre = ? LIMIT 1");
                          if ($stmt_id) {
                              $stmt_id->bind_param("s", $nombre);
                              $stmt_id->execute();
                              $stmt_id->bind_result($id_result);
                              if ($stmt_id->fetch()) {
                                  $id_eliminar = $id_result;
                              }
                              $stmt_id->close();
                          }
                      }
                      
                      // Eliminar siempre usa "id", el backend sabrá si es de subnivel o no por una bandera
                      $nombre_tabla = ($tabla_origen === 'subnivel') ? 'subnivel' : $codtab;
                      
                      if ($nodo['fuente'] === 'subnivel') {
                        // Buscar el ID real en subnivel
                        $stmt_id = $conn->prepare("SELECT id FROM subnivel WHERE nombre = ? LIMIT 1");
                        if ($stmt_id) {
                            $stmt_id->bind_param("s", $nombre);
                            $stmt_id->execute();
                            $stmt_id->bind_result($id_result);
                            if ($stmt_id->fetch()) {
                                $id_eliminar = $id_result;
                            }
                            $stmt_id->close();
                        }
                    
                        // Enlace con idSub (para subnivel)
                        echo "<td><a href='conect/eliminar_elemento.php?idSub=$id_eliminar&nombre=$nombre_url&secc=$seccion_url' class='btn_st'>
                                <img src='https://i.ibb.co/LdTnB39W/wp-borrar.png' alt='Botón Eliminar' style='width: 25px; height: 25px; vertical-align: middle; padding-right: 5px;'>
                              </a></td>";
                    } else {
                        // Enlace clásico (para menu_)
                        echo "<td><a href='conect/eliminar_elemento.php?cod=$cod&codtab=$codtab&nombre=$nombre_url&secc=$seccion_url' class='btn_st'>
                                <img src='https://i.ibb.co/LdTnB39W/wp-borrar.png' alt='Botón Eliminar' style='width: 25px; height: 25px; vertical-align: middle; padding-right: 5px;'>
                              </a></td>";
                    }
                    

                      
                echo "</tr>";
            

                // Renderizar los hijos del nodo actual
                if (!empty($nodo['hijos'])) {
                    renderizarArbol($nodo['hijos'], $nivel + 1);
                }
            }
        }

        renderizarArbol($tree); // Renderizar el árbol jerárquico
        ?>
    </table>
</div>



<?php
// Incluir el footer.php
include('estilo/footer.php');
?>
