<?php
//inclusion de informacion
include('estilo/data.php');

// Incluir el header.php    
include('estilo/header.php');

// Incluir el menu.php

include('estilo/menu.php');
?>

    <div class="contenido-derecha">
        <a href="panel.php"><button class="boton-cerrar">X</button></a>
        <div class="bloque-verde"><h2>Editar configuración</h2></div>
        <div class="bloque-gris"><h3>Configuración Web</h3></div>
        
        <div id="capaformulario">
            <form action="conect/insertar_empresa.php" method="post">
                <input type="hidden" id="idcontrol">
                <table class="tableborderfull">
                    <tr>
                        <td class="colgrishome">URL Página</td>
                        <td class="colblancocen">
                            <input name="url_pagina" type="hidden" value="<?php echo $empresa['url_pagina']; ?>" readonly>
                            <span class="negrita"><?php echo $empresa['url_pagina']; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Nombre</td>
                        <td class="colblancocen">
                            <input name="nombre" type="text" value="<?php echo $empresa['nombre']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Idioma</td>
                        <td class="colblancocen" >
                            <select name="idioma">
                                <option value="0" <?php echo ($empresa['idioma'] == '0') ? 'selected' : ''; ?>>English</option>
                                <option value="1" <?php echo ($empresa['idioma'] == '1') ? 'selected' : ''; ?>>Español</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Logo</td>
                        <td class="colblancocen"><input type="text" name="logo" value="<?php echo $empresa['logo']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Favicon</td>
                        <td class="colblancocen"><input type="text" name="favicon" value="<?php echo $empresa['favicon']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="colgrishome">SEO Título</td>
                        <td class="colblancocen"><input name="seo_titulo" type="text" value="<?php echo $empresa['seo_titulo']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="colgrishome">SEO Descripción</td>
                        <td class="colblancocen"><textarea name="seo_descripcion"><?php echo $empresa['seo_descripcion']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td class="colgrishome">SEO Metatags</td>
                        <td class="colblancocen"><textarea name="seo_metatags"><?php echo $empresa['seo_metatags']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Pie de Página</td>
                        <td class="colblancocen"><textarea name="pie_pagina"><?php echo $empresa['pie_pagina']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="titlehome">Datos Empresariales</td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Empresa</td>
                        <td class="colblancocen"><input type="text" name="empresa" value="<?php echo $empresa['empresa']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="colgrishome">RUC</td>
                        <td class="colblancocen"><input name="ruc" type="text" value="20602494188" value="<?php echo $empresa['ruc']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Descripción</td>
                        <td class="colblancocen"><textarea name="descripcion"><?php echo $empresa['descripcion']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td  class="colgrishome">País</td>
                        <td class="colblancocen">
                            <select name="pais" id="pais">
                                <option value="">Seleccione un país</option>
                                <option value="peru" <?php echo ($empresa['pais'] == 'peru') ? 'selected' : ''; ?>>Perú</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td  class="colgrishome">Región / Departamento</td>
                        <td class="colblancocen">
                            <select name="dpto" id="dpto">
                                <option value="">Seleccione un departamento</option>
                                <?php if ($empresa['pais'] == 'peru'): ?>
                                    <option value="<?php echo $empresa['dpto']; ?>" selected><?php echo $empresa['dpto']; ?></option>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td  class="colgrishome">Ciudad / Provincia</td>
                        <td class="colblancocen">
                            <select name="city" id="city">
                                <option value="">Seleccione una provincia</option>
                                <?php if (!empty($empresa['city'])): ?>
                                    <option value="<?php echo $empresa['city']; ?>" selected><?php echo $empresa['city']; ?></option>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="colgrishome" align="right">Direccion Principal:</td>
                        <td class="colblancocen"><input name="direccion_principal" type="text" id="direccion" value="<?php echo htmlspecialchars($empresa['direccion_principal'] ?? ''); ?>"></td>
                    </tr>
                    <tr>
                        <td class="colgrishome" align="right">Email Contactos:</td>
                        <td class="colblancocen"><input name="email_contactos" type="text" value="<?php echo htmlspecialchars($empresa['email_contactos'] ?? 'ventas.utisac@gmail.com'); ?>"></td>
                    </tr>
                    <tr>
                        <td class="colgrishome" align="right">Email Ventas:</td>
                        <td class="colblancocen"><input name="email_ventas" type="text" value="<?php echo htmlspecialchars($empresa['email_ventas'] ?? 'ventas.utisac@gmail.com'); ?>"></td>
                    </tr>
                    <tr>
                        <td class="colgrishome" align="right">Telefono Fijo:</td>
                        <td class="colblancocen"><input name="telefono_fijo" type="text"  value="<?php echo htmlspecialchars($empresa['telefono_fijo'] ?? '933 720 547'); ?>"></td>
                    </tr>
                    <tr>
                        <td class="colgrishome" align="right">Telefono Movil:</td>
                        <td class="colblancocen"><input name="telefono_movil" type="text"  value="<?php echo htmlspecialchars($empresa['telefono_movil'] ?? '922 919 010'); ?>"></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="titlehome">Servicios</td>
                    </tr>
                    <tr>
                        <td class="colgrishome">Moneda</td>
                        <td class="colblancocen">
                            <select name="moneda">
                                <option value="0" <?php echo ($empresa['moneda'] == '0') ? 'selected' : ''; ?>>Dólares</option>
                                <option value="1" <?php echo ($empresa['moneda'] == '1') ? 'selected' : ''; ?>>Soles</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome" align="right" valign="top">Precios</td>
                        <td class="colblancocen">
                            <select name="precios">
                                <option value="0" <?php echo ($empresa['precios'] == '0') ? 'selected' : ''; ?>>No Activo</option>
                                <option value="1" <?php echo ($empresa['precios'] == '1') ? 'selected' : ''; ?>>Activo</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome" align="right" valign="top">Carrito de Compras</td>
                        <td class="colblancocen">
                            <select name="carrito_compras">
                                <option value="0" <?php echo ($empresa['carrito_compras'] == '0') ? 'selected' : ''; ?>>No Activo</option>
                                <option value="1" <?php echo ($empresa['carrito_compras'] == '1') ? 'selected' : ''; ?>>Activo</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome" align="right" valign="top">Zona de usuarios</td>
                        <td class="colblancocen">
                            <select name="zona_usuarios">
                                <option value="0" <?php echo ($empresa['zona_usuarios'] == '0') ? 'selected' : ''; ?>>No Activo</option>
                                <option value="1" <?php echo ($empresa['zona_usuarios'] == '1') ? 'selected' : ''; ?>>Activo</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="colgrishome" align="right" valign="top">Terminos y condiciones</td>
                        <td class="colblancocen"><textarea name="terminos_condiciones" rows="10" cols="65"><?php echo htmlspecialchars($empresa['terminos_condiciones'] ?? ''); ?></textarea></td>
                    </tr>
                </table>
                <div class="boton-container">
                    <button name="aceptar" class="botonesAyC" type="submit">Aceptar</button>
                    <button name="Cancelar" class="botonesAyC" type="button" onclick="window.location = 'panel.php'">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    
<?php
// Incluir el footer.php
include('estilo/footer.php');
?>
