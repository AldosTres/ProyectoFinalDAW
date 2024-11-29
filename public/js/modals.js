document.addEventListener('DOMContentLoaded', () => {
    /**
     * Función que muestra una ventana y un mensaje dentro de la ventana
     * @param {string} message 
     * @param {string} type 
     */
    function showModal(type, message) {
        let modal = document.getElementById('modal')
        let modalMessage = document.getElementById('modal-content')
        modal.style.display = 'flex'
        modal.style.alignItems = 'center'
        modal.style.justifyContent = 'center'
        modalMessage.innerHTML = message

    }

    /**
     * Función que esconde el elemento html con id="modal"
     */
    function closeModal() {
        let modal = document.getElementById('modal')
        modal.style.display = 'none'
    }

    let flashData = document.getElementById('flash-data')
    if (flashData) {
        showModal(flashData.name, flashData.value)
    }
    
    let closeButton = document.getElementById('close-button')
    closeButton.addEventListener('click', closeModal)

})

$(document).ready(function () {
    /**
     * Función que muestra una ventana y un mensaje dentro de la ventana
     * @param {string} title 
     * @param {string} content 
     * @param {function} onConfirm 
     */
    function showModal(title, content, onConfirm = null) {
        //Agregando el titulo
        $('#modal-title').html(title)
        //Agregando el contenido
        $('#modal-content').html(content)
        //Abriendo el modal con animación
        $('#modal').fadeIn()

        // Al colocar .off('click), eliminamos el evento click, antes de colocarle otro manejador de eventos
        // Importante porque pretendemos que este modal se pueda abrir muchas veces y evitamos que el evento click
        // pueda ejecutarse muchas veces. El botón confirmar es dinámico

        //Dando acción a los botones
        $('#confirm-button').off('click').on('click', () =>
            // si recibo la variable onConfirm, ejecuta la función pasada por parámetro y en caso contrario, cierro el modal.
            onConfirm ? onConfirm() : closeModal()
        )
        $('#cancel-button')..on('click', closeModal)
    }

    /**
     * Función que esconde el elemento html con id="modal" con animación
     */
    function closeModal() {
        $('#modal').fadeOut()
    }

    //Acción para el boton cerrar
    $('#close-button').on('click', closeModal)

    
});