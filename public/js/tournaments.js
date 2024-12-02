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
    function getTournamentRowTemplate (tournament) {
            return `<tr class="tournaments-list-table-row">
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
                        </td>
                    </tr>`;
    }

    /**
     * Funcion que obtiene el valor del estado del filtro, y dependiendo del filtro obtiene unos torneos u otros,
     * seguidamente muestra estos torneos en #tournament-list
     */
    function loadTournamentsByStatus() {
        let status = $('#filter-status').val()
        loadRenderedData('admin/tournament/list', {status}, (data) => {
            let tournaments = data.tournaments
            let rows = renderTableRows(tournaments, getTournamentRowTemplate)
            $('#tournament-list').empty().append(rows);
        })
    }

    //Método JQuery parecido a AddEventListener, ya que al devolver un objeto JQuery, debo aplicar un método igual
    $('#sidebar-tournaments').on('click', loadTournamentsByStatus)
    $('#filter-button').on('click', loadTournamentsByStatus)


    function getTournamentFormTemplate(tournament) {
        return `<form action="admin/tournament/upload" method="post" class="tournaments__form" enctype="multipart/form-data">
                    <div class="tournaments__field">
                        <label for="tournament-name" class="tournaments__field-label">Nombre del torneo:</label>
                        <input type="text" name="name" id="tournament-name" class="tournaments__field-input" value="${tournament.nombre}">
                    </div>
                    <div class="tournaments__field">
                        <label for="tournament-start-date" class="tournaments__field-label">Fecha de inicio:</label>
                        <input type="date" name="start-date" id="tournament-start-date" class="tournaments__field-input" value="${tournament.fecha_inicio}">
                    </div>
                    <div class="tournaments__field">
                        <label for="tournament-end-date" class="tournaments__field-label">Fecha de finalización:</label>
                        <input type="date" name="end-date" id="tournament-end-date" class="tournaments__field-input" value="${tournament.fecha_fin}">
                    </div>
                    <div class="tournaments__field">
                        <label for="tournament-logo" class="tournaments__field-label">Logotipo del torneo:</label>
                        <!-- <input type="text" name="logo" id="tournament-logo" class="form-tournament__input "> -->
                        <input type="file" name="logo" id="tournament-logo" class="tournaments__field-input tournaments__field-input--file" accept="image/*" required>
                    </div>
                    <div class="tournaments__buttons">
                        <button type="submit" class="tournaments__button form-tournament__button--submit">Crear</button>
                        <button type="reset" class="tournaments__button form-tournament__button--reset">Borrar datos</button>
                    </div>
                </form>`
    }
    function getTournamentInfoForEdit() {
        let t = $(this).attr('data-id');
        console.log(t)
        loadRenderedData('admin/tournament/edit', {t}, (data) => {
            let tournament = data.tournament_info
            let row = getTournamentFormTemplate(tournament)
            showModal('Modificación datos torneo', row)
        })
    } 
    /**
     * Como genereo dinámicamente el contenido de la tabla, no funciona el añadirles eventos, ya que no están presentes en el DOM
     * cuando se hace $(document).ready() y no se enlaza nunca, para manejar esto, empleamos la delegación de eventos, es decir
     * aplicamos el evento .on() pero a un ancestro de estos existente ya en el DOM, de esta manera, cualquier boton añadido dinamicamente
     * se controla fácilmente
     */
    $('.tournaments__list-table').on('click', '.tournaments_list-table-button--edit', getTournamentInfoForEdit)
});
