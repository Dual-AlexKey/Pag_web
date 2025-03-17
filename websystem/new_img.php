<?php
include 'conect/conexion.php';
// Inclusión de información
include('estilo/data.php');
// Incluir el header.php
include('estilo/header.php');
// Incluir el menu.php
include('estilo/menu.php');
?>
<div class="contenido-derecha">
    <a href="panel.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Imagenes</h2></div>
    <a href="new_itemimg.php"><button class="boton-nvpag">Nueva sección</button></a>
    <div class="bloque-gris"><h3>Insertar</h3></div>

    <table class="tableborderfull">
        <tr>
            <td>||</td>
            <td>Item:/</td>
            <td>Orden</td>
            <td>Tipo</td>
            <td>Pos. hor</td>
            <td>Pos. Ver</td>
            <td>Ingreso desde</td>
            <td>Duracion</td>
            <td>Opciones</td>
        </tr>

        <?php
       /* $nombres_unicos = []; // Para almacenar los nombres ya mostrados

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

                echo "<tr>";
                echo "<td>||</td>";
                echo "<td>" . htmlspecialchars($nombre) . "</td>";
                echo "<td>" . htmlspecialchars($row["modulo"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["orden"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["nro_item"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["visitas"]) . "</td>";
                echo "<td>";

                // Obtener valores para los botones
                $cod = urlencode($row["cod"]); 
                $codtab = urlencode($row["codtab"]);
                $nombre = urlencode($row["nombre"]); 


                $botones = [
                    ["pagina" => "editccion.php", "imagen" => "https://i.ibb.co/nNQjXb7b/wp-editar.png"],
                    ["pagina" => "panel.php", "imagen" => "https://i.ibb.co/hPQ0zQ5/ws-menu.png"],
                    ["pagina" => "ajustes.php", "imagen" => "https://i.ibb.co/VYrngfWv/wp-page.png"],
                    ["pagina" => "config.php", "imagen" => "https://i.ibb.co/Fq6n7h1M/wp-tools.png"],
                    ["pagina" => "conect/eliminar_elemento.php", "imagen" => "https://i.ibb.co/LdTnB39W/wp-borrar.png"]
                ];

                foreach ($botones as $boton) {
                    $width = "25px";
                    $height = ($boton['pagina'] === "panel.php") ? "15px" : "25px";

                    // Construir URL con los parámetros adecuados
                    $url = "{$boton['pagina']}?cod=$cod";
                    if (!empty($codtab)) {
                        $url .= "&codtab=$codtab";
                        $url .= "&nombre=$nombre";
                    }
                    elseif (!empty($nombre)) {
                        $url .= "&nombre=$nombre";
                    }

                    echo "<a href='$url' class='btn_st'>
                            <img src='{$boton['imagen']}' alt='Botón' style='width: $width; height: $height; vertical-align: middle;'>
                          </a> ";
                }

                echo "</td>";
                echo "</tr>";
            }
        }*/
        ?>

    </table>
</div>
<?php
// Incluir el footer.php
include('estilo/footer.php');
?>