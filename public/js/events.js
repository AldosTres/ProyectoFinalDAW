import { showModal, closeModal } from './modals.js'
import { renderItems, loadRenderedData, renderPagination } from "./admin_page_utils.js"
$(document).ready(function () {
    function generateEventRowTemplate(event) {
        let isActive = event.activo == 1 ? true : false
        return `<tr class="events__list-table-row">
                    <td class="events__list-table-item" id="event-id">${event.id}</td>
                    <td class="events__list-table-item">${event.nombre}</td>
                    <td class="events__list-table-item">${event.descripcion ? (event.descripcion).substring(0,15) + '...' : 'Sin descripción'}</td>
                    <td class="events__list-table-item">${event.fecha_inicio}</td>
                    <td class="events__list-table-item">
                        <span class="events__list-status events__list-status--${event.estado.toLowerCase()}" data-id="${event.estado}">${event.estado}</span>
                    </td>
                    <td class="events__list-table-item">
                        <span class="events__list-active-status events__list-status--${isActive ? 'active' : 'inactive'}" data-id="${event.activo}">${isActive ? 'Si' : 'No'}</span>
                    </td>
                    <td class="events__list-table-item events__list-table-item--actions">
                        <div class="tooltip-container">
                            <button class="events__list-table-button list-table-button events__list-table-button--view-details tooltip-container__button" event-id-data="${event.id}"><i class="fa-solid fa-eye"></i></button>
                            <span class="tooltip-container__text">Ver detalles</span>
                        </div>
                        <div class="tooltip-container">
                            <button class="events__list-table-button list-table-button events__list-table-button--edit tooltip-container__button" event-id-data="${event.id}"><i class="fa-solid fa-pencil"></i></button>
                            <span class="tooltip-container__text">Editar evento</span>
                        </div>
                        <div class="tooltip-container">
                            <button class="events__list-table-button list-table-button events__list-table-button--toggle-active tooltip-container__button" event-id-data="${event.id}">${isActive ? '<i class="fa-solid fa-trash"></i>' : '<i class="fa-solid fa-arrow-rotate-left"></i>'}</button>
                            <span class="tooltip-container__text">${isActive ? 'Eliminar evento' : 'Activar evento'}</span>
                        </div>
                        
                        <div class="tooltip-container">
                            <a href="${event.link_mapa}" target="_blank" class="tooltip-container__link">
                                <button class="events__list-table-button list-table-button events__list-table-button--open-map tooltip-container__button" event-id-data="${event.id}">
                                    <i class="fa-solid fa-map"></i>
                                </button>
                            </a>
                            <span class="tooltip-container__text">Abrir en mapas</span>
                        </div>

                    </td>
                </tr>`
    }

    /**
     * Función que obtiene los valores del filtro, y dependiendo del filtro obtiene unos eventos u otros,
     * En caso de que no exista filtro, muestra todos los eventos seguidamente muestra estos eventos en #event-list
     */
    function loadEventsByStatus(page = 1) {
        $('#events-list').empty()
        // Siempre voy a querer mostrar 8 eventos por página
        const itemsPerPage = 8
        // Obtención de los valores de cada filtro
        let name = $('#event-name-search').val()
        let status = $('#event-filter-status').val()
        let startDate = $('#event-filter-start-date').val()
        let endDate = $('#event-filter-end-date').val()
        let eventActive = $('#event-filter-active:checked').is(':checked') ? 1 : 0;
        console.log(eventActive)
        let url = `admin/events/list/${page}/${itemsPerPage}`
        loadRenderedData('GET', url, {name, status, startDate, endDate, eventActive}, (data) => {
            let events = data.events
            console.log(events)
            let rows = renderItems(events, generateEventRowTemplate)
            $('#events-list').empty().append(rows)
            // Genero la paginación

            renderPagination(data.total_pages, page, this, 'events');
        })
    }

    function generateGoogleMapsEmbed(link) {
        if (!link) {
            return '<span>No disponible</span>';
        }
    
        let embedLink = '';
    
        // Si el enlace contiene '/maps/place', lo transformamos en un enlace embebido
        if (link.includes('/maps/place')) {
            embedLink = link.replace('https://www.google.com/maps/place', 'https://www.google.com/maps/embed/v1/place');
            embedLink += '?key=YOUR_GOOGLE_MAPS_API_KEY';  // Reemplaza con tu API Key de Google
        } else {
            return `<span>Enlace no compatible para embebido.</span>`;
        }
    
        // Retornar el iframe con la URL embebida
        return `<iframe
                    src="${embedLink}"
                    width="400"
                    height="300"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>`;
    }
    
    
    

    function generateEventDetailsTemplate(event){
        return `<div id="event-details-container">
                    <div class="event-detail-row">
                        <strong class="event-detail-label">Nombre:</strong>
                        <span class="event-detail-value" id="event-name">${event.nombre ?? 'Nombre no disponible'}</span>
                    </div>
                    <div class="event-detail-row">
                        <strong class="event-detail-label">Fecha:</strong>
                        <span class="event-detail-value" id="event-date">${event.fecha_inicio ?? 'Fecha no disponible'}</span>
                    </div>
                    <div class="event-detail-row event-detail-row--image">
                        <strong class="event-detail-label">Imagen:</strong>
                        <img src="${BASE_URL}img/logos_eventos/${event.url_imagen}.jpg" alt="" width=100>

                        <span class="event-detail-value" id="event-image"></span>
                    </div>
                    <div class="event-detail-row">
                        <strong class="event-detail-label">Lugar:</strong>
                        ${generateGoogleMapsEmbed(event.link_mapa)}
                    </div>
                    <div class="event-detail-row">
                        <strong class="event-detail-label">Descripción:</strong>
                        <p class="event-detail-value" id="event-description">${event.descripcion ?? 'No disponible'}</p>
                    </div>
            </div>`
    }

    // Para no pasar nada a la función de primeras
    $('#sidebar-events').on('click', () => loadEventsByStatus());
    $('#event-filter-button').on('click', () => loadEventsByStatus());

    function handleEventDetails() {
        let eventId = $(this).attr('event-id-data')
        let url = `admin/events/${eventId}/details`
        loadRenderedData('GET', url, {}, (data) => {
            let eventDetails = generateEventDetailsTemplate(data.event)
            showModal(data.title, eventDetails)
        })
    }

    // $('#events-list').off('click', )
    $('#events-list').off('click', '.events__list-table-button--view-details').on('click', '.events__list-table-button--view-details', handleEventDetails)

    function generateEventEditFormTemplate(event) {
        return `<form method="post" class="events__form form" enctype="multipart/form-data" id="edit-event-form">
                    <div class="events__field form-group row form__field">
                        <label for="event-name" class="events__field-label form__field-label">Nombre del evento:</label>
                        <input type="text" name="edit-event-name" id="edit-event-name" class="events__field-input form__field-input" value="${event.nombre}">
                    </div>
                    <div class="events__field form__field">
                        <label for="event-description" class="events__field-label form__field-label">Descripción:</label>
                        <textarea name="edit-event-description" id="edit-event-description" class="events__field-input form__field-input">${event.descripcion}</textarea>
                    </div>
                    <div class="events__field form__field">
                        <label for="event-start-date" class="events__field-label form__field-label">Fecha de inicio:</label>
                        <input type="date" name="edit-event-start-date" id="edit-event-start-date" class="events__field-input form__field-input" value="${event.fecha_inicio}">
                    </div>
                    <div class="events__field form__field">
                        <label for="event-start-time" class="events__field-label form__field-label">Fecha de fin:</label>
                        <input type="date" name="edit-event-end_date" id="edit-event-end_date" class="events__field-input form__field-input" value="${event.fecha_fin}">
                    </div>
                    <div class="events__field form__field">
                        <label for="event-location" class="events__field-label form__field-label">Ubicación:</label>
                        <input type="text" name="edit-event-location" id="edit-event-location" class="events__field-input form__field-input" value="${event.link_mapa}">
                    </div>
                    <div class="events__field form__field">
                        <label for="event-image" class="events__field-label form__field-label">Imagen del evento:</label>
                        <input type="file" name="edit-event-image" id="edit-event-image" class="events__field-input form__field-input events__field-input--file" accept=".jpg">
                    </div>
                    <input type="hidden" name="event-id" value="${event.id}">
                </form>`;
    }

    /**
     * Función que se encarga de procesar y enviar los datos del formulario de edición de un evento al servidor
     * @param {*} form 
     */
        function submitEditEventForm(form, eventId) {
            let formData = new FormData(form[0]) //Para trabajar directamente con el objeto DOM y no con el objeto jquery
            let url = `admin/events/${eventId}/update`
            loadRenderedData('POST', url, formData, (data) => {
                    closeModal()          // Cierra el modal
                    showModal(data.title, data.message) // Muestra mensaje del servidor
            }, true)
        }

    /**
     * Muestra el formulario para editar información del formulario
     */
    function handleEventEditForm() {
        let eventId = $(this).attr('event-id-data')
        let url = `admin/events/${eventId}/details`
        loadRenderedData('GET', url, {}, (data)=> {
            let eventEditForm = generateEventEditFormTemplate(data.event)
            console.log(data.a)
            showModal('Editar torneo', eventEditForm, () => {
                submitEditEventForm($('#edit-event-form'), eventId)
            })
        })
    }

    $('#events-list').off('click', '.events__list-table-button--edit').on('click', '.events__list-table-button--edit', handleEventEditForm)

    function toggleEventActiveStatus() {
        let eventId = $(this).attr('event-id-data'); // Obtiene el ID del evento
        let eventActiveStatus = $('.events__list-active-status').attr('data-id'); // Obtiene el estado "Activo" del evento
        console.log(eventId)
        console.log(eventActiveStatus)
        if (!eventId || typeof eventActiveStatus === 'undefined') {
            showModal('Error', 'No se pudo obtener el ID o estado activo del evento.');
        } else {
            let url = `admin/events/${eventId}/toggle-active`
            loadRenderedData('GET', url, {eventActiveStatus }, (data) => {
                showModal(data.title, data.message);
            });
        }
    }


    // Manejar clics en los botones de activación/desactivación para eventos
    $('#events-list')
    .off('click', '.events__list-table-button--toggle-active')
    .on('click', '.events__list-table-button--toggle-active', toggleEventActiveStatus);

});