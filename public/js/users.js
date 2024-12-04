import { showModal, closeModal } from './modals.js';
import { renderTableRows, loadRenderedData } from "./admin_page_utils.js";

$(document).ready(function () {

    //                              <th class="users__list-table-header-item">ID</th>
    //                             <th class="users__list-table-header-item">Alias</th>
    //                             <th class="users__list-table-header-item">Rol</th>
    //                             <th class="users__list-table-header-item">Estado</th>
    //                             <th class="users__list-table-header-item">Fecha de Registro</th>
    //                             <th class="users__list-table-header-item">Última Conexión</th>
    //                             <th class="users__list-table-header-item">Acciones</th>
    function generateUserRowTemplate(user) {
        return `<tr class="users__list-table-row">
                        <td class="users__list-table-item" id="user-id">${user.id}</td>
                        <td class="users__list-table-item">${user.alias_usuario}</td>
                        <td class="users__list-table-item">${user.rol}</td>
                        <td class="users__list-table-item">
                            <span class="users__list-status users__list-status--${user.activo ? 'active' : 'inactive'}">${user.activo ? 'ACTIVO' : 'INACTIVO'}</span>
                        </td>
                        <td class="users__list-table-item">${user.fecha_registro}</td>
                        <td class="users__list-table-item">${user.ultima_conexion}</td>
                        <td class="users__list-table-item users__list-table-item--actions">
                            <button class="users__list-table-button users_list-table-button--change-rol" data-id="${user.id}">Cambiar Rol</button>
                            <button class="users__list-table-button users_list-table-button--${user.activo ? 'activate' : 'desactivate'}">${user.activo ? 'ACTIVAR' : 'DESACTIVAR'}</button>
                        </td>
                    </tr>`
    }
    /**
     * Funcion que obtiene los valores del filtro, y dependiendo del filtro obtiene unos usuarios u otros,
     * En caso de que no exista filtro, muestro todos los usuarios seguidamente muestra estos torneos en #user-list
     */
    function loadUsersByStatus() {
        //Obtención de los estados de cada filtro
        let alias = $('#user-alias-search').val()
        let role = $('#filter-role').val()
        let status = $('#filter-status').val()
        let registrationStart = $('#registration-start').val()
        let registrationEnd = $('#registration-end').val()
        loadRenderedData('admin/users/list', {alias, role, status, registrationStart, registrationEnd}, (data) => {
            let users = data.users
            let rows = renderTableRows(users, generateUserRowTemplate)
            $('#user-list').empty().append(rows);
        })
    }

    $('#sidebar-users').on('click', loadUsersByStatus)
    $('#user-filter-button').on('click', loadUsersByStatus)
});