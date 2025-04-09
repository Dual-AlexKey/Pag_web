<?php 
include 'conect/conexion.php';
include('estilo/header.php');
include('estilo/menu.php');

// Inicializamos variables
$region = $_GET['region'] ?? '';
$nombre = $_GET['nombre'] ?? '';
$documento = $_GET['documento'] ?? '';
$correo = $_GET['correo'] ?? '';
$fecha_final = $_GET['fecha_final'] ?? '';
$orden = $_GET['orden'] ?? 'nombre';

?>

<div class="contenido-derecha">
    <a href="panel.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Usuarios</h2></div>

    <!-- Formulario de búsqueda -->
<!-- filepath: c:\xampp\htdocs\hub\websystem\administracion.php -->
<form method="GET">
    <table class="contenedor-botones" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td class="titlehome" style="width: 15%; text-align: left;">
                Región<br>
                <select name="region" id="region" class="form-control">
                    <option value="00000000" selected="">Todos</option>
                    <option value="PE010000">AMAZONAS</option>
                    <option value="PE020000">ANCASH</option>
                    <option value="PE030000">APURIMAC</option>
                    <option value="PE040000">AREQUIPA</option>
                    <option value="PE050000">AYACUCHO</option>
                    <option value="PE060000">CAJAMARCA</option>
                    <option value="PE070000">CALLAO</option>
                    <option value="PE080000">CUSCO</option>
                    <option value="PE090000">HUANCAVELICA</option>
                    <option value="PE100000">HUANUCO</option>
                    <option value="PE110000">ICA</option>
                    <option value="PE120000">JUNIN</option>
                    <option value="PE130000">LA LIBERTAD</option>
                    <option value="PE140000">LAMBAYEQUE</option>
                    <option value="PE150000">LIMA</option>
                    <option value="PE160000">LIMA PROVINCIAS</option>
                    <option value="PE170000">LORETO</option>
                    <option value="PE180000">MADRE DE DIOS</option>
                    <option value="PE190000">MOQUEGUA</option>
                    <option value="PE200000">PASCO</option>
                    <option value="PE210000">PIURA</option>
                    <option value="PE220000">PUNO</option>
                    <option value="PE230000">SAN MARTIN</option>
                    <option value="PE240000">TACNA</option>
                    <option value="PE250000">TUMBES</option>
                    <option value="PE260000">UCAYALI</option>
                </select>
            </td>
            <td class="titlehome" style="width: 15%; text-align: left;">
                Nombre<br>
                <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" class="form-control">
            </td>
            <td class="titlehome" style="width: 15%; text-align: left;">
                Documento<br>
                <input type="text" name="documento" value="<?= htmlspecialchars($documento) ?>" class="form-control">
            </td>
            <td class="titlehome" style="width: 15%; text-align: left;">
                Correo Electrónico<br>
                <input type="text" name="correo" value="<?= htmlspecialchars($correo) ?>" class="form-control">
            </td>
            <td class="titlehome" style="width: 15%; text-align: left;">
                Fecha<br>
                <input type="date" name="fecha_final" value="<?= htmlspecialchars($fecha_final) ?>" class="form-control">
            </td>
            <td class="titlehome" style="width: 15%; text-align: left;">
                Ordenar por<br>
                <select name="orden" class="form-control">
                    <option value="nombre" <?= $orden == 'nombre' ? 'selected' : '' ?>>Nombre</option>
                    <option value="documento" <?= $orden == 'documento' ? 'selected' : '' ?>>Documento</option>
                    <option value="correo" <?= $orden == 'correo' ? 'selected' : '' ?>>Correo</option>
                    <option value="fecha" <?= $orden == 'fecha' ? 'selected' : '' ?>>Fecha Aniversario</option>
                </select>
            </td>
        </tr>
    </table>

    <div style="margin: 10px 0; text-align: center;">
            <button type="submit" class="botonesAyC">Buscar</button>
            <button type="button" class="botonesAyC" onclick="window.location.href='new_user.php'">Nuevo Usuario</button>
    </div>
</form>

    <!-- Tabla de resultados -->
    <table class="tableborderfull" style="width: 100%; border-collapse: collapse;">
        <tr>
            <th style="width: 2%; text-align: left; padding-left: 10px;">||</th>
            <th style="width: 30%; text-align: left; padding-left: 10px;">Nombre</th>
            <th style="width: 20%; text-align: left; padding-left: 10px;">Correo</th>
            <th style="width: 15%; text-align: left; padding-left: 10px;">Documento</th>
            <th style="width: 15%; text-align: left; padding-left: 10px;">Fecha</th>
            <th style="width: 10%; text-align: left; padding-left: 10px;">Teléfono</th>
            <th style="width: 10%; text-align: left; padding-left: 10px;">Móvil</th>
            <th style="width: 10%; text-align: center;" colspan="3">Opciones</th>
        </tr>

        <?php
        // Construimos el filtro dinámico
        $where = "WHERE 1=1";
        if ($region) $where .= " AND region LIKE '%" . $conn->real_escape_string($region) . "%'";
        if ($nombre) $where .= " AND nombres LIKE '%" . $conn->real_escape_string($nombre) . "%'";
        if ($documento) $where .= " AND documento LIKE '%" . $conn->real_escape_string($documento) . "%'";
        if ($correo) $where .= " AND correo LIKE '%" . $conn->real_escape_string($correo) . "%'";
        if ($fecha_final) $where .= " AND fecha <= '" . $conn->real_escape_string($fecha_final) . "'";

        // Ordenar
        $ordenCol = match($orden) {
            'nombre' => 'nombres',
            'documento' => 'documento',
            'correo' => 'correo',
            'fecha' => 'fecha',
            default => 'nombres'
        };

        $sql = "SELECT * FROM log $where ORDER BY $ordenCol ASC";
        $res = $conn->query($sql);

        while ($row = $res->fetch_assoc()) {
            echo "<tr>";
            echo "<td>||</td>";
            echo "<td>" . htmlspecialchars($row['nombres']) . "</td>";
            echo "<td>" . htmlspecialchars($row['correo']) . "</td>";
            echo "<td>" . htmlspecialchars($row['documento']) . "</td>";
            echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";
            echo "<td>" . htmlspecialchars($row['telefono']) . "</td>";
            echo "<td>" . htmlspecialchars($row['movil']) . "</td>";
            echo "<td>
                    <a href='editar_usuario.php?id={$row['id']}' class='btn_st'>
                        <img src='https://i.ibb.co/nNQjXb7b/wp-editar.png' alt='Botón Editar' style='width: 25px; height: 25px; vertical-align: middle; padding-right: 5px;'>
                    </a>
                </td>";
            echo "<td>
                <a href='con.php?id={$row['id']}' class='btn_st'>
                    <img src='https://i.ibb.co/V0BcC0nq/ws-clave.png' alt='Botón con' style='width: 25px; height: 25px; vertical-align: middle; padding-right: 5px;'>
                </a>
            </td>";
            echo "<td>
                    <a href='eliminar_usuario.php?id={$row['id']}' class='btn_st'>
                        <img src='https://i.ibb.co/LdTnB39W/wp-borrar.png' alt='Botón Eliminar' style='width: 25px; height: 25px; vertical-align: middle; padding-right: 5px;'>
                    </a>
                </td>";
            echo "</tr>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

<?php include('estilo/footer.php'); ?>
