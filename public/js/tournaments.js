import { showModal, closeModal } from './modals.js'
import { renderItems, loadRenderedData } from "./admin_page_utils.js"

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
                            <button class="tournaments__list-table-button tournaments_list-table-button--manage-tournament" data-id="${tournament.id}">Gestionar torneo</button>
                        </td>
                    </tr>`
    }

    /**
     * Funcion que obtiene el valor del estado del filtro, y dependiendo del filtro obtiene unos torneos u otros,
     * En caso de que no exista filtro, muestra todos seguidamente muestra estos torneos en #tournament-list
     */
    function loadTournamentsByStatus() {
        let status = $('#tournament-filter-status').val()
        loadRenderedData('GET','admin/tournament/list', {status}, (data) => {
            let tournaments = data.tournaments
            let rows = renderItems(tournaments, generateTournamentRowTemplate)
            $('#tournament-list').empty().append(rows)
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
     * Función que se encarga de procesar y enviar los datos del formulario de edición de un torneo al servidor
     * @param {*} form 
     */
    function submitEditTournamentForm(form) {
        let formData = new FormData(form[0]) //Para trabajar directamente con el objeto DOM y no con el objeto jquery
        
        loadRenderedData('POST', 'admin/tournament/update', formData, (data) => {
                closeModal()          // Cierra el modal
                showModal(data.title, data.message) // Muestra mensaje del servidor
        }, true)
    }

    /**
     * Obtiene la información de un torneo para editarla y muestra un modal con un formulario prellenado.
     */
    function handelTournamentEdit() {
        let tournamentId = $(this).attr('data-id')
        loadRenderedData('GET', 'admin/tournament/get-data-for-edit', {tournamentId}, (data) => {
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


    /**
     * 
     * @param {*} participant 
     * @returns 
     */
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
        let tournamentId = $(this).attr('data-id')
        let participantsTable = `<table class="participants__list-table">
                                    <thead class="participants__list-table-header">
                                        <tr class="participants__list-table-row">
                                            <th class="participants__list-table-header-item">Id Inscripción</th>
                                            <th class="participants__list-table-header-item">Alias</th>
                                            <th class="participants__list-table-header-item">Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="participants__list-table-body" id="participants-list">`

        loadRenderedData('GET', 'admin/tournament/participants',{tournamentId}, (data) => {
            let participants = data.participants
            let rows = renderItems(participants, generateParticipantRowTemplate)
            participantsTable += rows
            participantsTable += `      </tbody>
                                </table>`
            showModal('Participantes', participantsTable)
        })        
    }
    $('.tournaments__list-table').on('click', '.tournaments_list-table-button--show-participants', handleTournamentParticipants)

    /**
     * Función que calcula el número de enfrentamientos de un torneo dependiendo del numero de participantes y el índice de ronda
     * en el que se encuentre
     * @param {*} numParticipants 
     * @param {*} roundIndex 
     * @returns 
     */
    function calculateNumMatches(numParticipants, roundIndex) {
        return numParticipants / Math.pow(2, roundIndex + 1) //8/2^1, 8/2^2, 8/2^3
    }

    /**
     * Función que devuelve un template u otro dependiendo de isFirstRound(bool)
     * @param {*} isFirstRound 
     * @param {*} tournamentId 
     * @returns 
     */
    function createMatchHtml(isFirstRound, tournamentId, matchPosition, roundId, existsMatch = false, match = null) {
        if (existsMatch) {
            if (roundId == match['id_tipo_ronda']) {
                return `
                <div class="tournament__bracket-match" data-id="${matchPosition}">
                    <div class="tournament__bracket-date">18 Junio 2025</div>
                    <div class="tournament__bracket-score">Esperando Ganador...</div>
                    <div class="tournament__bracket-participants">
                        <div class="tournament__bracket-first-participant tournament__bracket-participant" id="">
                            <span class="tournament__bracket-participant-alias">${match['participante1_alias']}</span>
                            <i class="fa-solid fa-circle-user tournament__bracket-participant-logo"></i>
                        </div>
                        <div class="vs">
                        <p>vs</p>
                        </div>
                        <div class="tournament__bracket-second-participant tournament__bracket-participant" id="">
                            <i class="fa-solid fa-circle-user tournament__bracket-participant-logo"></i>
                            <span class="tournament__bracket-participant-alias">${match['participante2_alias']}</span>
                        </div>
                        
                    </div>
                </div>`
            } else {
                return `
                <div class="tournament__bracket-match" data-id="${matchPosition}">
                    <div class="tournament__bracket-date">18 Junio 2025</div>
                    <div class="tournament__bracket-score">Esperando Ganador...</div>
                    <div class="tournament__bracket-participants">
                        <div class="tournament__bracket-first-participant tournament__bracket-participant" id="">Esperando resultado...</div>
                        <div class="vs">VS</div>
                        <div class="tournament__bracket-second-participant tournament__bracket-participant" id="">Esperando resultado...</div>
                    </div>
                </div>`
            }
        } else {
            if (isFirstRound) {
                return `
                    <div class="tournament__bracket-match" data-id="${matchPosition}">
                        <div class="tournament__bracket-date">18 Junio 2025</div>
                        <div class="tournament__bracket-score">Esperando Ganador...</div>
                        <div class="tournament__bracket-participants">
                            <button class="tournament__bracket-add-participants-btn" id="add-participants" value="" data-match-position="${matchPosition}" data-round-id="${roundId}">
                                Añadir Participantes
                                <i class="fa-solid fa-circle-plus"></i>
                            </button>
                        </div>
                    </div>`
            }
            return `
                <div class="tournament__bracket-match" data-id="${matchPosition}">
                    <div class="tournament__bracket-date">18 Junio 2025</div>
                    <div class="tournament__bracket-score">Esperando Ganador...</div>
                    <div class="tournament__bracket-participants">
                        <div class="tournament__bracket-first-participant tournament__bracket-participant" id="">Esperando resultado...</div>
                        <div class="vs">VS</div>
                        <div class="tournament__bracket-second-participant tournament__bracket-participant" id="">Esperando resultado...</div>
                    </div>
                </div>`
        }
    }
    

    /**
     * Función que genera un template de bracket de un torneo dinámico
     * @param {*} rounds 
     * @param {*} tournamentId 
     */
    function generateBracketHtml(tournamentRounds, tournamentId, matchs = null) {
        const numParticipants = 8
        let html = ``
        // Generar las rondas
        // html += `<div class="tela">
        //             <h2 class="tournament__bracket-round-title">Numero ronda</h2>
        //             <h2 class="tournament__bracket-round-title">Numero ronda</h2>
        //             <h2 class="tournament__bracket-round-title">Numero ronda</h2>
        //          </div>`
        // html += `<h2 class="tournament__bracket-round-title">Numero ronda</h2>`
        html += `<div class="tournament__bracket" data-id="${tournamentId}">`
        tournamentRounds.forEach((round, index) => {
            html += `<div class="tournament__bracket-round" data-id="${index + 1}">
                         <h2 class="tournament__bracket-round-title">${round.nombre}</h2>`
            
            // Generar los enfrentamientos de esta ronda
            const numMatches = calculateNumMatches(numParticipants,index)

            for (let j = 0; j < numMatches; j++) {
                if (matchs) {
                    html += createMatchHtml(index===0, tournamentId, j, index + 1,matchs.length > 0, matchs[j])
                } else {
                    html += createMatchHtml(index===0, tournamentId, j, index + 1)
                }
            }
            html += `</div>`
        })
        html += `</div>`
        $("#tournaments").html(html)
    }
    /**
     * Función que busca información en el servidor para pasar información necesaria a generateBracketHtml
     * @param {*} tournament
     */
    function loadAndRenderTournamentBracket() {
        $('#tournaments').empty()
        //Atributo data-id del boton manage-tournament
        let tournamentId = $(this).attr('data-id')
        const url = `admin/tournament/bracket/${tournamentId}`
        loadRenderedData('GET', url, {}, (data) => {
            const rounds = data.rounds_type || []
            // const t = data.tournament_id
            const matches = data.matches
            console.log(matches)
            if (matches.length == 0) {
                generateBracketHtml(rounds, tournamentId)
            } else {
                generateBracketHtml(rounds, tournamentId, matches)
            }
        })
    }

    $('.tournaments__list-table').on('click', '.tournaments_list-table-button--manage-tournament', loadAndRenderTournamentBracket)
    // Participante
    

    
    /**
     * Función que genera una template de elemento <li> que contiene el alias de un participante y un checkbox para seleccionarlo
     * @param {*} participant 
     * @returns 
     */

    function generateParticipantItemHtml(participant) {
        return `<li class="participants__form_item">
                    <input type="checkbox" class="participants__form-item-checkbox" id="participant-${participant.id}" name="participant" value="${participant.id}">
                    <label for="participant-${participant.id}" class="participants__form-item-label">${participant.alias}</label>
                </li>`
    }

    /**
     * Función que muestra en un modal un formulario para elegir participantes para el enfrentamiento de un torneo específico
     */
    function loadAndDisplayParticipantsForm() {
        const tournamentId = $('.tournament__bracket').attr('data-id');
        const matchPosition = $(this).attr('data-match-position');
        const roundId = $(this).attr('data-round-id');
        
        const participantsFormStart = `
            <form id="add-participant-form" class="participants__form">
                <ul class="participants__form-list">`;
    
        loadRenderedData('GET', 'admin/tournament/participants', { tournamentId }, (data) => {
            // Verificar si los participantes existen y no son nulos
            const participants = data.participants || [];
            const rows = participants.length > 0 
                ? renderItems(participants, generateParticipantItemHtml) 
                : `<li>No hay participantes disponibles para este torneo.</li>`;
    
            // Completar el formulario
            const participantsForm = `
                ${participantsFormStart}
                    ${rows}

                </ul>
            </form>`;
    
            // Mostrar modal con el formulario
            showModal('Selecciona participantes', participantsForm, () => {
                //Para que el boton confirmar solo cierre en caso de que participants no tenga ningun elemento
                if (participants.length > 0) {
                    const selectedCheckboxes = $('.participants__form-item-checkbox:checked');
                    if (selectedCheckboxes.length !== 2) {
                        showModal('Elección de participantes', 'Tienes que seleccionar exactamente 2 participantes.');
                        return;
                    }
        
                    // Obtener los valores seleccionados
                    const selectedParticipants = [];
                    selectedCheckboxes.each(function () {
                        selectedParticipants.push($(this).val());
                    });
        
                    // Enviar los participantes seleccionados
                    submitParticipantForm(tournamentId, matchPosition, roundId, selectedParticipants[0], selectedParticipants[1]);
                }
            });
        });
    }
    

    $('#tournaments').on('click', '#add-participants', loadAndDisplayParticipantsForm)


    function submitParticipantForm(tournamentId, matchPosition, roundId, firstParticipantId, secondParticipantId) {
        
        let url = `admin/tournament/bracket/${tournamentId}/add-participant`
        loadRenderedData('POST', url, {matchPosition, roundId, firstParticipantId, secondParticipantId}, (data) => {
            // closeModal()
            showModal(data.title, data.message)
        })
    }
});
