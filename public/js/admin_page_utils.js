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
    $('#' + sectionId).removeClass('menu__section--hidden') // Muestra la sección correspondiente
}

/**
     * Funcion que retorna las filas de una tabla con su respectivo contenido
     * *CORREGIR NOMBRE
     * @param {*} data //Torneos, usuarios, jueces, eventos, torneos...
     * @param {*} renderTemplate //El contenido de la tabla, las filas
     * @returns 
     */
export function renderItems (data, renderTemplate) {
    let rows = ``
    //Recorro un array del que obtengo su item y ese item se pasa a la funcion renderTemplate, correspondiente a cada apartado de 
    //menu de admin, redner template es solo, un ejemplo de la fila de la tabla, el esqueleto
    for (const item of data) {
        rows += renderTemplate(item)
    }
    return rows;
}

/**
 * Función diseñada para realizar una solicitud GET a un servidor, recibir datos en formato JSON,
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
            showModal('Error', 'Ocurrió un problema al enviar la solicitud.');
            console.error('Error:', status, error);
        }
    });
}

$(document).ready(function () {
    //Cargando a los elementos la funcion selectOption
    $('.sidebar__item').on('click', selectOption);
});