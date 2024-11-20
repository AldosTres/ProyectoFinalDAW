function selectOption() {
    // Seleccionar los elementos del menú y las secciones correspondientes
    let menuItems = document.querySelectorAll('.sidebar__item');
    let sections = document.querySelectorAll('.menu__section');

    menuItems.forEach(item => {
        item.addEventListener("click", (event) => {
            // Evitar que el enlace recargue la página
            event.preventDefault();

            // Obtener la sección que se debe mostrar
            let sectionId = item.getAttribute('data-section');
            let sectionToShow = document.getElementById(sectionId);

            // Recorremos todas las secciones para mostrar/ocultar según corresponda
            sections.forEach(section => {
                if (section.getAttribute('id') === sectionId) {
                    section.classList.remove('menu__section--hidden'); // Mostrar la sección seleccionada
                } else {
                    section.classList.add('menu__section--hidden'); // Ocultar otras secciones
                }
            });
        });
    });
}

// Ejecutar la función cuando el contenido del DOM esté cargado
document.addEventListener("DOMContentLoaded", selectOption, false);
