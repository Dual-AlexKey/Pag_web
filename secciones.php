<?php
include 'conect/conexion.php';
//inclusion de informacion
include('estilo/data.php');
// Incluir el header.php
include('estilo/header.php');
// Incluir el menu.php
include('estilo/menu.php');

$sql = "SHOW TABLES LIKE 'menu_%'";
$result = $conn->query($sql);

$menu_tables = [];
while ($row = $result->fetch_array()) {
    $menu_tables[] = $row[0]; // Guardar nombres de tablas
}

?>

    <div class="contenido-derecha">
        <a href="panel.php"><button class="boton-cerrar">X</button></a>
        <div class="bloque-verde"><h2>Secciones</h2></div>
        <a href="newseccion.php"><button class="boton-nvpag">Nueva seccion</button></a>
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
        foreach ($menu_tables as $table) {
            // Consultar datos de cada tabla encontrada
            $sql = "SELECT * FROM `$table`";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>||</td>";
                echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["modulo"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["orden"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["nro_item"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["visitas"]) . "</td>";
                echo "<td>";
                
                // Obtener valores
                $nombre = urlencode($row["nombre"]);
                $cod = urlencode($row["cod"]); // Capturar el código
                $codtab = urlencode($row["codtab"]); // Capturar el código
            
                $botones = [
                    ["pagina" => "editccion.php", "imagen" => "https://i.ibb.co/nNQjXb7b/wp-editar.png"],
                    ["pagina" => "panel.php", "imagen" => "https://i.ibb.co/hPQ0zQ5/ws-menu.png"],
                    ["pagina" => "ajustes.php", "imagen" => "https://i.ibb.co/VYrngfWv/wp-page.png"],
                    ["pagina" => "config.php", "imagen" => "https://i.ibb.co/Fq6n7h1M/wp-tools.png"],
                    ["pagina" => "conect/eliminar_elemento.php", "imagen" => "https://i.ibb.co/LdTnB39W/wp-borrar.png"]
                ];
    
                foreach ($botones as $index => $boton) {
                    $width = ($boton['pagina'] === "panel.php") ? "25px" : "25px";
                    $height = ($boton['pagina'] === "panel.php") ? "15px" : "25px";
                    
                    // Solo agregar codtab si tiene valor
                    $url = "{$boton['pagina']}?cod=$cod";
                    if (!empty($codtab)) {
                        $url .= "&codtab=$codtab";
                    }
                
                    echo "<a href='$url' class='btn_st'>
                            <img src='{$boton['imagen']}' alt='Botón' style='width: $width; height: $height; vertical-align: middle;'>
                          </a> ";
                }
                
            
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

