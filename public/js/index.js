// import { showModal, closeModal } from './modals.js'
import { renderItems, loadRenderedData, renderPagination } from "./admin_page_utils.js"
$(document).ready(function () {
    /**
     * Array de valores para la seccion sobre nosotros
     */
    const jumpstyleValues = [
        {
            "title": "Pasión por el Jumpstyle",
            "description": "Celebramos y promovemos el Jumpstyle como una forma de expresión artística que conecta a personas de todo el mundo."
        },
        {
            "title": "Unión y Comunidad",
            "description": "Creemos en el poder del Jumpstyle para derribar barreras culturales y unir a bailarines de diferentes lugares del mundo."
        },
        {
            "title": "Creatividad y Autenticidad",
            "description": "Fomentamos la innovación en el baile, apoyando a todos los que buscan encontrar su estilo único."
        },
        {
            "title": "Disciplina y Superación",
            "description": "Inspiramos a los bailarines a trabajar duro y desafiarse a sí mismos, alcanzando nuevas metas en cada competición."
        },
        {
            "title": "Diversión y Energía",
            "description": "Celebramos la energía vibrante del Jumpstyle, recordando que bailar es una experiencia que debe disfrutarse."
        },
        {
            "title": "Respeto Mutuo",
            "description": "Valoramos el respeto entre los participantes, creando un ambiente positivo en cada torneo."
        },
        {
            "title": "Longevidad del Jumpstyle",
            "description": "Trabajamos para preservar y expandir la esencia del Jumpstyle, asegurando que continúe inspirando a futuras generaciones."
        }
    ]

    /**
     * Array de propietarios para la seccion sobre nosotros
     */

    const jlsOwners = [
        {
            'name': 'Daiser',
            'url_image':'daiser.jpeg'
        },
        {
            'name': 'Ivi',
            'url_image':'ivi.jpg'
        },
        {
            'name': 'Dejux',
            'url_image':'djuana.jpg'
        }
    ]
    
    /**
     * Función que genera los valores de la empresa en la página
     */
    function generateValues() {
        let values = ``
        jumpstyleValues.forEach(value => {
            values += `<div class="about-us__value-card">
                                <p class="about-us__value-title">${value['title']}</p>
                                <p class="about-us__value-description">${value['description']}</p>
                              </div>`
        })
        $('.values__list').html(values)

    }
    generateValues()

     /**
     * Función que genera el contenido de los propietarios en la seccion sobre nosotros
     */
    function generateOwners() {
        let owners = ``
        jlsOwners.forEach(owner => {
            owners += `<div class="team__owner-card">
                            <img src="${BASE_URL}img/propietarios/${owner['url_image']}" alt="">
                            <div class="team__owner-card-content">
                                <p class="team__owner-card-title">${owner['name']}</p>
                            </div>
                        </div>`
        })

        $('.team__owners-container').html(owners)
    }
    generateOwners()



    
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
    //Mostrar el tournament-bracker en página de cada torneo

    /**
     * Genera el HTML para un enfrentamiento en el bracket del torneo.
     *
     * @param {boolean} isFirstRound - Indica si es la primera ronda.
     * @param {number|string} tournamentId - Identificador del torneo.
     * @param {number} matchPosition - Posición del enfrentamiento dentro de la ronda.
     * @param {number} roundTypeId - Tipo o número de la ronda.
     * @param {boolean} existsMatch - Indica si el enfrentamiento ya existe.
     * @param {Object|null} match - Información del enfrentamiento (opcional).
     * @returns {string} - HTML del enfrentamiento.
     */
    function createMatchHtml(isFirstRound, tournamentId, matchPosition, roundTypeId, existsMatch = false, match = null) {
        if (existsMatch) {
            return `
                <div class="tournament__bracket-match" match-position-data="${matchPosition}">
                    <div class="tournament__bracket-date">Fecha pendiente</div>
                    <div class="tournament__bracket-score">${match['resultado'] ?? 'Esperando Ganador...'}</div>
                    <div class="tournament__bracket-participants">
                        <div class="tournament__bracket-first-participant tournament__bracket-participant">
                            <span class="tournament__bracket-participant-alias">${match['participante1_alias'] ?? 'Desconocido'}</span>
                            <i class="fa-solid fa-circle-user tournament__bracket-participant-logo"></i>
                        </div>
                        <div class="vs"><p>vs</p></div>
                        <div class="tournament__bracket-second-participant tournament__bracket-participant">
                            <i class="fa-solid fa-circle-user tournament__bracket-participant-logo"></i>
                            <span class="tournament__bracket-participant-alias">${match['participante2_alias'] ?? 'Desconocido'}</span>
                        </div>
                    </div>
                </div>`;
        } else {
            return `
                <div class="tournament__bracket-match" match-position-data="${matchPosition}">
                    <div class="tournament__bracket-date">Fecha pendiente</div>
                    <div class="tournament__bracket-score">Esperando Ganador...</div>
                    <div class="tournament__bracket-participants">
                        <div class="tournament__bracket-first-participant">Esperando resultado...</div>
                        <div class="vs">VS</div>
                        <div class="tournament__bracket-second-participant">Esperando resultado...</div>
                    </div>
                </div>`;
        }
    }

    /**
     * Genera el HTML del bracket del torneo basado en las rondas y enfrentamientos.
     *
     * @param {Array} tournamentRounds - Lista de rondas del torneo.
     * @param {number|string} tournamentId - Identificador único del torneo.
     * @param {Array|null} matches - Lista de enfrentamientos existentes (opcional).
     */
    function generateBracketHtml(tournamentRounds, tournamentId, matches = null) {
        const numParticipants = 8; // Cambiar según el diseño del torneo
        let html = `<div class="tournament__bracket" data-id="${tournamentId}">`;

        tournamentRounds.forEach((round, index) => {
            html += `<div class="tournament__bracket-round" data-id="${index + 1}">
                        <h2 class="tournament__bracket-round-title">${round.nombre}</h2>`;
            const numMatches = calculateNumMatches(numParticipants, index);
            for (let j = 0; j < numMatches; j++) {
                const currentMatch = matches?.find(match => 
                    match['posicion_enfrentamiento'] == (j + 1) && 
                    match['id_tipo_ronda'] == (index + 1)
                );
                html += createMatchHtml(index === 0, tournamentId, j + 1, index + 1, !!currentMatch, currentMatch);
            }
            html += `</div>`;
        });

        html += `</div>`;
        $("#tournament-bracket").html(html);
    }

    /**
     * Carga los datos de las rondas y enfrentamientos del torneo desde el servidor,
     * y genera el HTML del bracket.
     */
    function loadAndRenderTournamentBracket(tournamentId) {
        const url = `admin/tournament/bracket/${tournamentId}`; // Ruta para obtener datos del torneo
    
        // Llamar al servidor y cargar el bracket
        loadRenderedData('GET', url, {}, (data) => {
            const rounds = data.rounds_type || [];
            const matches = data.matches || [];
    
            generateBracketHtml(rounds, tournamentId, matches); // Generar el HTML dinámico del bracket
        });
    }
    
    // $('.available-tournaments__action').on('click', loadAndRenderTournamentBracket)

    const urlParts = window.location.pathname.split('/'); // Divide la URL en partes
    const tournamentId = urlParts[urlParts.length - 1]; // Última parte de la URL

    if (tournamentId) {
        loadAndRenderTournamentBracket(tournamentId); // Llamar a la función para cargar el bracket
        console.log(tournamentId)
    }
    
});