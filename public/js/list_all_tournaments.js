document.addEventListener('DOMContentLoaded', () => {
    // let tournamentsButton = document.getElementById('tournaments')

    function addtournamentToTable (tournaments) {
        // let tournamentList = document.getElementById('tournament-list')
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
        // $('#tournament-list').empty().text('sjdhfkjsdfh')
    }

    function chargeOnAllTournaments() {
        console.log('funciona')
        $.ajax({
            type: 'GET',
            url: 'admin/tournament/list',
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    var tournaments = data.tournaments
                    // var ultimoMensaje = data.ultimoMensaje;
                    // mensajes.forEach(function(mensaje) {
                    //     agregarMensaje(mensaje.mensaje, mensaje.emisor);
                    // });
                    // agregarUltimoMensaje(ultimoMensaje.mensaje, ultimoMensaje.emisor);
                    addtournamentToTable(tournaments)
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText)
            }

        })
        // addtournamentToTable();
    }

    //Método JQuery parecido a AddEventListener, ya que al devolver un objeto JQuery, debo aplicar un método igual
    $('#tournaments').on('click', chargeOnAllTournaments)
    
})
console.log("Archivo JS externo cargado correctamente");