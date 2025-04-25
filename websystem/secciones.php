<?php
include 'conect/conexion.php';
include('estilo/data.php');
include '../contador_visitas.php';
include('estilo/header.php');
include('estilo/menu.php');

// 1️⃣ Cargar tablas `menu_*`
$menu_tables = [];
$sql = "SHOW TABLES LIKE 'menu_%'";
$result = $conn->query($sql);
while ($row = $result->fetch_array()) {
    $menu_tables[] = $row[0];
}

// 2️⃣ Cargar datos de `detalles` para orden y nro de ítems
$detalles_map = [];
$sql_det = "SELECT nombre, orden, ordensecc FROM detalles";
$result_det = $conn->query($sql_det);
if ($result_det) {
    while ($row = $result_det->fetch_assoc()) {
        $detalles_map[trim($row['nombre'])] = $row;
    }
}

// 3️⃣ Cargar registros de `menu_*`
$registros = [];
foreach ($menu_tables as $table) {
    $sql = "SELECT * FROM `$table`";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $nombre = trim($row['nombre']);
        if (isset($detalles_map[$nombre])) {
            $row['orden'] = $detalles_map[$nombre]['orden'];
            $row['ordensecc'] = $detalles_map[$nombre]['ordensecc'];
        } else {
            $row['orden'] = 0;
            $row['ordensecc'] = 0;
        }
        $row['fuente'] = 'menu'; // ➕ Indica que viene de menu
        $registros[] = $row;
    }
}

// 4️⃣ Agregar registros desde `subnivel`
$sql_sub = "SELECT * FROM subnivel ORDER BY Num_nivel ASC";
$result_sub = $conn->query($sql_sub);
if ($result_sub) {
    while ($row = $result_sub->fetch_assoc()) {
        $nombre = trim($row['nombre']);
        if (isset($detalles_map[$nombre])) {
            $row['orden'] = $detalles_map[$nombre]['orden'];
            $row['ordensecc'] = $detalles_map[$nombre]['ordensecc'];
        } else {
            $row['orden'] = 0;
            $row['ordensecc'] = 0;
        }
        $row['fuente'] = 'subnivel'; // ➕ Indica que viene de subnivel
        $registros[] = $row;
    }
}

// 5️⃣ Construir árbol usando `secciones`
function construirArbol($registros) {
    $tree = [];
    $index = [];

    foreach ($registros as $registro) {
        $nombre = $registro['nombre'];
        $index[$nombre] = $registro;
        $index[$nombre]['hijos'] = [];
    }

    foreach ($index as &$registro) {
        $secciones = trim($registro['secciones'] ?? '', '/');
        if (empty($secciones)) {
            $tree[] = &$registro;
        } else {
            $rutas = explode("/", $secciones);
            $padre_nombre = end($rutas); // padre directo es el último nombre de la ruta
            if (isset($index[$padre_nombre])) {
                $index[$padre_nombre]['hijos'][] = &$registro;
            } else {
                // Si no encuentra padre, lo agrega como raíz
                $tree[] = &$registro;
            }
        }
    }

    return $tree;
}

$tree = construirArbol($registros);
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

                // Imprimir la fila
                echo "<tr>";
                echo "<td>||</td>";
                echo "<td $seccion_clase>{$espacios}<a href='$seccion_destino?cod=$cod&nombre=$nombre_url&codtab=$codtab$accion_param' style='color: black; text-decoration: none;'>$nombre</a></td>";
                echo "<td>$modulo</td>";
                echo "<td>$orden</td>";
                echo "<td>$nro_item</td>";
                echo "<td>$vistas</td>";

                // Botón de edición dinámica (dependiendo de "secciones")
                echo "<td><a href='$seccion_destino?cod=$cod&nombre=$nombre_url&codtab=$codtab$accion_param' class='btn_st'>
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
                echo "<td><a href='seccionpagina.php?cod=$cod&nombre=$nombre_url' class='btn_st'>
                        <img src='https://i.ibb.co/VYrngfWv/wp-page.png' alt='Botón Página' style='width: 25px; height: 25px; vertical-align: middle; padding-right: 5px;'>
                      </a></td>";
                echo "<td><a href='secciondetalle.php?cod=$cod&nombre=$nombre_url' class='btn_st'>
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
