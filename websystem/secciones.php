<?php
include 'conect/conexion.php';
// Inclusión de información
include('estilo/data.php');
include('estilo/header.php');
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
                $codtab = urlencode($row["codtab"] ?? ''); 
                $nombre = urlencode($row["nombre"]); 
                $nv = urlencode($row["Num_nivel"] ?? '');  
            
                // Definir las páginas y sus parámetros específicos
                $botones = [
                    "editccion.php" => ["cod", "codtab", "nombre"],
                    "subseccion.php" => ["cod", "codtab", "nombre", "nv"],
                    "seccionpagina.php" => ["cod"],
                    "secciondetalle.php" => ["cod"],
                    "conect/eliminar_elemento.php" => ["cod", "codtab"]
                ];
            
                $imagenes = [
                    "editccion.php" => "https://i.ibb.co/nNQjXb7b/wp-editar.png",
                    "subseccion.php" => "https://i.ibb.co/hPQ0zQ5/ws-menu.png",
                    "seccionpagina.php" => "https://i.ibb.co/VYrngfWv/wp-page.png",
                    "secciondetalle.php" => "https://i.ibb.co/Fq6n7h1M/wp-tools.png",
                    "conect/eliminar_elemento.php" => "https://i.ibb.co/LdTnB39W/wp-borrar.png"
                ];
            
                foreach ($botones as $pagina => $parametros) {
                    // Construir URL con los parámetros adecuados
                    $url = "$pagina?";
                    $params = [];
            
                    if (in_array("cod", $parametros)) {
                        $params[] = "cod=$cod";
                    }
                    if (in_array("codtab", $parametros) && !empty($codtab)) {
                        $params[] = "codtab=$codtab";
                    }
                    if (in_array("nombre", $parametros) && !empty($nombre)) {
                        $params[] = "nombre=$nombre";
                    }
                    if (in_array("nv", $parametros) && !empty($nv)) {
                        $params[] = "nv=$nv";
                    }
            
                    $url .= implode("&", $params);
            
                    // Ajustar tamaño de los botones
                    $width = "25px";
                    $height = ($pagina === "subseccion.php") ? "15px" : "25px";
            
                    echo "<a href='$url' class='btn_st'>
                            <img src='{$imagenes[$pagina]}' alt='Botón' style='width: $width; height: $height; vertical-align: middle;'>
                          </a> ";
                }
            }
        }
        ?>

    </table>
</div>

<?php
// Incluir el footer.php
include('estilo/footer.php');
?>
