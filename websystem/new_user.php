<?php
include 'conect/conexion.php';
// Inclusión de información
include('estilo/data.php');

// Incluir el header.php
include('estilo/header.php');

// Incluir el menu.php
include('estilo/menu.php');

// Inicializar variables vacías
$nombres = '';
$correo = '';
$documento = '';
$fecha_aniversario = '';
$sexo = '';
$perfil = '';
$pais = '';
$dpto = '';
$city = '';
$direccion = '';
$telefono = '';
$movil = '';

// Verificar si el ID está presente en la URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']); // Asegurarse de que el ID sea un número entero

    // Consultar los datos del usuario en la base de datos
    $sql = "SELECT * FROM log WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Recuperar los datos del usuario
        $row = $result->fetch_assoc();
        $nombres = $row['nombres'];
        $correo = $row['correo'];
        $documento = $row['documento'];
        $fecha_aniversario = $row['fecha'];
        $sexo = $row['sexo'];
        $perfil = $row['perfil'];
        $pais = $row['pais'];
        $dpto = $row['dpto'];
        $city = $row['city'];
        $direccion = $row['direccion'];
        $telefono = $row['telefono'];
        $movil = $row['movil'];
    }

    $stmt->close();
}
// Generar token CSRF
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<div class="contenido-derecha">
    <a href="administracion.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Datos Básicos</h2></div>
    <form action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="formulario_tipo" value="User"> 
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <table class="tableborderfull">
            <tr>
                <td class="colgrishome">Nombres:</td>
                <td class="colblancocen">
                    <input type="text" name="nombres" style="width: 50%;" value="<?= htmlspecialchars($nombres) ?>" required>
                </td>
            </tr>
            <tr>
                <td class="colgrishome">Correo electrónico:</td>
                <td class="colblancocen">
                    <input type="email" name="correo" style="width: 50%;" value="<?= htmlspecialchars($correo) ?>" required>
                </td>
            </tr>
            <tr>
                <td class="colgrishome">Documento Identidad:</td>
                <td class="colblancocen">
                    <input type="text" name="documento" style="width: 50%;" value="<?= htmlspecialchars($documento) ?>" required>
                </td>
            </tr>
            <tr>
                <td class="colgrishome">Fecha Aniversario:</td>
                <td class="colblancocen">
                    <input type="date" name="fecha_aniversario" style="width: 50%;" value="<?= htmlspecialchars($fecha_aniversario) ?>" required>
                </td>
            </tr>
            <tr>
                <td class="colgrishome">Sexo:</td>
                <td class="colblancocen">
                    <select name="sexo" style="width: 30%;" required>
                        <option value="F" <?= $sexo === 'F' ? 'selected' : '' ?>>Femenino</option>
                        <option value="M" <?= $sexo === 'M' ? 'selected' : '' ?>>Masculino</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="colgrishome">Perfil:</td>
                <td class="colblancocen">
                    <select name="perfil" style="width: 30%;" required>
                        <option value="Operador" <?= $perfil === 'Operador' ? 'selected' : '' ?>>Operador</option>
                        <option value="Administrador" <?= $perfil === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </td>
            </tr>
        </table>

        <div class="bloque-verde"><h2>Datos de Contacto</h2></div>
        <div class="columna-formulario">
            <table class="tableborderfull">
                <tr>
                    <td class="colgrishome">País</td>
                    <td class="colblancocen">
                        <select name="pais" id="pais" style="width: 30%;">
                            <option value="">Seleccione un país</option>
                            <option value="peru" <?= ($pais == 'peru') ? 'selected' : '' ?>>Perú</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Región / Departamento</td>
                    <td class="colblancocen">
                        <select name="dpto" id="dpto" style="width: 30%;">
                             <option value="">Seleccione un departamento</option>
                            <?php if (!empty($empresa['dpto'])): ?>
                               <option value="<?= htmlspecialchars($dpto) ?>" selected><?= htmlspecialchars($dpto) ?></option>
                            <?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Ciudad / Provincia</td>
                    <td class="colblancocen">
                        <select name="city" id="city" style="width: 30%;">
                            <option value="">Seleccione una provincia</option>
                            <?php if (!empty($empresa['city'])): ?>
                                <option value="<?= htmlspecialchars($city) ?>" selected><?= htmlspecialchars($city) ?></option>
                            <?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Dirección:</td>
                    <td class="colblancocen">
                        <input type="text" name="direccion" style="width: 50%;" value="<?= htmlspecialchars($direccion) ?>" required>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Teléfono:</td>
                    <td class="colblancocen">
                        <input type="text" name="telefono" style="width: 50%;" value="<?= htmlspecialchars($telefono) ?>">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Móvil:</td>
                    <td class="colblancocen">
                        <input type="text" name="movil" style="width: 50%;" value="<?= htmlspecialchars($movil) ?>">
                    </td>
                </tr>
            </table>

            <div class="boton-container">
                <button name="aceptar" class="botonesAyC" type="submit">Aceptar</button>
                <button name="cancelar" class="botonesAyC" type="button" onclick="window.location = 'secciones.php'">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<?php
// Incluir el footer.php
include('estilo/footer.php');
?>