<?php
include 'conect/conexion.php';
// Incluir el header.php
include('estilo/header.php');
// Incluir el menu.php
include('estilo/menu.php');

?>
    <div class="contenido-derecha">
        <a href="panel.php"><button class="boton-cerrar">X</button></a>
        <div class="bloque-verde"><h2>Usuarios</h2></div>
        <table class="contenedor-botones">
            <tr>
                <td class="colgrishome">Región</td>
                <td class="colblancocen">
                    <select name="dpto" id="dpto" style="width: 30%;">
                        <option value="">Seleccione un departamento</option>
                        <option value="peru">peru</option>
                    </select>
                </td>
                <td class="colgrishome">Nombre</td>
                <td class="colblancocen"><input type="text" name="empresa"></td>
                <td class="colgrishome">Documento</td>
                <td class="colblancocen"><input type="text" name="empresa"></td>
                <td class="colgrishome">Correo Electronico</td>
                <td class="colblancocen"><input type="text" name="empresa"></td>
                <td class="colgrishome">Fecha Final:</td>
                <td class="colblancocen">
                    <input type="date" id="fecha_final" name="fecha_final" value="<?= htmlspecialchars($datos['fecha_final']) ?>">
                </td>
                <td class="colgrishome">Ordenar por:</td>
                <td class="colblancocen">
                    <select id="categoria" name="categoria" style="width: 30%;">
                        <?php
                        $categorias = ["nombre" => "Nombres", "DOC" => "Documento", "Fecha" => "Fecha de registro", "Correo" => "Correo Electronico"];
                        foreach ($categorias as $key => $label) {
                            $selected = (isset($datos['categoria']) && $datos['categoria'] == $key) ? 'selected' : '';
                            echo "<option value='$key' $selected>$label</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
       
        <table class="tableborderfull" style="width: 100%; border-collapse: collapse;">
        <tr>
            <th style="width: 2%; text-align: left; padding: 10px 10px;" >||</th>
            <th style="width: 60%; text-align: left; padding: 10px 10px;">Usuario</th>
            <th style="width: 10%; text-align: left; padding: 10px 10px;">Correo</th>
            <th style="width: 10%; text-align: left; padding: 10px 10px;">Cargo</th>
            <th style="width: 10%; text-align: left; padding: 10px 10px;">Documento</th>
            <th style="width: 10%; text-align: left; padding: 10px 10px;">Telefono</th>
            <th style="width: 10%; text-align: left; padding: 10px 10px;">Movil</th>
            <th style="width: 10%; text-align: left; padding: 10px 10px;">Fec. Registro</th>
            <th style=" text-align: center;" colspan="5">Opciones</th>
        </tr>

        <?php
            foreach ($menu_tables as $table) {
                // Consultar datos de cada tabla encontrada
                $sql = "SELECT * FROM `$table`";
                $result = $conn->query($sql);
            
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>||</td>";
                    
                    // Crear un enlace en el nombre (Item) con estilo negro
                    $id = urlencode($row["id"]); // Capturar el ID
                    $tp = urlencode($row["formu"]); // Capturar el tipo de formulario
                    $paginas = [
                        "Imagen" => "new_tabimg.php",
                        "HTML" => "new_tabhtml.php",
                        "Apps" => "new_tabapps.php",
                        "Banner" => "new_tabbanner.php",
                        "Ventana" => "new_tabventana.php",
                        "Contenidos" => "new_tabconte.php"
                    ];
                    $pagina_destino = isset($paginas[$tp]) ? $paginas[$tp] : "new_default.php";
            
                    // Hacer que "nombre" sea un enlace negro
                    $nombre = htmlspecialchars($row["nombre"]);
                    echo "<td><a href='{$pagina_destino}?id=$id' class='item-link' style='text-decoration: none; color: black;'>$nombre</a></td>";
            
                    echo "<td>" . htmlspecialchars($row["formu"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["altura"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["ubicacion"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["orden"]) . "</td>";
            
                    // Botón "Editar"
                    echo "<td><a href='{$pagina_destino}?id=$id' class='btn_st'>
                            <img src='https://i.ibb.co/nNQjXb7b/wp-editar.png' alt='Editar' style='width: 25px; height: 25px; vertical-align: middle;'>
                        </a></td>";
            
                    // Botón "Fotos" (condicional)
                    if ($tp === "Banner" || $tp === "Contenidos") {
                        echo "<td><a href='new_img.php?id=$id' class='btn_st'>
                                <img src='https://i.ibb.co/S7Gq2mpG/ws-fotos.png' alt='Fotos' style='width: 25px; height: 25px; vertical-align: middle;'>
                            </a></td>";
                    } else {
                        echo "<td><a></a></td>"; // Sin enlace si no es aplicable
                    }
            
                    // Botón "Eliminar"
                    echo "<td><a href='conect/eliminar_elemento.php?id=$id' class='btn_st'>
                            <img src='https://i.ibb.co/LdTnB39W/wp-borrar.png' alt='Eliminar' style='width: 25px; height: 25px; vertical-align: middle;'>
                        </a></td>";
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

