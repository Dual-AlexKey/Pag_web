<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Editor con Modal de Imagen</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">

  <link rel="stylesheet" type="text/css" href="css/styles.css?<?php echo time(); ?>" />

</head>
<body>
<style>
        /* Ampliar el ancho del editor */
        #editor {
            width: 100%; /* Ocupa todo el ancho disponible */
        }
    </style>

  <div class="container">
    <textarea id="editor" name="editor"></textarea>
  </div>

  <!-- MODAL -->
  <div id="modal-explorador" class="modal2">
  <div class="modal-backdrop"></div> <!-- FONDO OSCURO LOCAL -->
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarExplorador()">&times;</span>
        <h3>Explorador de Imágenes</h3>

        <!-- 🔹 FORMULARIO DE SUBIDA DE IMÁGENES -->
        <form id="form-subida" enctype="multipart/form-data">
            <input type="file" id="imagen" name="imagen" accept="image/*">
            <button type="button" class="boton-subir" onclick="subirgen()">Subir Imagen</button>
            <button type="button" class="boton-eliminar" onclick="activarEliminar()">Eliminar</button>
        </form>
        <!-- 🔹 LISTADO DE IMÁGENES QUE SE ACTUALIZARÁ AUTOMÁTICAMENTE -->
        <div class="explorador" id="lista-imagenes">
            <?php
            $directorio = "../img/";
            $archivos = is_dir($directorio) ? scandir($directorio) : [];
            if (!empty($archivos)) {
                foreach ($archivos as $archivo) {
                    if ($archivo != "." && $archivo != "..") {
                        $ruta = $directorio . $archivo;
                        echo "<div class='item' onclick='seleccionarImagen(\"$ruta\")'>";
                        echo "<span class='eliminar-x' onclick='eliminarImagen(\"$archivo\", event)'>&times;</span>"; // ✅ Agregar botón de eliminar
                        echo "<img src='$ruta' alt='$archivo' class='preview'>";
                        echo "</div>";
                    }
                }
            } else {
                echo "<p>No se encontraron imágenes.</p>";
            }
            ?>
        </div>
    </div>
</div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>

  <script>
    $(document).ready(function () {
      $('#editor').summernote({
        height: 400,
        toolbar: [
          ['style', ['style','bold', 'italic', 'underline', 'clear']],
          ['font', ['fontname', 'fontsize', 'color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['insert', ['myImageButton']],
          ['view', ['link', 'video', 'table','codeview']]
        ],
        buttons: {
          myImageButton: function (context) {
            var ui = $.summernote.ui;
            return ui.button({
                contents: '<i class="note-icon-picture"></i>',
                tooltip: 'Insertar imagen desde explorador',
                click: function () {
                    mostrarExplorador('editor'); // ✅ Este ID lo usás en data-campo
                }
            }).render();
          }
        }
      });
        // Cargar contenido desde el campo 'contenido' de la página principal
        var contenido = window.parent.document.getElementById('contenido').value;

        // Modificar el contenido para agregar '../' en las URLs de las imágenes
        contenido = contenido.replace(/<img src="(?!..\/)([^"]+)"/g, function(match, p1) {
          return '<img src="../' + p1 + '"';
        });

        // Inserta el contenido en el editor de Summernote
        $('#editor').summernote('code', contenido);

        // Función para actualizar el contenido en el campo oculto del formulario principal
        function actualizarContenido() {
          var contenido = $('#editor').summernote('code');

          // Eliminar el prefijo '../' de las imágenes antes de enviarlo
          contenido = contenido.replace(/<img src="..\/([^"]+)"/g, function(match, p1) {
            return '<img src="' + p1 + '"';
          });

          // Actualiza el contenido en el textarea del formulario principal
          window.parent.document.getElementById('contenido').value = contenido;
        }

        // Llamar a la función cada vez que el contenido cambie
        $('#editor').on('summernote.change', function() {
          actualizarContenido();
        });

        // También actualizar el contenido cuando se cargue el editor
        actualizarContenido();
    });

    function seleccionarImagen(ruta) {
      $('#editor').summernote('insertImage', ruta, function ($image) {
        $image.addClass('img-responsive');
      });
      cerrarExplorador();
    }
    function subirgen() {
    let modal = document.getElementById("modal-explorador");
    let campoDestino = modal.getAttribute("data-campo");

    if (!campoDestino) {
        console.error("No se definió el campo destino.");
        return;
    }

    let inputImagen = document.querySelector("#imagen");
    let inputFile = document.querySelector("#file");

    // Usamos el primer archivo válido que encontremos
    let archivo = inputImagen?.files[0];

    if (!archivo) {
        alert("Por favor, selecciona una imagen.");
        return;
    }

    let formData = new FormData();
    formData.append("imagen", archivo); // 💡 Esto es lo que espera subir_imagen.php

    fetch("../websystem/img/subir_imagen.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert("Imagen subida correctamente.");

            // ✅ Insertar la ruta en el input que disparó el modal
            let inputTexto = document.querySelector(`#${campoDestino}`);
            if (inputTexto) inputTexto.value = data.ruta;

            // ✅ Agregar imagen al explorador
            let listaImagenes = document.querySelector("#lista-imagenes");
            let nuevoItem = document.createElement("div");
            nuevoItem.classList.add("item");

            let nuevaImg = document.createElement("img");
            nuevaImg.src = data.ruta;
            nuevaImg.alt = data.nombre;
            nuevaImg.classList.add("preview");
            nuevaImg.onclick = function () {
                seleccionarImagen(data.ruta);
            };

            nuevoItem.appendChild(nuevaImg);
            listaImagenes.appendChild(nuevoItem);
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error al subir imagen:", error);
        alert("Ocurrió un error al subir la imagen.");
    });
}


    
  </script>
  <script type="text/javascript" src="js/script.js "></script>


</body>
</html>
