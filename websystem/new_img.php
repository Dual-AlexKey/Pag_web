<?php
include 'conect/conexion.php';
include('estilo/data.php'); // Información adicional
include('estilo/header.php'); // Header
include('estilo/menu.php'); // Menú
?>
<div class="contenido-derecha">
    <a href="panel.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Imágenes</h2></div>
    <a href="new_itemimg.php"><button class="boton-nvpag">Nueva sección</button></a>
    <div class="bloque-gris"><h3>Insertar</h3></div>

    <table class="tableborderfull">
        <tr>
            <th style="width: 2%; text-align: left; padding: 10px 10px;">||</th>
            <th style="width: 60%; text-align: left; padding: 10px 10px;">Item</th>
            <th style="width: 10%; text-align: left; padding: 10px 10px;">Orden</th>
            <th style="width: 10%; text-align: left; padding: 10px 10px;">Tipo</th>
            <th style="width: 10%; text-align: left; padding: 10px 10px;">Pos. Hor</th>
            <th style="width: 10%; text-align: left; padding: 10px 10px;">Pos. Ver</th>
            <th style="width: 10%; text-align: left; padding: 10px 10px;">Ingreso desde</th>
            <th style="width: 10%; text-align: left; padding: 10px 10px;">Duración</th>
            <th style="width: 5%; text-align: center;" colspan="5">Opciones</th>
        </tr>

        <?php
        $sql = "SELECT * FROM Imagenes ORDER BY orden ASC"; // Consulta principal
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id_img = $row["id_img"];
                $nombre = !empty($row["nombre"]) ? htmlspecialchars($row["nombre"]) : "--";
                $orden = !empty($row["orden"]) ? htmlspecialchars($row["orden"]) : "--";
                $duracion = !empty($row["altura"]) ? htmlspecialchars($row["altura"]) . " ms" : "--";

                // Mostrar fila principal (padre)
                echo "<tr>";
                echo "<td>||</td>";
                echo "<td>$nombre</td>";
                echo "<td>$orden</td>";
                echo "<td>--</td>"; // Tipo (vacío para el padre)
                echo "<td>--</td>"; // Pos. Hor (vacío para el padre)
                echo "<td>--</td>"; // Pos. Ver (vacío para el padre)
                echo "<td>--</td>"; // Ingreso desde (vacío para el padre)
                echo "<td>$duracion</td>";
                echo "<td style='text-align: center;'>
                        <a href='new_itemimg.php?id=$id_img'>
                            <img src='https://i.ibb.co/nNQjXb7b/wp-editar.png' alt='Editar' style='width: 20px; height: 20px;'>
                        </a>
                      </td>";
                echo "<td style='text-align: center;'>
                        <a href='conect/eliminar_elemento.php?id_img=$id_img' onclick=\"return confirm('¿Eliminar esta imagen?');\">
                            <img src='https://i.ibb.co/LdTnB39W/wp-borrar.png' alt='Borrar' style='width: 20px; height: 20px;'>
                        </a>
                      </td>";
                echo "<td style='text-align: center;'>
                        <a href='new_elementoimg.php?id=$id_img&nombre=$nombre'>
                            <img src='https://i.ibb.co/S7Gq2mpG/ws-fotos.png' alt='Nuevo' style='width: 20px; height: 20px;'>
                        </a>
                      </td>";
                echo "</tr>";

                // Consultar resultados de la tabla "imagenes2" relacionados con el registro actual
                $sql_child = "SELECT * FROM imagenes2 WHERE padre = ?";
                if ($stmt = $conn->prepare($sql_child)) {
                    $stmt->bind_param("s", $nombre);
                    $stmt->execute();
                    $child_result = $stmt->get_result();

                    if ($child_result->num_rows > 0) {
                        while ($child = $child_result->fetch_assoc()) {
                            $child_id = $child["id_img"];
                            $child_nombre = htmlspecialchars($child["titulo"]);
                            $child_tipo = htmlspecialchars($child["tipo"]);
                            $child_posX = htmlspecialchars($child["PosX"]);
                            $child_posY = htmlspecialchars($child["PosY"]);

                            // Mostrar fila hijo
                            echo "<tr style='background-color: #f9f9f9;'>
                                    <td>↳</td>
                                    <td style='padding-left: 30px;'>$child_nombre</td>
                                    <td>--</td>
                                    <td>$child_tipo</td>
                                    <td>$child_posX</td>
                                    <td>$child_posY</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td style='text-align: center;'>
                                        <a href='new_elementoimg.php?id=$child_id'>
                                            <img src='https://i.ibb.co/nNQjXb7b/wp-editar.png' alt='Editar' style='width: 20px; height: 20px;'>
                                        </a>
                                    </td>
                                    <td style='text-align: center;'>
                                        <a href='conect/eliminar_elemento.php?id_img=$child_id' onclick=\"return confirm('¿Eliminar esta imagen?');\">
                                            <img src='https://i.ibb.co/LdTnB39W/wp-borrar.png' alt='Borrar' style='width: 20px; height: 20px;'>
                                        </a>
                                    </td>
                                    <td></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr style='background-color: #f9f9f9;'>
                                <td>↳</td>
                                <td style='padding-left: 30px;'>No hay resultados</td>
                                <td colspan='9'>--</td>
                              </tr>";
                    }
                    $stmt->close();
                }
            }
        } else {
            echo "<tr><td colspan='11'>No hay imágenes registradas.</td></tr>";
        }

        $conn->close();
        ?>
    </table>
</div>

<?php
include('estilo/footer.php'); // Footer
?>