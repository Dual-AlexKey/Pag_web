<?php
include 'conect/conexion.php';
// Inclusión de información
include('estilo/data.php');

// Incluir el header.php
include('estilo/header.php');

// Incluir el menu.php
include('estilo/menu.php');
// Contenido principal

?>
<div class="contenido-derecha">
    <a href="administracion.php"><button class="boton-cerrar">X</button></a>
    <div class="bloque-verde"><h2>Datos Básicos</h2></div>
    <form action="conect/guardar_tablero.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="formulario_tipo" value="User">    

    <table class="tableborderfull">
        <tr>
            <td class="colgrishome">Nombres:</td>
            <td class="colblancocen">
                <input type="text" name="nombres" style="width: 50%;" required>
            </td>
        </tr>
        <tr>
            <td class="colgrishome">Correo electrónico:</td>
            <td class="colblancocen">
                <input type="email" name="correo" style="width: 50%;" required>
            </td>
        </tr>
        <tr>
            <td class="colgrishome">Documento Identidad:</td>
            <td class="colblancocen">
                <input type="text" name="documento" style="width: 50%;" required>
            </td>
        </tr>
        <tr>
            <td class="colgrishome">Fecha Aniversario:</td>
            <td class="colblancocen">
                <input type="date" name="fecha_aniversario" style="width: 50%;" required>
            </td>
        </tr>
        <tr>
            <td class="colgrishome">Sexo:</td>
            <td class="colblancocen">
                <select name="sexo" style="width: 30%;" required>
                    <option value="F">Femenino</option>
                    <option value="M">Masculino</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="colgrishome">Perfil:</td>
            <td class="colblancocen">
                <select name="perfil" style="width: 30%;" required>
                    <option value="Operador">Operador</option>
                    <option value="Administrador">Administrador</option>
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
                        <option value="peru">Perú</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="colgrishome">Región / Departamento</td>
                <td class="colblancocen">
                    <select name="dpto" id="dpto" style="width: 30%;">
                        <option value="">Seleccione un departamento</option>
                        
                        <option ></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="colgrishome">Ciudad / Provincia</td>
                <td class="colblancocen">
                    <select name="city" id="city" style="width: 30%;">
                        <option value="">Seleccione una provincia</option>
                            <option ></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="colgrishome">Dirección:</td>
                <td class="colblancocen">
                    <input type="text" name="direccion" style="width: 50%;" required>
                </td>
            </tr>
            <tr>
                <td class="colgrishome">Teléfono:</td>
                <td class="colblancocen">
                    <input type="text" name="telefono" style="width: 50%;">
                </td>
            </tr>
            <tr>
                <td class="colgrishome">Móvil:</td>
                <td class="colblancocen">
                    <input type="text" name="movil" style="width: 50%;">
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
</div>

<?php
// Incluir el footer.php
include('estilo/footer.php');
?>