<?php
include 'conect/conexion.php';
// Inclusión de información
include('estilo/data.php');
include '../contador_visitas.php';

include('estilo/header.php');
include('estilo/menu.php');

// Buscar tablas que comiencen con "menu_"
$sql = "SHOW TABLES LIKE 'menu_%'";
$result = $conn->query($sql);

$menu_tables = [];
while ($row = $result->fetch_array()) {
    $menu_tables[] = $row[0]; // Guardar nombres de tablas
}

// Ruta del log del servidor (ajusta esta ruta según tu configuración)
?>
<div class="contenido-derecha">
    <a href="panel.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Secciones</h2></div>
    <a href="newseccion.php"><button class="boton-nvpag">Nueva sección</button></a>
    <div class="bloque-gris"><h3>Insertar</h3></div>
    <table class="tableborderfull">
        <tr>
            <td>||</td>
            <td>Sección</td>
            <td>Módulo</td>
            <td>Orden</td>
            <td>Nro de items</td>
            <td>Vistas</td>
            <td>Opciones</td>
        </tr>

        <?php
        $nombres_unicos = []; // Para almacenar los nombres ya mostrados

        foreach ($menu_tables as $table) {
            // Consultar datos de cada tabla encontrada
            $sql = "SELECT * FROM `$table`";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                $nombre = $row["nombre"];

                // Si el nombre ya fue mostrado, saltarlo
                if (isset($nombres_unicos[$nombre])) {
                    continue;
                }

                // Marcar el nombre como mostrado
                $nombres_unicos[$nombre] = true;

                // Verificar si secciones es NULL o tiene un valor
                $seccion_clase = '';
                if (empty($row['secciones'])) {
                    $seccion_clase = 'style="font-weight: bold;"'; // Negrita si es NULL
                }

                // Calcular espacios o tabulaciones basados en el número de '/'
                $num_tabs = substr_count($row['secciones'] ?? '', '/');
                $espacios = str_repeat('&nbsp;&nbsp;', $num_tabs);

                // Consultar valores de `orden` y `nro_item` en la tabla `detalles`
                $orden = $row['orden'] ?? 0;
                $nro_item = $row['nro_item'] ?? 0;
                $detalle_sql = "SELECT orden, ordensecc FROM detalles WHERE cod = ?";
                $stmt_detalle = $conn->prepare($detalle_sql);
                if ($stmt_detalle) {
                    $stmt_detalle->bind_param("s", $row['cod']);
                    $stmt_detalle->execute();
                    $stmt_detalle->bind_result($orden_detalle, $ordensecc_detalle);
                    if ($stmt_detalle->fetch()) {
                        $orden = $orden_detalle;
                        $nro_item = $ordensecc_detalle;
                    }
                    $stmt_detalle->close();
                }

               
                // Definir el destino para el nombre como enlace
                $cod = urlencode($row["cod"]);
                $codtab = urlencode($row["codtab"] ?? '');
                $nombre_url = urlencode($row["nombre"]);
                $seccion_destino = "editccion.php"; // Valor por defecto si es NULL o vacío
                $accion_param = ""; // Por defecto vacío
                if (!empty($row["secciones"])) {
                    $seccion_destino = "subseccion.php"; // Cambiar destino si secciones tiene un valor
                    $accion_param = "&accion=subseccion"; // Agregar identificador
                }
                $vistas = obtener_contador_por_pagina($nombre);


                // Imprimir la fila con los ajustes solicitados
                echo "<tr>";
                echo "<td>||</td>";
                echo "<td $seccion_clase>{$espacios}<a href='$seccion_destino?cod=$cod&nombre=$nombre_url&codtab=$codtab$accion_param' style='color: black; text-decoration: none;'>" . htmlspecialchars($nombre) . "</a></td>";
                echo "<td>" . htmlspecialchars($row["modulo"]) . "</td>";
                echo "<td>" . htmlspecialchars($orden) . "</td>";
                echo "<td>" . htmlspecialchars($nro_item) . "</td>";
                echo "<td>" . htmlspecialchars($vistas) . "</td>";
                echo "<td>";

                // Botón de edición dinámica (dependiendo de "secciones")
                echo "<a href='$seccion_destino?cod=$cod&nombre=$nombre_url&codtab=$codtab$accion_param' class='btn_st'>
                        <img src='https://i.ibb.co/nNQjXb7b/wp-editar.png' alt='Botón Editar' style='width: 25px; height: 25px; vertical-align: middle;'>
                      </a>";

                // Botón de creación de subsección (siempre presente)
                echo "<a href='subseccion.php?cod=$cod&nombre=$nombre_url&codtab=$codtab' class='btn_st'>
                        <img src='https://i.ibb.co/hPQ0zQ5/ws-menu.png' alt='Botón Crear Subsección' style='width: 25px; height: 15px; vertical-align: middle;'>
                      </a>";

                // Otros botones
                echo "<a href='seccionpagina.php?cod=$cod&nombre=$nombre_url' class='btn_st'>
                        <img src='https://i.ibb.co/VYrngfWv/wp-page.png' alt='Botón Página' style='width: 25px; height: 25px; vertical-align: middle;'>
                      </a>";
                echo "<a href='secciondetalle.php?cod=$cod&nombre=$nombre_url' class='btn_st'>
                        <img src='https://i.ibb.co/Fq6n7h1M/wp-tools.png' alt='Botón Detalle' style='width: 25px; height: 25px; vertical-align: middle;'>
                      </a>";
                echo "<a href='conect/eliminar_elemento.php?cod=$cod&codtab=$codtab&nombre=$nombre_url' class='btn_st'>
                        <img src='https://i.ibb.co/LdTnB39W/wp-borrar.png' alt='Botón Eliminar' style='width: 25px; height: 25px; vertical-align: middle;'>
                      </a>";
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
</div>

<?php
// Incluir el footer.php
include('estilo/footer.php');
?>
