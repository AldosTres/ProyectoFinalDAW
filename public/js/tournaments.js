import { showModal, closeModal } from './modals.js';
import { renderTableRows, loadRenderedData } from "./admin_page_utils.js";

$(document).ready(function () {
    //Abriendo el modal en cuanto reciba un flashdata indicando que se ha creado un torneo
    let flashData = document.getElementById('flash-data')
    if (flashData) {
        showModal(flashData.name, flashData.value)
    }

    /**
     * Función que devuelve el esqueleto o modelo de una fila de una lista de torneos
     * @param {*} tournament 
     * @returns 
     */
    function generateTournamentRowTemplate (tournament) {
            return `<tr class="tournaments__list-table-row">
                        <td class="tournaments__list-table-item" id="tournament-id">${tournament.id}</td>
                        <td class="tournaments__list-table-item">${tournament.nombre}</td>
                        <td class="tournaments__list-table-item">${tournament.fecha_inicio}</td>
                        <td class="tournaments__list-table-item">${tournament.fecha_fin}</td>
                        <td class="tournaments__list-table-item">
                            <span class="tournaments__list-status tournaments__list-status--active">${tournament.activo}</span>
                        </td>
                        <td class="tournaments__list-table-item tournaments__list-table-item--actions">
                            <button class="tournaments__list-table-button tournaments_list-table-button--edit" data-id="${tournament.id}">Editar</button>
                            <button class="tournaments__list-table-button tournaments_list-table-button--delete">Eliminar</button>
                            <button class="tournaments__list-table-button tournaments_list-table-button--show-participants" data-id="${tournament.id}">Ver participantes</button>
                        </td>
                    </tr>`
    }

    /**
     * Funcion que obtiene el valor del estado del filtro, y dependiendo del filtro obtiene unos torneos u otros,
     * En caso de que no exista filtro, muestra todos seguidamente muestra estos torneos en #tournament-list
     */
    function loadTournamentsByStatus() {
        let status = $('#tournament-filter-status').val()
        loadRenderedData('admin/tournament/list', {status}, (data) => {
            let tournaments = data.tournaments
            let rows = renderTableRows(tournaments, generateTournamentRowTemplate)
            $('#tournament-list').empty().append(rows);
        })
    }

    //Método JQuery parecido a AddEventListener, ya que al devolver un objeto JQuery, debo aplicar un método igual
    $('#sidebar-tournaments').on('click', loadTournamentsByStatus)
    $('#tournaments-filter-button').on('click', loadTournamentsByStatus)


    /**
     * Función que genera una plantilla HTML para un formulario de torneo con datos prellenados.
     * @param {*} tournament 
     * @returns 
     */
    function generateTournamentFormTemplate(tournament) {
        return `<form method="post" class="tournaments__form" enctype="multipart/form-data" id="edit-tournament-form">
                    <div class="tournaments__field">
                        <label for="tournament-name" class="tournaments__field-label">Nombre del torneo:</label>
                        <input type="text" name="edit-name" id="edit-tournament-name" class="tournaments__field-input" value="${tournament.nombre}">
                    </div>
                    <div class="tournaments__field">
                        <label for="tournament-start-date" class="tournaments__field-label">Fecha de inicio:</label>
                        <input type="date" name="edit-start-date" id="edit-tournament-start-date" class="tournaments__field-input" value="${tournament.fecha_inicio}">
                    </div>
                    <div class="tournaments__field">
                        <label for="tournament-end-date" class="tournaments__field-label">Fecha de finalización:</label>
                        <input type="date" name="edit-end-date" id="edit-tournament-end-date" class="tournaments__field-input" value="${tournament.fecha_fin}">
                    </div>
                    <div class="tournaments__field">
                        <label for="tournament-logo" class="tournaments__field-label">Logotipo del torneo:</label>
                        <input type="file" name="edit-logo" id="edit-tournament-logo" class="tournaments__field-input tournaments__field-input--file" accept=".jpg" required>
                    </div>
                    <div class="tournaments__buttons">
                        <button type="submit" class="tournaments__button form-tournament__button--submit">Crear</button>
                        <button type="reset" class="tournaments__button form-tournament__button--reset">Borrar datos</button>
                    </div>
                    <input type="hidden" name="tournament-id" value="${tournament.id}">
                </form>`
    }

    /**
     * Obtiene la información de un torneo para editarla y muestra un modal con un formulario prellenado.
     */
    function handelTournamentEdit() {
        let tournamentId = $(this).attr('data-id');
        loadRenderedData('admin/tournament/get-data-for-edit', {tournamentId}, (data) => {
            let tournament = data.tournament_info
            let tournamentFormForEdit = generateTournamentFormTemplate(tournament)
            showModal('Modificación datos torneo', tournamentFormForEdit, () => {
                submitEditTournamentForm($('#edit-tournament-form'))
            })
        })
    }
    /**
     * Como genereo dinámicamente el contenido de la tabla, no funciona el añadirles eventos, ya que no están presentes en el DOM
     * cuando se hace $(document).ready() y no se enlaza nunca, para manejar esto, empleamos la delegación de eventos, es decir
     * aplicamos el evento .on() pero a un ancestro de estos existente ya en el DOM, de esta manera, cualquier boton añadido dinamicamente
     * se controla fácilmente
     */
    $('.tournaments__list-table').on('click', '.tournaments_list-table-button--edit', handelTournamentEdit)

    function submitEditTournamentForm(form) {
        let formData = new FormData(form[0]); //Para trabajar directamente con el objeto DOM y no con el objeto jquery
        $.ajax({
            type: 'POST',
            url: 'admin/tournament/update',
            data: formData,
            processData: false, // Necesario para enviar archivos
            contentType: false, // Necesario para enviar archivos
            success: function (response) {
                closeModal()
                let data = JSON.parse(response);
                if (data.status === 'success') {
                    showModal(data.title, data.message);
                }
            }
        });
    }

    function generateParticipantRowTemplate(participant) {
        return `<tr class="participants__list-table-row">
                    <td class="participants__list-table-item">${participant.id}</td>
                    <td class="participants__list-table-item">${participant.alias}</td>
                    <td class="participants__list-table-item participants__list-table-item--actions">
                        <button class="participants__list-table-button participants__list-table-button--ban" data-id="1">Banear usuario</button>
                        <button class="participants__list-table-button participants__list-table-button--delete" data-id="1">Eliminar</button>
                    </td>
                </tr>`
    }

    function handleTournamentParticipants() {
        let tournamentId = $(this).attr('data-id');
        let participantsTable = `<table class="participants__list-table">
                                    <thead class="participants__list-table-header">
                                        <tr class="participants__list-table-row">
                                            <th class="participants__list-table-header-item">Id Inscripción</th>
                                            <th class="participants__list-table-header-item">Alias</th>
                                            <th class="participants__list-table-header-item">Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="participants__list-table-body" id="participants-list">`

        
        loadRenderedData('admin/tournament/participants',{tournamentId}, (data) => {
            let participants = data.participants
            let rows = renderTableRows(participants, generateParticipantRowTemplate)
            participantsTable += rows
            participantsTable += `      </tbody>
                                </table>`
            showModal('Participantes', participantsTable);
        })                       
    }

    $('.tournaments__list-table').on('click', '.tournaments_list-table-button--show-participants', handleTournamentParticipants)

});
