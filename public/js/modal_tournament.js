document.addEventListener('DOMContentLoaded', () => {
    /**
     * Función que muestra una ventana y un mensaje dentro de la ventana
     * @param {string} message 
     * @param {string} type 
     */
    function showModal(type, message) {
        let modal = document.getElementById('modal')
        let modalMessage = document.getElementById('modal-message')
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