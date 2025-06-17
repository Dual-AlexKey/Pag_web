<?php
include 'conect/conexion.php';
include('estilo/data.php');
include('estilo/header.php');
include('estilo/menu.php');
include('estilo/tabla_menu.php');

$menu = [
    'nombre'   => '',
    'link'     => '',
    'modulo'   => '',
    'estilos'  => '',
    'publicar' => [],
    'secciones' => ''
];

$tablas_menu = [];
$query = "SHOW TABLES LIKE 'menu_%'";
$resultado_tablas = mysqli_query($conexion, $query);

while ($fila = mysqli_fetch_row($resultado_tablas)) {
    $tablas_menu[] = $fila[0];
}

// Recoger valores de la URL
$nombre = isset($_GET['nombre']) ? htmlspecialchars(trim($_GET['nombre'])) : '';
$accion = isset($_GET['accion']) ? htmlspecialchars(trim($_GET['accion'])) : '';
$codp = isset($_GET['codpS']) ? htmlspecialchars(trim($_GET['codpS'])) : '';
$id = isset($_GET['idSub']) ? htmlspecialchars(trim($_GET['idSub'])) : '';

$seccion_principal = '';
$datos_subnivel = [];

// ✅ 1. Buscar en subnivel si accion=subseccion y cod existe
if ($accion === 'subseccion' && !empty($codp)) {
    $sql_subnivel = "SELECT * FROM subnivel WHERE codp = ?";
    $stmt_subnivel = $conexion->prepare($sql_subnivel);
    if ($stmt_subnivel) {
        $stmt_subnivel->bind_param("s", $codp);
        $stmt_subnivel->execute();
        $resultado_subnivel = $stmt_subnivel->get_result();
        if ($resultado_subnivel->num_rows > 0) {
            $datos_subnivel = $resultado_subnivel->fetch_assoc();
            // ✅ Rellenar menú
            $menu['nombre'] = $datos_subnivel['nombre'] ?? '';
            $menu['link'] = $datos_subnivel['link'] ?? '';
            $menu['modulo'] = $datos_subnivel['modulo'] ?? '';
            $menu['estilos'] = $datos_subnivel['estilos'] ?? '';
            $menu['secciones'] = $datos_subnivel['secciones'] ?? '';
        }
        $stmt_subnivel->close();
    }
}

// ✅ 2. Buscar nombre en tabla subnivel y usar secciones si coincide
$seccion_base = '';
$sql_check_nombre = "SELECT secciones FROM subnivel WHERE nombre = ? LIMIT 1";
$stmt_nombre = $conexion->prepare($sql_check_nombre);
if ($stmt_nombre) {
    $stmt_nombre->bind_param("s", $nombre);
    $stmt_nombre->execute();
    $stmt_nombre->store_result();
    if ($stmt_nombre->num_rows > 0) {
        $stmt_nombre->bind_result($seccion_subnivel);
        $stmt_nombre->fetch();
        $seccion_base = $seccion_subnivel;
    }
    $stmt_nombre->close();
}

// ✅ Siempre incluir el nombre del URL
if ($accion === 'subseccion') {
    // Mostrar solo el valor guardado en secciones
    $seccion_principal = $menu['secciones'] ?? '';
    if (empty($seccion_principal)) {
        $seccion_principal = '/'; // o lo que desees como fallback
    }
} else {
    // Armar Sección Principal combinando secciones + nombre
    $seccion_principal = rtrim($seccion_base, '/') . '/' . $nombre;
    if (empty($seccion_base)) {
        $seccion_principal = '/' . $nombre;
    }
}

$estiloSeleccionado = $menu['estilos'] ?? '';
$moduloSeleccionado = $menu['modulo'] ?? '';
?>      

<div class="contenido-derecha">
    <a href="secciones.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde">
    <h2><?= ($accion === 'subseccion') ? 'Editar Subseccion' : 'Nueva Subseccion'; ?></h2>
</div>
    <div id="capaformulario">
        <form id="miFormulario" action="conect/guardar_tablero.php" method="post">
            <input type="hidden" name="formulario_tipo" value="Subseccion">
            <input type="hidden" name="nameold" value="<?php echo isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : ''; ?>">
            <input type="hidden" name="estructsecc" value="Estilo Derecha">
            <input type="hidden" name="orden" value="12">
            <input type="hidden" name="cod" value="<?php echo isset($_GET['cod']) ? htmlspecialchars($_GET['cod']) : ''; ?>">
            
            
            <table class="tableborderfull">
                <tr>
                    <td class="colgrishome">Sección Principal:</td>
                    <td class="colblancocen">
                        <input id="secciones" name="secciones" type="hidden" value="<?= htmlspecialchars($seccion_principal); ?>">
                        <span class="negrita" style="font-weight: 900;"><?= htmlspecialchars($seccion_principal); ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Nombre:</td>
                    <td class="colblancocen">
                        <input type="text" id="nombre" name="nombre" style="width: 50%;" required oninput="actualizarURL()" value="<?= htmlspecialchars($menu['nombre']) ?>">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">URL:</td>
                    <td class="colblancocen">
                        <input type="text" id="link" name="link" style="width: 50%;" required readonly value="<?= htmlspecialchars($menu['link']) ?>">
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Módulo:</td>
                    <td class="colblancocen">
                        <select id="modulo" name="modulo" style="width: 30%;" required onchange="cambiarEstilos()">
                            <option value="Contenidos" <?= $moduloSeleccionado == 'Contenidos' ? 'selected' : '' ?>>Contenidos</option>
                            <option value="Catalogo" <?= $moduloSeleccionado == 'Catalogo' ? 'selected' : '' ?>>Catálogo</option>
                            <option value="Usuarios" <?= $moduloSeleccionado == 'Usuarios' ? 'selected' : '' ?>>Usuarios</option>
                            <option value="Formularios" <?= $moduloSeleccionado == 'Formularios' ? 'selected' : '' ?>>Formularios</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Estilos:</td>
                    <td class="colblancocen">
                        <div style="display: flex; gap: 20px; align-items: flex-start;" id="estilos" 
                            data-seleccionado="<?= htmlspecialchars($estiloSeleccionado) ?>">
                            <!-- Aquí se cargarán dinámicamente los estilos -->
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="colgrishome">Publicar en Menú:</td>
                    <td class="colblancocen">
                    <?php if (!empty($tablas_menu)): ?>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <?php foreach ($tablas_menu as $index => $tabla_menu): ?>
                                <?php
                                // Omitir la tabla 'menu_sinselect'
                                if ($tabla_menu === 'menu_sinselect') {
                                    continue;
                                }

                                // Limpieza del nombre de la tabla
                                $menu_limpio = preg_replace('/^menu_/', '', $tabla_menu);
                                $ubicaciones = ['cabecerat', 'pie', 'cabeceral', 'cabeceram', 'columnai', 'columnad'];

                                foreach ($ubicaciones as $ubicacion) {
                                    $menu_limpio = preg_replace('/_' . preg_quote($ubicacion, '/') . '$/', '', $menu_limpio);
                                }

                                // Verificar si el registro existe en la tabla
                                $checked = '';
                                if ($accion === 'subseccion') {
                                    $sql_check = "SELECT COUNT(*) FROM $tabla_menu WHERE nombre = ?";
                                    $stmt_check = $conexion->prepare($sql_check);
                                    if ($stmt_check) {
                                        $stmt_check->bind_param("s", $nombre);
                                        $stmt_check->execute();
                                        $stmt_check->bind_result($existe);
                                        $stmt_check->fetch();
                                        $stmt_check->close();
                                        if ($existe > 0) {
                                            $checked = 'checked';
                                        }
                                    }
                                }
                                ?>
                                <label style="display: flex; align-items: center;">
                                    <input type="checkbox" id="publicar_<?= $index; ?>" name="publicar[]" value="<?= $tabla_menu; ?>" <?= $checked; ?>>
                                    <span style="margin-left: 5px;"><?= htmlspecialchars($menu_limpio); ?></span>
                                </label>
                                <?php if (($index + 1) % 3 == 0): ?>
                                    <br>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No hay menús disponibles.</p>
                    <?php endif; ?>
                    </td>
                </tr>
            </table>
            <div class="boton-container">
                <button name="aceptar" class="botonesAyC" type="submit" onclick="crearArchivo(event)">Aceptar</button>
                <button name="Cancelar" class="botonesAyC" type="button" onclick="window.location = 'secciones.php'">Cancelar</button>
            </div>
        </form>
    </div>
</div>
<?php
// Incluir el footer.php
include('estilo/footer.php');
?>
