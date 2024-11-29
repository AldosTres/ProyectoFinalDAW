document.addEventListener('DOMContentLoaded', () => {
    function addtournamentsToTable (tournaments) {
        let rows = ``
        for (const tournament of tournaments) {
            rows += `<tr class="tournaments-list-table-row">
                        <td class="tournaments__list-table-item">${tournament.nombre}</td>
                        <td class="tournaments__list-table-item">${tournament.fecha_inicio}</td>
                        <td class="tournaments__list-table-item">${tournament.fecha_fin}</td>
                        <td class="tournaments__list-table-item">
                            <span class="tournaments__list-status tournaments__list-status--active">${tournament.activo}</span>
                        </td>
                        <td class="tournaments__list-table-item tournaments__list-table-item--actions">
                            <button class="tournaments__list-table-button tournaments_list-table-button--edit">Editar</button>
                            <button class="tournaments__list-table-button tournaments_list-table-button--delete">Eliminar</button>
                        </td>
                    </tr>`
        }
        // Evita duplicación de datos al añadir de nuevo contenido
        $('#tournament-list').empty().append(rows)
    }

    function loadAllTournaments() {
        $.ajax({
            type: 'GET',
            url: 'admin/tournament/list',
            success: function(response) {
                let data = JSON.parse(response);
                if (data.status === 'success') {
                    let tournaments = data.tournaments
                    addtournamentsToTable(tournaments)
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText)
            }

        })
        // addtournamentToTable();
    }

    function loadTournamentsByStatus() {
        console.log('no funciona algo')

        let status = $('#filter-status').val();
        $.ajax({
            type: 'GET',
            url: 'admin/tournament/list',
            data: {status}, //Envío a la función del Home.php, 
            success: function (response) {
                let data = JSON.parse(response);
                if (data.status === 'success') {
                    let tournaments = data.tournaments
                    addtournamentsToTable(tournaments)
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText)
            }
        });
    }

    //Método JQuery parecido a AddEventListener, ya que al devolver un objeto JQuery, debo aplicar un método igual
    $('#tournaments').on('click', loadAllTournaments)
    $('#filter-button').on('click', loadTournamentsByStatus)
})