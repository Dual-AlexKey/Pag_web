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
    <textarea id="editor"></textarea>
  </div>

  <!-- MODAL -->
  <div id="modal-explorador" class="modal">
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
                        echo "<div class='item' onclick='seleccionar(\"$ruta\")'>";
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
        height: 600,
        toolbar: [
          ['style', ['style','bold', 'italic', 'underline', 'clear']],
          ['font', ['fontname', 'fontsize', 'color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['insert', ['myImageButton', 'link', 'video', 'table']]
        ],
        buttons: {
          myImageButton: function (context) {
            var ui = $.summernote.ui;
            return ui.button({
              contents: '<i class="note-icon-picture"></i>',
              tooltip: 'Insertar imagen desde explorador',
              click: function () {
                mostrarExplorador();
              }
            }).render();
          }
        }
      });
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
        console.error("Error: No se encontró el campo destino en el modal.");
        return;
      }

      let inputImagen = document.querySelector("#imagen"); // Esta línea será eliminada

      if (!inputImagen) {
        console.error("Error: No se encontró el input de imagen.");
        return;
      }

      // Crear un input de tipo file dinámicamente
      let archivoInput = document.createElement('input');
      archivoInput.type = 'file';
      archivoInput.accept = 'image/*';

      // Abrir el selector de archivo
      archivoInput.click();

      // Cuando el usuario selecciona un archivo
      archivoInput.onchange = function () {
        let archivo = archivoInput.files[0];

        if (!archivo) {
          alert("Por favor, selecciona una imagen.");
          return;
        }

        let formData = new FormData();
        formData.append("imagen", archivo);

        fetch("../websystem/img/subir_imagen.php", {
          method: "POST",
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          console.log("Respuesta del servidor:", data);

          if (data.status === "success") {
            alert("Imagen subida correctamente");

            let inputTexto = document.querySelector(`#${campoDestino}`);
            inputTexto.value = data.ruta;

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
      };
    }

    
  </script>
  <script type="text/javascript" src="js/script.js "></script>


</body>
</html>
