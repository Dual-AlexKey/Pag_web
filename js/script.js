document.addEventListener("DOMContentLoaded", function () {
    const botonesConSubMenu = document.querySelectorAll(".boton.submenu");

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
    
    // Limpiar las opciones actuales
    estilosDiv.innerHTML = '';

    let estilos = [];

    // Definir los estilos según el módulo seleccionado
    if (modulo === 'Contenidos') {
        estilos = [
            { src: 'https://i.ibb.co/1frpx8B2/estiloblog.gif', alt: 'Blog', name: 'Blog' },
            { src: 'https://i.ibb.co/qLdNSmzM/estiloresumen.gif', alt: 'Resumen', name: 'Resumen' },
            { src: 'https://i.ibb.co/k29qfG19/estilogaleria.gif', alt: 'Galería', name: 'Galería' },
            { src: 'https://i.ibb.co/k29qfG19/estilogaleria.gif', alt: 'Galería', name: 'Portafolio' },
            { src: 'https://i.ibb.co/k29qfG19/estilogaleria.gif', alt: 'Galería', name: 'Acordion' },
            { src: 'https://i.ibb.co/k29qfG19/estilogaleria.gif', alt: 'Galería', name: 'Album' },
            { src: 'https://i.ibb.co/k29qfG19/estilogaleria.gif', alt: 'Galería', name: 'Videos' }
        ];
    } else if (modulo === 'Catalogo') {
        estilos = [
            { src: 'https://i.ibb.co/qLdNSmzM/estiloresumen.gif', alt: 'Resumen', name: 'Resumen' },
            { src: 'https://i.ibb.co/k29qfG19/estilogaleria.gif', alt: 'Galería', name: 'Galería' }
        ];
    } else if (modulo === 'Usuarios') {
        estilos = [
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Blog', name: 'Registro' },
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Blog', name: 'Recuperar' },
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Blog', name: 'Login' },
            { src: 'https://i.ibb.co/mr84FKDj/estilolistado.gif', alt: 'Blog', name: 'Panel' }
        ];
    } else if (modulo === 'Formularios') {
        estilos = [
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Blog', name: 'Contactos' },
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Blog', name: 'Pedidos' },
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Blog', name: 'Reserva' },
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Blog', name: 'Facturacion' },
            { src: 'https://i.ibb.co/kLZTY7D/estilosformulario.gif', alt: 'Blog', name: 'Suscribe' }
        ];
    }

    // Agregar los elementos de estilo a la página
    estilos.forEach(estilo => {
        const divEstilo = document.createElement('div');
        divEstilo.innerHTML = `
            <img src="${estilo.src}" alt="${estilo.alt}"><br>
            <input type="radio" name="estilo" id="${estilo.name}" value="${estilo.name}"> ${estilo.name}
        `;
        estilosDiv.appendChild(divEstilo);
    });
}

// Llamar a la función al cargar la página para que el módulo predeterminado (Módulo 1) tenga los estilos cargados
window.onload = cambiarEstilos;

function actualizarURL() {
    let nombre = document.getElementById("nombre").value;
    let url = nombre.replace(/\s+/g, "-"); // Reemplaza espacios por "-"
    document.getElementById("link").value = url;
}


