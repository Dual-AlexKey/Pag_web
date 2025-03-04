<?php
include 'conect/conexion.php';
//inclusion de informacion
include('estilo/data.php');
// Incluir el header.php
include('estilo/header.php');
// Incluir el menu.php
include('estilo/menu.php');

$sql = "SHOW TABLES LIKE 'tablero'";
$result = $conn->query($sql);

$menu_tables = [];
while ($row = $result->fetch_array()) {
    $menu_tables[] = $row[0]; // Guardar nombres de tablas
}
?>
    <div class="contenido-derecha">
        <a href="panel.php"><button class="boton-cerrar">X</button></a>
        <div class="bloque-verde"><h2>Tablero</h2></div>
        <table class="contenedor-botones">
            <tr>
                <td>
                    <h3 >Insertar</h3>
                </td>
                <td>
                    <a href="new_tabimg.php"><button class="boton-tab">Imagen</button></a>
                    <a href="new_tabhtml.php"><button class="boton-tab">HTML</button></a>
                    <a href="new_tabconte.php"><button class="boton-tab">Contenidos</button></a>
                    <a href="new_tabbanner.php"><button class="boton-tab">Crear Banner</button></a>
                    <a href="new_tabapps.php"><button class="boton-tab">Apps</button></a>
                    <a href="new_tabventana.php"><button class="boton-tab">Vetanas</button></a>
                </td>
            </tr>
        </table>
       
        <table class="tableborderfull">
        <tr>
            <td>||</td>
            <td>Item</td>
            <td>Tipo</td>
            <td>Tamaño</td>
            <td>Ubicacion</td>
            <td>Orden</td>
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
                echo "<td>" . htmlspecialchars($row["formu"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["altura"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["ubicacion"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["orden"]) . "</td>";
                echo "<td>";
                
                // Obtener valores
                $nombre = urlencode($row["nombre"]);
                $id = urlencode($row["id"]); // Capturar el código
            
                $botones = [
                    ["pagina" => "editccion.php", "imagen" => "https://i.ibb.co/nNQjXb7b/wp-editar.png"],
                    ["pagina" => "panel.php", "imagen" => "https://i.ibb.co/S7Gq2mpG/ws-fotos.png"],
                    ["pagina" => "conect/eliminar_elemento.php", "imagen" => "https://i.ibb.co/LdTnB39W/wp-borrar.png"]
                ];
    
                foreach ($botones as $index => $boton) {
                    echo "<a href='{$boton['pagina']}?id=$id' class='btn_st'>
                            <img src='{$boton['imagen']}' alt='Botón' style='width: 25px; height: 25px; vertical-align: middle;'>
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