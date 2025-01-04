import { showModal, closeModal } from "./modals.js";
/**
 * Función que sirve para seleccionar una opción del menu admin y mostrar su contenido
 * @param {*} event 
 */

function selectOption(event) {
    // Evitar que el enlace recargue la página
    event.preventDefault();
    // Obtener la sección que se debe mostrar
    let sectionId = $(this).attr('data-section')

    // Recorremos todas las secciones para mostrar/ocultar según corresponda
    $('.menu__section').addClass('menu__section--hidden') // Oculta todas las secciones
    $('.nav-link').removeClass('active') //Quito la clase active a todos los link del sidebar
    
    $('#link-' + sectionId).addClass('active') //Pongo la clase active a la sección escogida a mostrar
    $('#' + sectionId).removeClass('menu__section--hidden') // Muestra la sección correspondiente
}

/**
 * Función que recorre un array de datos y genera un string HTML a partir de una plantilla de renderizado.
 * @param {Array} data - Array de datos (ej.: torneos, usuarios, jueces, etc.).
 * @param {Function} renderTemplate - Función que recibe un elemento del array y devuelve un string HTML.
 * @returns {string} - String que contiene las filas HTML generadas.
 */
export function renderItems (data, renderTemplate) {
    //.map aplica a cada elemento del array una función y el resultado lo aplica en un nuevo array
    return data.map(item => renderTemplate(item)).join() //.join() combina los elementos del array en un solo string
}

/**
 * Función diseñada para realizar una solicitud GET/POST a un servidor, recibir datos en formato JSON,
 * y ejecutar una función de éxito personalizada si la solicitud es exitosa
 * @param {*} url 
 * @param {*} params //Parametros que podamos ingresar, en el caso de torneos, el estado del filtro
 * @param {*} succesFunction //Funcion en caso de que el ajax devuelva correctamente los datos
 */
export function loadRenderedData(type, url, params = {}, succesFunction, isFormData = false) {
    $.ajax({
        type: type,
        url: url,
        data: params,
        /**
         * Manejo de processData y contentType:
         * Si el tipo de solicitud es POST o PUT, se desactivan processData y contentType para manejar datos como FormData automáticamente.
         * Para solicitudes GET o DELETE, se mantiene el comportamiento por defecto.
         * Excepto que se pase el parámetro isFormData, para cuando sea una solicitud POST pero con datos serializados
         */
        processData: !isFormData, // Si es FormData, no lo procesa. Sirve para el manejo de FormData
        contentType: !isFormData ? 'application/x-www-form-urlencoded; charset=UTF-8' : false, // Evita forzar el Content-Type
        success: function (response) {
            let data = JSON.parse(response);
            if (data.status === 'success') {
                succesFunction(data)
            } else {
                showModal(data.title, data.message)
            }
        },
        error: function (xhr, status, error) {
            console.error('Detalles del error AJAX:', 
                url,
                status,
                error,
                xhr.responseText
            )
            showModal('Error', `Ocurrió un problema al enviar la solicitud: ${status}.`);
        }
    });
}

$(document).ready(function () {
    //Cargando a los elementos la funcion selectOption
    $('.sidebar__item').on('click', selectOption);
});

/**
 * Función que genera paginación de una lista
 * @param {*} totalPages //Numero total de páginas
 * @param {*} currentPage //Página donde nos encontramos
 * @param {*} chargeList //Funcion que genera el listado
 * @param {*} name //Nombre de la seccion donde mostrar la paginación
 */
export function renderPagination(totalPages, currentPage, chargeList, name) {
    let paginationContainer = $('.pagination-container-' + name);
    paginationContainer.empty();

    for (let i = 1; i <= totalPages; i++) {
        paginationContainer.append(`
            <button class="pagination__button ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>
        `);
    }

    $('.pagination__button').on('click', function () {
        let page = parseInt($(this).attr('data-page'), 10); // Conversión
        chargeList(page)
    });
}