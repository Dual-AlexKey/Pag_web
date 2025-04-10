<?php
include 'conect/conexion.php';
include('estilo/data.php');
include('estilo/header.php');
include('estilo/menu.php');

// Inicializar variables
$correo = $nombres = $documento = $fecha = '';
$id = 0;

// Verificar si hay un ID en la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "SELECT correo, nombres, documento, fecha FROM log WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $correo = $row['correo'];
        $nombres = $row['nombres'];
        $documento = $row['documento'];
        $fecha = $row['fecha'];
    } else {
        die("❌ Usuario no encontrado.");
    }

    $stmt->close();
} else {
    die("❌ ID inválido.");
}

// Generar token CSRF
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<div class="contenido-derecha">
    <a href="administracion.php"><button class="boton-cerrar">X</button></a>

    <!-- Datos Básicos -->
    <div class="bloque-verde"><h2>Datos Básicos</h2></div>
    <table class="tableborderfull">
        <tr>
            <td class="colgrishome">Correo electrónico:</td>
            <td class="colblancocen"><?= htmlspecialchars($correo) ?></td>
        </tr>
        <tr>
            <td class="colgrishome">Nombres:</td>
            <td class="colblancocen"><?= htmlspecialchars($nombres) ?></td>
        </tr>
        <tr>
            <td class="colgrishome">Documento Identidad:</td>
            <td class="colblancocen"><?= htmlspecialchars($documento) ?></td>
        </tr>
        <tr>
            <td class="colgrishome">Fecha:</td>
            <td class="colblancocen"><?= htmlspecialchars($fecha) ?></td>
        </tr>
    </table>

    <!-- Formulario de cambio de contraseña -->
    <form action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="formulario_tipo" value="User">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <div class="bloque-verde"><h2>Cambiar Contraseña</h2></div>
        <table class="tableborderfull">
            <tr>
                <td class="colgrishome">Nueva Contraseña:</td>
                <td class="colblancocen">
                    <input type="password" name="con" style="width: 50%;" required>
                </td>
            </tr>
            <tr>
                <td class="colgrishome">Confirmar Contraseña:</td>
                <td class="colblancocen">
                    <input type="password" name="confirmar_con" style="width: 50%;" required>
                </td>
            </tr>
        </table>

        <div class="boton-container">
            <button name="aceptar" class="botonesAyC" type="submit">Aceptar</button>
            <button name="cancelar" class="botonesAyC" type="button" onclick="window.location = 'administracion.php'">Cancelar</button>
        </div>
    </form>
</div>

<?php include('estilo/footer.php'); ?>
