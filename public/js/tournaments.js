import { showModal, closeModal } from './modals.js'
import { renderItems, loadRenderedData, renderPagination } from "./admin_page_utils.js"

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
        const isActive = tournament.activo == 1 ? true : false; // Verifica si el usuario está activo (1 para activo, 0 para inactivo) VALOR TINYINT
        return `<tr class="tournaments__list-table-row">
                    <td class="tournaments__list-table-item" id="tournament-id">${tournament.id}</td>
                    <td class="tournaments__list-table-item">${tournament.nombre}</td>
                    <td class="tournaments__list-table-item">${tournament.fecha_inicio}</td>
                    <td class="tournaments__list-table-item">${tournament.fecha_fin}</td>
                    <td class="tournaments__list-table-item">
                        <span class="tournaments__list-status tournaments__list-status--active" status-id-data="${tournament.activo}">${isActive ? 'Activo' : 'Inactivo'}</span>
                    </td>
                    <td class="tournaments__list-table-item tournaments__list-table-item--actions">
                        <div class="tooltip-container" data-id="${tournament.id}">
                            <button class="tournaments__list-table-button list-table-button tournaments__list-table-button--edit" data-id="${tournament.id}"><i class="fa-regular fa-pen-to-square"></i></button>
                            <span class="tooltip-container__text">Editar torneo</span>
                        </div>
                        <div class="tooltip-container">
                            <button class="tournaments__list-table-button list-table-button tournaments__list-table-button--change-status" data-id="${tournament.id}">${isActive ? '<i class="fa-solid fa-xmark"></i>' : '<i class="fa-solid fa-check"></i>'}</button>
                            <span class="tooltip-container__text">${isActive ? 'Desactivar Torneo' : 'Activar torneo'}</span>
                        </div>
                        <div class="tooltip-container">
                            <button class="tournaments__list-table-button list-table-button tournaments__list-table-button--show-participants" data-id="${tournament.id}"><i class="fa-regular fa-id-card"></i></button>
                            <span class="tooltip-container__text">Mostrar participantes</span>
                        </div>

                        <div class="tooltip-container">
                            <button class="tournaments__list-table-button list-table-button tournaments__list-table-button--manage-tournament" data-id="${tournament.id}"><i class="fa-solid fa-gears"></i></button>
                            <span class="tooltip-container__text">Gestiornar torneo</span>
                        </div>
                    </td>
                </tr>`
    }

    /**
     * Funcion que obtiene el valor del estado del filtro, y dependiendo del filtro obtiene unos torneos u otros,
     * En caso de que no exista filtro, muestra todos seguidamente muestra estos torneos en #tournament-list
     */
    function loadTournamentsByStatus(page = 1) {
        const itemsPerPage = 8
        let status = $('#tournament-filter-status').val()
        let name = $('#tournament-search').val()
        let url = `admin/tournament/list/${page}/${itemsPerPage}`
        loadRenderedData('GET',url, {status, name}, (data) => {
            let tournaments = data.tournaments
            let rows = renderItems(tournaments, generateTournamentRowTemplate)
            $('#tournament-list').empty().append(rows)

            // Genero la paginación
            renderPagination(data.total_pages, page, loadTournamentsByStatus, 'tournaments');

        })
    }
    
    //Método JQuery parecido a AddEventListener, ya que al devolver un objeto JQuery, debo aplicar un método igual
    $('#sidebar-tournaments').on('click', () => loadTournamentsByStatus())
    $('#tournaments-filter-button').on('click', () => loadTournamentsByStatus())


    /**
     * Función que genera una plantilla HTML para un formulario de torneo con datos prellenados. 
     * @param {*} tournament 
     * @returns 
     */
    function generateTournamentFormTemplate(tournament) {
        return `<form method="post" class="tournaments__form form" enctype="multipart/form-data" id="edit-tournament-form">
                    <div class="tournaments__field form__field">
                        <label for="tournament-name" class="tournaments__field-label form__field-label">Nombre del torneo:</label>
                        <input type="text" name="edit-name" id="edit-tournament-name" class="tournaments__field-input form__field-input" value="${tournament.nombre}">
                    </div>
                    <div class="tournaments__field form__field">
                        <label for="tournament-start-date" class="tournaments__field-label form__field-label">Fecha de inicio:</label>
                        <input type="date" name="edit-start-date" id="edit-tournament-start-date" class="tournaments__field-input form__field-input" value="${tournament.fecha_inicio}">
                    </div>
                    <div class="tournaments__field form__field">
                        <label for="tournament-end-date" class="tournaments__field-label form__field-label">Fecha de finalización:</label>
                        <input type="date" name="edit-end-date" id="edit-tournament-end-date" class="tournaments__field-input form__field-input" value="${tournament.fecha_fin}">
                    </div>
                    <div class="tournaments__field form__field">
                        <label for="tournament-logo" class="tournaments__field-label form__field-label">Logotipo del torneo:</label>
                        <input type="file" name="edit-logo" id="edit-tournament-logo" class="tournaments__field-input form__field-input tournaments__field-input--file" accept=".jpg" required>
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
                closeModal()
                showModal(data.title, data.message)
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
    $('.tournaments__list-table').on('click', '.tournaments__list-table-button--edit', handelTournamentEdit)


    /**
     * Función que permite actualizar el estado del torneo
     */
    function updateTournamentStatus() {
        let tournamentId = $(this).attr('data-id'); // Obtiene el ID del usuario
        let tournamentStatus = $('.tournaments__list-status').attr('status-id-data');
        if (!tournamentId || !tournamentStatus) {
            showModal('Error', 'No se pudo obtener el ID o estado del torneo.')
        } else {
            let url = `admin/tournament/change-status/${tournamentId}`
            loadRenderedData('GET', url, {tournamentStatus}, (data) => {
                showModal(data.title, data.message)
            })
        }
    }
    $('#tournament-list').off('click', '.tournaments__list-table-button--change-status').on('click', '.tournaments__list-table-button--change-status', updateTournamentStatus)

    /**
     * Función que genera un template de un particpante
     * @param {*} participant 
     * @returns 
     */
    function generateParticipantRowTemplate(participant) {
        let isActive = participant.activo == 1 ? true : false
        return `<tr class="participants__list-table-row">
                    <td class="participants__list-table-item">${participant.id}</td>
                    <td class="participants__list-table-item">${participant.alias}</td>
                    <td class="participants__list-table-item participants__list-table-item--actions">
                        <div class="tooltip-container">
                            <button class="participants__list-table-button list-table-button participants__list-table-button--change-status" data-id="${participant.id}" status-data-id="${participant.activo}">${isActive ? '<i class="fa-solid fa-user-slash"></i>' : '<i class="fa-solid fa-user-check"></i>'}</button>
                            <span class="tooltip-container__text">${isActive ? 'Banear usuario' : 'Restablecer usuario'}</span>
                        </div>  
                    </td>
                </tr>`
    }

    /**
     * Función que me permite mostrar por pantalla un listado de participantes correspondientes a un torneo
     */
    function handleTournamentParticipants() {
        let tournamentId = $(this).attr('data-id')
        let participantsTable = `<table class="participants__list-table table table-hover table-striped" id="participants-table">
                                    <thead class="participants__list-table-header table-header">
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
            showModal('Participantes', participants.length > 0 ? participantsTable : 'No hay usuarios inscritos')
        })        
    }
    $('.tournaments__list-table').on('click', '.tournaments__list-table-button--show-participants', handleTournamentParticipants)


    /**
     * Función que permite cambiar el estado del participante del de activo a inactivo
     */
    function updateParticipantStatus (){
        let participantId = $(this).attr('data-id')
        let participantStatus = $(this).attr('status-data-id')
        console.log('hola')
        if (!participantId || !participantStatus) {
            showModal('Error', 'No se pudo obtener el ID o estado del participante.')
        } else {
            let url = `admin/tournament/participants/${participantId}/change-status`
            loadRenderedData('GET', url, {participantStatus}, (data) => {
                showModal(data.title, data.message)
            })
        }
    }

    $('#participants-table').on('click', '.participants__list-table-button--change-status', updateParticipantStatus)


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
     * Función que devuelve un template u otro dependiendo de si nos encontramos en la primera ronda o si existe un enfrentamiento en una posición
     * @param {*} isFirstRound 
     * @param {*} tournamentId 
     * @returns 
     */
    function createMatchHtml(isFirstRound, tournamentId, matchPosition, roundTypeId, existsMatch = false, match = null) {
        if (existsMatch) {
            if (roundTypeId == match['id_tipo_ronda']) {
                return `
                <div class="tournament__bracket-match" match-position-data="${matchPosition}">
                    <div class="tournament__bracket-score">${match['ganador_alias']??'Esperando Ganador...'}</div>
                    <div class="tournament__bracket-participants">
                        <div class="tournament__bracket-first-participant tournament__bracket-participant" id="" participant-id-data="${match['participante1_id']}" round-type-id-data="${roundTypeId}" round-id-data="${match['id']}">
                            <span class="tournament__bracket-participant-alias">${match['participante1_alias']}</span>
                            <img src="${BASE_URL}img/perfil_usuarios/${match['participante1_foto']}" alt="perfil_usuario" width="32" height="32" class="rounded-circle">
                        
                        </div>
                        <div class="vs">
                        <p>vs</p>
                        </div>
                        <div class="tournament__bracket-second-participant tournament__bracket-participant" id="" participant-id-data="${match['participante2_id']}" round-type-id-data="${roundTypeId}" round-id-data="${match['id']}">
                            <img src="${BASE_URL}img/perfil_usuarios/${match['participante2_foto']}" alt="perfil_usuario" width="32" height="32" class="rounded-circle">
                            <span class="tournament__bracket-participant-alias">${match['participante2_alias']}</span>
                        </div>
                        
                    </div>
                </div>`
            } else {
                return `
                <div class="tournament__bracket-match" match-position-data="${matchPosition}">
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
                    <div class="tournament__bracket-match" match-position-data="${matchPosition}">
                        <div class="tournament__bracket-score">Esperando Ganador...</div>
                        <div class="tournament__bracket-participants">
                            <button class="tournament__bracket-add-participants-btn" id="add-participants" value="" match-position-data="${matchPosition}" round-type-id-data="${roundTypeId}">
                                Añadir Participantes
                                <i class="fa-solid fa-circle-plus"></i>
                            </button>
                        </div>
                    </div>`
            }
            return `
                <div class="tournament__bracket-match" match-position-data="${matchPosition}">
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
     * Genera un bracket dinámico para un torneo en formato HTML.
     *
     * Esta función toma la información de las rondas de un torneo y genera un bracket visual que
     * incluye los enfrentamientos de cada ronda. Es compatible con datos de enfrentamientos existentes
     * (opcional) para actualizar el bracket con información predefinida.
     *
     * @param {Array} tournamentRounds - Array que contiene los datos de las rondas del torneo. Cada elemento
     *                                   debe incluir al menos un nombre o identificador para la ronda.
     * @param {Number|String} tournamentId - Identificador único del torneo, que se incluirá en los atributos
     *                                       de los elementos generados.
     * @param {Array|null} matchs - (Opcional) Arreglo con los datos de los enfrentamientos existentes. Cada
     *                              elemento debe contener información como `posicion_enfrentamiento` e
     *                              `id_tipo_ronda` para asignarlos correctamente al bracket.
     *
     * @returns {void} - La función no devuelve valores, pero actualiza el contenido de un contenedor
     *                   HTML con el id `#tournament-bracket` para mostrar el bracket generado.
     */
    function generateBracketHtml(tournamentRounds, tournamentId, matchs = null) {
        const numParticipants = 8
        let html = ``
        html += `<div class="tournament__bracket" data-id="${tournamentId}">`
        tournamentRounds.forEach((round, index) => {
            html += `<div class="tournament__bracket-round" data-id="${index + 1}">
                         <h2 class="tournament__bracket-round-title">${round.nombre}</h2>`
            // Generar los enfrentamientos de esta ronda
            const numMatches = calculateNumMatches(numParticipants,index)
            for (let j = 0; j < numMatches; j++) {
                let currentMatch = null;
                if (matchs) {
                    // Busca el match específico para esta posición y tipo de ronda
                    currentMatch = matchs.find(match => 
                        match['posicion_enfrentamiento'] == (j + 1) &&
                        match['id_tipo_ronda'] == (index + 1)
                    );
                }
                //Con !! convertimos a currentMatch en un booleano
                //Cuando currentMatch sea null o undefined, devolverá false, y true cuando se un objeto
                html += createMatchHtml(index === 0, tournamentId, j + 1, index + 1, !!currentMatch, currentMatch);
            }
            html += `</div>`
        })
        html += `</div>`
        $("#tournament-bracket").removeClass('menu__section--hidden')
        $("#tournament-bracket").html(html)
    }
    /**
     * Función que busca información en el servidor para pasar información necesaria a generateBracketHtml
     */
    function loadAndRenderTournamentBracket() {
        // $('#tournament-bracket').empty();
        $('#tournaments').addClass('menu__section--hidden')
        const tournamentId = $(this).attr('data-id');
        const url = `admin/tournament/bracket/${tournamentId}`;
    
        loadRenderedData('GET', url, {}, (data) => {
            const rounds = data.rounds_type || [];
            const matches = data.matches || [];
    
            generateBracketHtml(rounds, tournamentId, matches);
        });
    }
    

    $('.tournaments__list-table').on('click', '.tournaments__list-table-button--manage-tournament', loadAndRenderTournamentBracket)
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
        const tournamentId = $('.tournament__bracket').attr('data-id')
        const matchPosition = $(this).attr('match-position-data')
        const roundTypeId = $(this).attr('round-type-id-data')
        const existsBracket = true

        const participantsFormStart = `
            <form id="add-participant-form" class="participants__form">
                <ul class="participants__form-list">`
    
        loadRenderedData('GET', 'admin/tournament/participants', { tournamentId, existsBracket}, (data) => {
            // Verificar si los participantes existen y no son nulos
            const participants = data.participants || [];
            const rows = participants.length > 0 
                ? renderItems(participants, generateParticipantItemHtml) 
                : `<li>No hay participantes disponibles para este torneo.</li>`
    
            // Completar el formulario
            const participantsForm = `
                ${participantsFormStart}
                    ${rows}

                </ul>
            </form>`
            if (participants.length > 0) {
                // Mostrar modal con el formulario
                showModal('Selecciona participantes', participantsForm, () => {
                    //Para que el boton confirmar solo cierre en caso de que participants no tenga ningun elemento
                    const selectedCheckboxes = $('.participants__form-item-checkbox:checked');
                    if (selectedCheckboxes.length !== 2) {
                        showModal('Elección de participantes', 'Tienes que seleccionar exactamente 2 participantes.')
                    }
                    // Obtener los valores seleccionados
                    const selectedParticipants = [];
                    selectedCheckboxes.each(function () {
                        selectedParticipants.push($(this).val())
                    })
                    // Enviar los participantes seleccionados
                    submitParticipantForm(tournamentId, matchPosition, roundTypeId, selectedParticipants[0], selectedParticipants[1])
                })
            } else {
                showModal('Selecciona participantes', participantsForm)
            }
        });
    }
    
    $('#tournament-bracket').on('click', '.tournament__bracket-add-participants-btn', loadAndDisplayParticipantsForm)

    /**
     * Función que añade enfrentamientos a un torneo específico
     * @param {*} tournamentId 
     * @param {*} matchPosition 
     * @param {*} roundTypeId 
     * @param {*} firstParticipantId 
     * @param {*} secondParticipantId 
     */
    function submitParticipantForm(tournamentId, matchPosition, roundTypeId, firstParticipantId, secondParticipantId) {
        
        let url = `admin/tournament/bracket/${tournamentId}/add-participant`
        loadRenderedData('POST', url, {matchPosition, roundTypeId, firstParticipantId, secondParticipantId}, (data) => {
            // closeModal()
            showModal(data.title, data.message)
        })
    }

    /**
     * Genera un componente HTML para un criterio de evaluación en forma de input tipo range.
     * 
     * Esta función crea dinámicamente un elemento de formulario que permite evaluar un criterio
     * mediante un control deslizante (`input:range`). Es útil para integrar criterios personalizados
     * en el sistema de evaluación.
     *
     * @param {Object} criteria - Objeto que contiene la información del criterio.
     *        - `criteria.nombre` (String): Nombre del criterio, que se usará como etiqueta y como identificador del input.
     *        - `criteria.id` (Number): Identificador único del criterio, asignado como atributo en el input.
     *
     * @returns {String} - Una cadena de texto HTML que representa el componente del criterio.
     */
    function generateRoundResultForm(criteria) {
        return `
                <div class="round-vote-form__criteria-item">
                    <label class="round-vote-form__label" for="${criteria.nombre}">${criteria.nombre}:</label>
                    <div class="round-vote-form__input-container">
                        <input 
                        class="round-vote-form__input" 
                        type="range" 
                        id="${criteria.nombre}"
                        name="${criteria.nombre}"
                        min="1" 
                        max="10" 
                        step="0.5" 
                        value="5"
                        criteria-id-data="${criteria.id}">
                        <div id="${criteria.nombre}-value" class="round-vote-form__input-value"></div>
                    </div>
                </div>`
    }

    /**
     * Muestra un formulario en un modal para evaluar a un participante en una ronda.
     * 
     * Esta función se ejecuta al hacer clic en un participante del bracket del torneo. Carga 
     * dinámicamente un formulario que incluye un video relacionado con el participante y criterios
     * de evaluación representados por controles deslizantes.
     *
     * @param {Object} params - Parámetros adicionales (opcional).
     */
    function handleRoundResultForm() {
        let participantId = $(this).attr('participant-id-data')
        let roundTypeId = $(this).attr('round-type-id-data')
        let roundId = $(this).attr('round-id-data')
        let matchPosition = $('.tournament__bracket-match').attr('match-position-data')
        // $roundId, $position, $participantId
        let urlprimera = `tournament/showvideos/${roundId}/${participantId}`
        console.log(urlprimera)
        let video = ``
        loadRenderedData('GET',urlprimera,{},(data)=> {
            video = data.video_url
            // showModal('hola', data.message)
            let criteriaFormStart = `
                                <form class="round-vote-form" participant-id-data="${participantId}" round-type-id-data="${roundTypeId}" round-id-data="${roundId}">
                                    <!-- Contenedor del iframe -->
                                    <div class="round-vote-form__video-container">
                                        ${data.video_url ?? 'Aún no se ha subido video'}
                                    </div>
                                    <!-- Contenedor de los criterios -->
                                    <div class="round-vote-form__criteria">
                                `
        loadRenderedData('GET', 'admin/tournament/scoring-criteria', {}, (data) => {
            
            let rows = renderItems(data.scoring_criteria, generateRoundResultForm)
            let criteriaForm = `
                                    ${criteriaFormStart}
                                        ${rows}
                                    </div>
                                </form>
                                `
            showModal('Calificación participante', criteriaForm, submitRoundResultForm)

            //Muestro los valores del input:range de cada criterio       
            data.scoring_criteria.forEach((criteria, index) => {
                //Primero establezco para cada uno que se vea su valor por defecto
                $(`#${criteria.nombre}-value`).html($(`#${criteria.nombre}`).val());

                $('#modal').on('input', `#${criteria.nombre}`, function () {
                    $(`#${criteria.nombre}-value`).html($(`#${criteria.nombre}`).val());
                });    
            })
        })

        })
        
    }
    $('#tournament-bracket').on('click', '.tournament__bracket-participant', handleRoundResultForm)

    /**
     * Envía las puntuaciones de un participante en una ronda al servidor.
     * 
     */
    function submitRoundResultForm() {
        let tournamentId = $('.tournament__bracket').attr('data-id');
        let participantId = $('.round-vote-form').attr('participant-id-data');
        let roundId = $('.round-vote-form').attr('round-id-data');
        let scores = []
        // Llamada para obtener criterios de puntuación
        loadRenderedData('GET', 'admin/tournament/scoring-criteria', {}, (data) => {
            if (data && data.scoring_criteria) {
                // Usamos .map para generar el array de puntuaciones
                scores = data.scoring_criteria.map((criterion) => {
                    const criterionId = $(`#${criterion.nombre}`).attr('criteria-id-data');
                    const score = $(`#${criterion.nombre}`).val();
    
                    // Devolvemos el objeto solo si ambos valores son válidos
                    return criterionId && score ? { criterionId, score } : null;
                }).filter(Boolean); // Eliminamos valores nulos o inválidos
    
                if (scores.length === 0) {
                    showModal('Error', 'No se seleccionaron puntuaciones válidas.');
                    return;
                }
                
                let url = `admin/tournament/${tournamentId}/round/${roundId}/participant/${participantId}/scores`;
                loadRenderedData('POST', url, { scores: JSON.stringify(scores) }, (data) => {
                    showModal(data.title, data.message);
                });
            } else {
                showModal('Error', 'No se pudieron cargar los criterios de puntuación.');
            }
        })
    }    
})