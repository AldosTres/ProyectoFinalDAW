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
     * @param {*} data //Torneos, usuarios, jueces, eventos, torneos...
     * @param {*} renderTemplate //El contenido de la tabla, las filas
     * @returns 
     */
export function renderTableRows (data, renderTemplate) {
    let rows = ``
    //Recorro un array del que obtengo su item y ese item se pasa a la funcion renderTemplate, correspondiente a cada apartado de 
    //menu de admin, redner template es solo, un ejemplo de la fila de la tabla, el esqueleto
    for (const item of data) {
        rows += renderTemplate(item)
    }
    return rows;
}

/**
 * Funcion que carga los datos recibidos por renderTableRows a la página html
 * @param {*} url 
 * @param {*} params //Parametros que podamos ingresar, en el caso de torneos, el estado del filtro
 * @param {*} succesFunction //Funcion en caso de que el ajax devuelva correctamente los datos
 */
export function loadRenderedData(url, params = {}, succesFunction) {
    $.ajax({
        type: "GET",
        url: url,
        data: params,
        success: function (response) {
            let data = JSON.parse(response);
            if (data.status === 'success') {
                succesFunction(data)
            }
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText)
        }
    });
}

$(document).ready(function () {
    //Cargando a los elementos la funcion selectOption
    $('.sidebar__item').on('click', selectOption);
});