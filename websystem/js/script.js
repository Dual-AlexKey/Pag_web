document.addEventListener("DOMContentLoaded", function () {
    const botonesConSubMenu = document.querySelectorAll(".boton.submenu");
    console.log("JavaScript cargado correctamente.");

    

    botonesConSubMenu.forEach(function (boton) {
        boton.addEventListener("click", function () {
            const menu = boton.nextElementSibling;
            const expanded = boton.getAttribute("aria-expanded") === "true";

            // Si el submenú está abierto, lo cerramos
            if (expanded) {
                menu.style.display = "none";
                boton.querySelector(".icono").textContent = "+";
                boton.setAttribute("aria-expanded", "false");
            } else {
                // Cerrar todos los submenús
                const menusAbiertos = document.querySelectorAll(".menu");
                menusAbiertos.forEach(function (menuAbierto) {
                    menuAbierto.style.display = "none";
                    const icono = menuAbierto.previousElementSibling.querySelector(".icono");
                    icono.textContent = "+";
                    menuAbierto.previousElementSibling.setAttribute("aria-expanded", "false");
                });

                // Abrir el submenú correspondiente
                menu.style.display = "block";
                boton.querySelector(".icono").textContent = "-";
                boton.setAttribute("aria-expanded", "true");
            }
        });
    });

    const departamentos = {
        peru: [
            "Amazonas", "Áncash", "Apurímac", "Arequipa", "Ayacucho", "Cajamarca", "Callao", "Cusco", "Huancavelica",
            "Huánuco", "Ica", "Junín", "La_Libertad", "Lambayeque", "Lima", "Loreto", "Madre_de_Dios", "Moquegua",
            "Pasco", "Piura", "Puno", "San_Martín", "Tacna", "Tumbes", "Ucayali"
        ]
    };
    
    const provincias = {
        Amazonas: ["Bagua", "Bongará", "Chachapoyas", "Condorcanqui", "Luya", "Rodríguez de Mendoza", "Utcubamba"],
        Áncash: ["Aija", "Antonio Raymondi", "Asunción", "Bolognesi", "Carhuaz", "Carlos Fermín Fitzcarrald", "Casma", "Corongo",
                 "Huaraz", "Huari", "Huarmey", "Huaylas", "Mariscal Luzuriaga", "Ocros", "Pallasca", "Pomabamba", "Recuay",
                 "Santa", "Sihuas", "Yungay"],
        Apurímac: ["Abancay", "Andahuaylas", "Antabamba", "Aymaraes", "Cotabambas", "Chincheros", "Grau"],
        Arequipa: ["Arequipa", "Camaná", "Caravelí", "Castilla", "Caylloma", "Condesuyos", "Islay", "La Unión"],
        Ayacucho: ["Cangallo", "Huamanga", "Huanca Sancos", "Huanta", "La Mar", "Lucanas", "Parinacochas", "Páucar del Sara Sara",
                   "Sucre", "Víctor Fajardo", "Vilcas Huamán"],
        Cajamarca: ["Cajabamba", "Cajamarca", "Celendín", "Chota", "Contumazá", "Cutervo", "Hualgayoc", "Jaén", "San Ignacio",
                    "San Marcos", "San Miguel", "San Pablo", "Santa Cruz"],
        Callao: ["Callao"],
        Cusco: ["Acomayo", "Anta", "Calca", "Canas", "Canchis", "Chumbivilcas", "Cusco", "Espinar", "La Convención",
                "Paruro", "Paucartambo", "Quispicanchi", "Urubamba"],
        Huancavelica: ["Acobamba", "Angaraes", "Castrovirreyna", "Churcampa", "Huancavelica", "Huaytará", "Tayacaja"],
        Huánuco: ["Ambo", "Dos de Mayo", "Huacaybamba", "Huamalíes", "Huánuco", "Lauricocha", "Leoncio Prado", "Marañón",
                  "Pachitea", "Puerto Inca", "Yarowilca"],
        Ica: ["Chincha", "Ica", "Nazca", "Palpa", "Pisco"],
        Junín: ["Chanchamayo", "Chupaca", "Concepción", "Huancayo", "Jauja", "Junín", "Satipo", "Tarma", "Yauli"],
        La_Libertad: ["Ascope", "Bolívar", "Chepén", "Gran Chimú", "Julcán", "Otuzco", "Pacasmayo", "Pataz", "Sánchez Carrión",
                      "Santiago de Chuco", "Trujillo", "Virú"],
        Lambayeque: ["Chiclayo", "Ferreñafe", "Lambayeque"],
        Lima: ["Barranca", "Cajatambo", "Canta", "Cañete", "Huaral", "Huarochirí", "Huaura", "Lima", "Oyón", "Yauyos"],
        Loreto: ["Alto Amazonas", "Datem del Marañón", "Loreto", "Mariscal Ramón Castilla", "Maynas", "Putumayo", "Requena",
                 "Ucayali"],
        Madre_de_Dios: ["Manu", "Tahuamanu", "Tambopata"],
        Moquegua: ["General Sánchez Cerro", "Ilo", "Mariscal Nieto"],
        Pasco: ["Daniel Alcides Carrión", "Oxapampa", "Pasco"],
        Piura: ["Ayabaca", "Huancabamba", "Morropón", "Paita", "Piura", "Sechura", "Sullana", "Talara"],
        Puno: ["Azángaro", "Carabaya", "Chucuito", "El Collao", "Huancané", "Lampa", "Melgar", "Moho", "Puno", "San Antonio de Putina",
               "San Román", "Sandia", "Yunguyo"],
        San_Martín: ["Bellavista", "El Dorado", "Huallaga", "Lamas", "Mariscal Cáceres", "Moyobamba", "Picota", "Rioja",
                     "San Martín", "Tocache"],
        Tacna: ["Candarave", "Jorge Basadre", "Tacna", "Tarata"],
        Tumbes: ["Contralmirante Villar", "Tumbes", "Zarumilla"],
        Ucayali: ["Atalaya", "Coronel Portillo", "Padre Abad", "Purús"]
    };
    
    // Referencias a los selectores
    const paisSelect = document.getElementById("pais");
    const dptoSelect = document.getElementById("dpto");
    const citySelect = document.getElementById("city");
    
    // Evento para cambiar los departamentos según el país seleccionado
    paisSelect.addEventListener("change", function () {
        dptoSelect.innerHTML = `<option value="">Seleccione un departamento</option>`;
        citySelect.innerHTML = `<option value="">Seleccione una provincia</option>`;
        
        if (this.value === "peru") {
            departamentos.peru.forEach(depto => {
                dptoSelect.innerHTML += `<option value="${depto}">${depto}</option>`;
            });
        }
    });
    
    // Evento para cambiar las provincias según el departamento seleccionado
    dptoSelect.addEventListener("change", function () {
        citySelect.innerHTML = `<option value="">Seleccione una provincia</option>`;
        
        if (this.value in provincias) {
            provincias[this.value].forEach(provincia => {
                citySelect.innerHTML += `<option value="${provincia}">${provincia}</option>`;
            });
        }
    });
        

});
function guardarFormulario() {
    var nombre = document.getElementById('nombre').value; // Obtener el nombre del formulario
    if (nombre != "") {
        // Usar AJAX para enviar el formulario a PHP
        var formData = new FormData(document.getElementById("form"));
        formData.append("action", "create_page"); // Acción para identificar que se debe crear el archivo

        // Crear una solicitud AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "crear_pagina.php", true); // Cambiar 'crear_pagina.php' al nombre del archivo PHP que manejará la solicitud
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert("Página creada exitosamente.");
                window.location.reload(); // Recargar la página
            }
        };
        xhr.send(formData);
    } else {
        alert("El nombre no puede estar vacío.");
    }
}
    
function cambiarEstilos() {
    const modulo = document.getElementById('modulo').value;
    const estilosDiv = document.getElementById('estilos');
    const estiloSeleccionado = estilosDiv.getAttribute('data-seleccionado'); // Estilo guardado en PHP

    
    // Limpiar las opciones actuales
    estilosDiv.innerHTML = '';

    let estilos = [];
    //resumen=0, galeria=1, portafolio=2,acordion=3,album=4,videos=5,registro=6,recuperar=7,
    //login=8,perfil=9,panel=10,contactos=11,pedidos=12,reserva=13,facturacion=14,suscribe=15

    // Definir los estilos según el módulo seleccionado
    if (modulo === 'Contenidos') {
        estilos = [
            { src: 'https://i.ibb.co/1frpx8B2/estiloblog.gif', alt: 'Blog', name: 'Blog' },
            { src: 'https://i.ibb.co/qLdNSmzM/estiloresumen.gif', alt: 'Resumen', name: 'Resumen' },
            { src: 'https://i.ibb.co/k29qfG19/estilogaleria.gif', alt: 'Galería', name: 'Galería' },
            { src: 'https://i.ibb.co/k29qfG19/estilogaleria.gif', alt: 'Portafolio', name: 'Portafolio' },
            { src: 'https://i.ibb.co/k29qfG19/estilogaleria.gif', alt: 'Acordion', name: 'Acordion' },
            { src: 'https://i.ibb.co/k29qfG19/estilogaleria.gif', alt: 'Album', name: 'Album' },
            { src: 'https://i.ibb.co/k29qfG19/estilogaleria.gif', alt: 'Videos', name: 'Videos' }
        ];
    } else if (modulo === 'Catalogo') {
        estilos = [
            { src: 'https://i.ibb.co/qLdNSmzM/estiloresumen.gif', alt: 'Resumen', name: 'Resumen' },
            { src: 'https://i.ibb.co/k29qfG19/estilogaleria.gif', alt: 'Galería', name: 'Galería' }
        ];
    } else if (modulo === 'Usuarios') {
        estilos = [
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Registro', name: 'Registro' },
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Recuperar', name: 'Recuperar' },
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Login', name: 'Login' },
            { src: 'https://i.ibb.co/mr84FKDj/estilolistado.gif', alt: 'Panel', name: 'Panel' }
        ];
    } else if (modulo === 'Formularios') {
        estilos = [
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Contactos', name: 'Contactos' },
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Pedidos', name: 'Pedidos' },
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Reserva', name: 'Reserva' },
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Facturacion', name: 'Facturacion' },
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Suscribe', name: 'Suscribe' }
        ];
    }

    // Agregar los elementos de estilo a la página
    estilos.forEach(estilo => {
        const divEstilo = document.createElement('div');
        divEstilo.innerHTML = `
            <img src="${estilo.src}" alt="${estilo.alt}"><br>
            <input type="radio" name="estilos" value="${estilo.name}" ${estilo.name === estiloSeleccionado ? 'checked' : ''}> ${estilo.name}
        `;
        estilosDiv.appendChild(divEstilo);
    });
}

// Llamar a la función al cargar la página para que el módulo predeterminado (Módulo 1) tenga los estilos cargados
window.onload = cambiarEstilos;
cambiarEstilos();


function actualizarURL() {
    let nombre = document.getElementById("nombre").value;
    let url = nombre.replace(/\s+/g, "-"); // Reemplaza espacios por "-"
    document.getElementById("link").value = url;
}

function crearArchivo(event) {
    event.preventDefault(); // Evita que el formulario se envíe de inmediato

    // ✅ Validación de "Publicar en Menú"
    let checkboxesMenu = document.querySelectorAll("input[name='publicar[]']");
    let menuSeleccionado = Array.from(checkboxesMenu).some(checkbox => checkbox.checked);

    // ✅ Validación de "Estilos"
    let estilosDiv = document.getElementById("estilos");

    if (!estilosDiv || estilosDiv.children.length === 0) {
        alert("⚠️ Debes seleccionar al menos un estilo.");
        return;
    }

    let estilosSeleccionados = false;
    let elementosEstilos = estilosDiv.querySelectorAll("input, select, option");

    elementosEstilos.forEach(function (elemento) {
        if ((elemento.type === "checkbox" || elemento.type === "radio") && elemento.checked) {
            estilosSeleccionados = true;
        } else if (elemento.tagName === "SELECT" && elemento.value.trim() !== "") {
            estilosSeleccionados = true;
        }
    });

    // ✅ Validación de "Nombre"
    let nombre = document.getElementById('nombre').value.trim();
    if (!nombre) {
        alert('⚠️ Por favor, ingresa un nombre válido.');
        return;
    }

    // ✅ Mensajes de error combinados
    if (!menuSeleccionado && !estilosSeleccionados) {
        alert("⚠️ Falta seleccionar información: Estilos y 'Publicar en Menú'.");
        return;
    }

    if (!menuSeleccionado) {
        alert("⚠️ Falta seleccionar información: 'Publicar en Menú'.");
        return;
    }

    if (!estilosSeleccionados) {
        alert("⚠️ Falta seleccionar información: Estilos.");
        return;
    }

    // ✅ TODO ESTÁ VALIDADO, AHORA ENVIAMOS EL FORMULARIO
    document.getElementById("miFormulario").submit(); // Asegúrate de que tu formulario tenga este ID
}

// 🔹 ACTUALIZAR EXPLORADOR DE IMÁGENES
function actualizarExplorador(url) {
    let listaImagenes = document.querySelector("#lista-imagenes");
    let botonEliminar = document.querySelector(".boton-eliminar");

    if (!listaImagenes) return;

    let modoEliminarActivo = listaImagenes.classList.contains("eliminar-activo");

    fetch(url)
        .then(response => response.text())
        .then(html => {
            let parser = new DOMParser();
            let doc = parser.parseFromString(html, "text/html");
            let nuevaLista = doc.querySelector("#lista-imagenes").innerHTML;
            listaImagenes.innerHTML = nuevaLista;

            if (modoEliminarActivo) {
                setTimeout(() => {
                    listaImagenes.classList.add("eliminar-activo");
                    botonEliminar?.classList.add("activo");
                }, 100);
            }
        })
        .catch(error => console.error("Error al actualizar explorador:", error));
}

// 🔹 ABRIR Y CERRAR EL MODAL
function mostrarExplorador() {
    document.getElementById("modal-explorador").style.display = "block";
}

function cerrarExplorador() {
    document.getElementById("modal-explorador").style.display = "none";
}

// 🔹 SELECCIONAR IMAGEN
function seleccionar(ruta) {
    document.getElementById("imagen_link").value = ruta;
    cerrarExplorador();
}


// 🔹 ACTIVAR/DESACTIVAR MODO ELIMINACIÓN
function activarEliminar() {
    let listaImagenes = document.querySelector("#lista-imagenes");
    let botonEliminar = document.querySelector(".boton-eliminar");

    if (!listaImagenes || !botonEliminar) return;

    let modoEliminarActivo = listaImagenes.classList.contains("eliminar-activo");

    if (modoEliminarActivo) {
        // 🔹 Desactivar modo eliminación y quitar todas las "X"
        listaImagenes.classList.remove("eliminar-activo");
        botonEliminar.classList.remove("activo");

        let botonesX = document.querySelectorAll(".eliminar-x");
        botonesX.forEach(boton => boton.remove());

    } else {
        // 🔹 Activar modo eliminación y agregar "X" a todas las imágenes
        listaImagenes.classList.add("eliminar-activo");
        botonEliminar.classList.add("activo");

        let items = document.querySelectorAll(".item");
        items.forEach(item => {
            if (!item.querySelector(".eliminar-x")) {
                let nombreImagen = item.querySelector("img").alt;
                let botonX = document.createElement("span");
                botonX.classList.add("eliminar-x");
                botonX.innerHTML = "&times;";
                botonX.onclick = function(event) {
                    eliminarImagen(nombreImagen, event, "new_itemimg.php");
                };
                item.appendChild(botonX);
            }
        });
    }
}


// 🔹 ELIMINAR UNA IMAGEN Y ACTUALIZAR SIN RECARGAR
function eliminarImagen(nombreImagen, event, url) {
    event.stopPropagation();

    if (!confirm("¿Seguro que quieres eliminar esta imagen?")) return;

    fetch("../websystem/img/eliminar_imagen.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "nombre=" + encodeURIComponent(nombreImagen)
    })
    .then(response => response.json())
    .then(data => {
        console.log("Respuesta del servidor:", data);
        if (data.status === "success") {
            alert("Imagen eliminada correctamente");

            // ✅ ELIMINAR LA IMAGEN DEL DOM SIN RECARGAR
            let listaImagenes = document.querySelector("#lista-imagenes");
            let imagenes = listaImagenes.querySelectorAll(".item");

            imagenes.forEach(img => {
                if (img.innerHTML.includes(nombreImagen)) {
                    img.remove(); // ✅ Remover la imagen eliminada del DOM
                }
            });

            // ✅ Después de borrar, actualizar el explorador
            setTimeout(() => {
                actualizarExplorador(url);
            }, 200);
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error al eliminar imagen:", error);
        alert("Ocurrió un error al eliminar la imagen.");
    });
}

// 🔹 SUBIR UNA IMAGEN Y AGREGARLA AL EXPLORADOR SIN RECARGAR
function subirImagen(url) {
    let formSubida = document.querySelector("#form-subida");
    let inputImagen = document.querySelector("#imagen");

    if (!formSubida || !inputImagen) {
        console.error("Error: No se encontró el formulario o el input de imagen.");
        return;
    }

    let formData = new FormData(formSubida);
    let archivo = formData.get("imagen");

    if (!archivo || archivo.size === 0) {
        alert("Por favor, selecciona una imagen.");
        return;
    }

    fetch("../websystem/img/subir_imagen.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log("Respuesta del servidor:", data);

        if (data.status === "success") {
            alert("Imagen subida correctamente");

            let listaImagenes = document.querySelector("#lista-imagenes");
            let botonEliminar = document.querySelector(".boton-eliminar");
            let modoEliminarActivo = listaImagenes.classList.contains("eliminar-activo");

            let nuevoItem = document.createElement("div");
            nuevoItem.classList.add("item");

            // ✅ Agregar imagen
            let nuevaImg = document.createElement("img");
            nuevaImg.src = data.ruta;
            nuevaImg.alt = data.nombre;
            nuevaImg.classList.add("preview");
            nuevoItem.appendChild(nuevaImg);

            // ✅ Si "Eliminar" estaba activado, la imagen nueva también tiene "X"
            if (modoEliminarActivo) {
                let botonX = document.createElement("span");
                botonX.classList.add("eliminar-x");
                botonX.innerHTML = "&times;";
                botonX.onclick = function(event) {
                    eliminarImagen(data.nombre, event, url);
                };
                nuevoItem.appendChild(botonX);
            }

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
