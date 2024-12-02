/**
 * Función que muestra una ventana y un mensaje dentro de la ventana, en caso de que 
 * @param {string} title //Titulo del modal
 * @param {string} content //Contenido del modal
 * @param {function} onConfirm //funcion en caso de que el boton confirmar tenga que realizar otra funcion específica
 */
export function showModal(title, content, onConfirm = null) {
    //Agregando el titulo
    $('#modal-title').html(title)
    //Agregando el contenido
    $('#modal-content').html(content)
    //Abriendo el modal con animación
    $('#modal').fadeIn()
    //Cambio los valores css de .modal
    $('.modal').css('display', 'flex')
    $('.modal').css('align-items', 'center')
    $('.modal').css('justify-content', 'center')


    // Al colocar .off('click), eliminamos el evento click, antes de colocarle otro manejador de eventos
    // Importante porque pretendemos que este modal se pueda abrir muchas veces y evitamos que el evento click
    // pueda ejecutarse muchas veces. El botón confirmar es dinámico, o cierra el modal o realiza una función.

    //Dando acción a los botones
    $('#confirm-button').off('click').on('click', () =>
        // si recibo la variable onConfirm, ejecuta la función pasada por parámetro y en caso contrario, cierro el modal.
        onConfirm ? onConfirm() : closeModal()
    )
    $('#cancel-button').off('click').on('click', closeModal)
}

/**
 * Función que esconde el elemento html con id="modal" con animación
 */
export function closeModal() {
    $('#modal').fadeOut()
}

//Acción para el boton cerrar
$('#close-button').on('click', closeModal)
