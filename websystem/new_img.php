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
    <div class="bloque-verde"><h2>Imágenes</h2></div>
    <a href="new_itemimg.php"><button class="boton-nvpag">Nueva sección</button></a>
    <div class="bloque-gris"><h3>Insertar</h3></div>

    <table class="tableborderfull">
        <tr>
            <td>||</td>
            <td>Item: /</td>
            <td>Orden</td>
            <td>Tipo</td>
            <td>Pos. Hor</td>
            <td>Pos. Ver</td>
            <td>Ingreso desde</td>
            <td>Duración</td>
            <td>Opciones</td>
        </tr>

        <?php

        $sql = "SELECT * FROM Imagenes ORDER BY orden ASC"; // 🔹 Obtener imágenes ordenadas
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $nombre = !empty($row["nombre"]) ? htmlspecialchars($row["nombre"]) : "--";
                $orden = !empty($row["orden"]) ? htmlspecialchars($row["orden"]) : "--";
                $duracion = !empty($row["altura"]) ? htmlspecialchars($row["altura"]) . " ms" : "--";

                echo "<tr>";
                echo "<td>||</td>";
                echo "<td>$nombre</td>";
                echo "<td>$orden</td>";
                echo "<td>--</td>"; // Tipo (vacío)
                echo "<td>--</td>"; // Pos. Hor (vacío)
                echo "<td>--</td>"; // Pos. Ver (vacío)
                echo "<td>--</td>"; // Ingreso desde (vacío)
                echo "<td>$duracion</td>";
                echo "<td style='display: flex; align-items: center; gap: 5px;'>
                        <a href='edit_itemimg.php?id=" . $row["id"] . "'>
                            <img src='https://i.ibb.co/nNQjXb7b/wp-editar.png' alt='Editar' style='width: 20px; height: 20px;'>
                        </a>
                        <a href='conect/eliminar_imagen.php?id=" . $row["id"] . "' onclick=\"return confirm('¿Eliminar esta imagen?');\">
                            <img src='https://i.ibb.co/LdTnB39W/wp-borrar.png' alt='Borrar' style='width: 20px; height: 20px;'>
                        </a>
                        <a href='ver_imagenes.php?id=" . $row["id"] . "'>
                            <img src='https://i.ibb.co/Fq6n7h1M/wp-tools.png' alt='Imágenes' style='width: 20px; height: 20px;'>
                        </a>
                        </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No hay imágenes registradas.</td></tr>";
        }

        $conn->close();
        ?>

    </table>
</div>

<?php
// Incluir el footer.php
include('estilo/footer.php');
?>