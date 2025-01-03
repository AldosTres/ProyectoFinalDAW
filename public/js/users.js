import { showModal, closeModal } from './modals.js'
import { renderItems, loadRenderedData, renderPagination } from "./admin_page_utils.js"

$(document).ready(function () {

    //                              <th class="users__list-table-header-item">ID</th>
    //                             <th class="users__list-table-header-item">Alias</th>
    //                             <th class="users__list-table-header-item">Rol</th>
    //                             <th class="users__list-table-header-item">Estado</th>
    //                             <th class="users__list-table-header-item">Fecha de Registro</th>
    //                             <th class="users__list-table-header-item">Última Conexión</th>
    //                             <th class="users__list-table-header-item">Acciones</th>
    function generateUserRowTemplate(user) {
        const isActive = user.activo == 1 ? true : false; // Verifica si el usuario está activo (1 para activo, 0 para inactivo) VALOR TINYINT
        return `<tr class="users__list-table-row">
                        <td class="users__list-table-item" id="user-id">${user.id}</td>
                        <td class="users__list-table-item">${user.alias_usuario}</td>
                        <td class="users__list-table-item">${user.rol_nombre}</td>
                        <td class="users__list-table-item">
                            <span class="users__list-status users__list-status--${isActive ? 'active' : 'inactive'}" data-id="${user.activo}">${isActive ? 'Activo' : 'Inactivo'}</span>
                        </td>
                        <td class="users__list-table-item">${user.fecha_registro}</td>
                        <td class="users__list-table-item">${user.ultima_conexion ?? 'No disponible'}</td>
                        <td class="users__list-table-item users__list-table-item--actions">
                            <div class="tooltip-container">
                                <button class="users__list-table-button list-table-button users_list-table-button--change-rol tooltip-container__button" data-id="${user.id}"><i class="fa-solid fa-arrow-right-arrow-left"></i></button>
                                <span class="tooltip-container__text">Cambiar rol</span>
                            </div>
                            <div class="tooltip-container">
                                <button class="users__list-table-button list-table-button users_list-table-button--${isActive ? 'desactivate' : 'activate'} users_list-table-button--change-status" data-id="${user.id}">${isActive? '<i class="fa-solid fa-user-slash"></i>' : '<i class="fa-solid fa-user-check"></i>'}</button>
                                <span class="tooltip-container__text">${isActive ? 'Desactivar usuario' : 'Activar usuario'}</span>
                            </div>
                        </td>
                    </tr>`
    }
    /**
     * Funcion que obtiene los valores del filtro, y dependiendo del filtro obtiene unos usuarios u otros,
     * En caso de que no exista filtro, muestro todos los usuarios seguidamente muestra estos torneos en #user-list
     */
    function loadUsersByStatus(page = 1) {
        //Siempre voy a querer mostrar 10 usuarios por página
        const itemsPerPage = 8
        //Obtención de los estados de cada filtro
        let alias = $('#user-alias-search').val()
        let role = $('#filter-role').val()
        let status = $('#filter-status').val()
        let registrationStart = $('#user-registration-start').val()
        let registrationEnd = $('#user-registration-end').val()
    
        let url = `admin/users/list/${page}/${itemsPerPage}`
        loadRenderedData('GET', url, {alias, role, status, registrationStart, registrationEnd}, (data) => {
            let users = data.users
            let rows = renderItems(users, generateUserRowTemplate)
            $('#user-list').empty().append(rows)
            // Genero la paginación
            renderPagination(data.total_pages, page, loadUsersByStatus, 'users');
        })
    }

    //Para no pasar nada a la función de primeras
    $('#sidebar-users').on('click', () => loadUsersByStatus());
    $('#user-filter-button').on('click', () => loadUsersByStatus());

    function renderOptionRolTemplate(rol) {
        return `<option value="${rol.id}" class="users__select-option">${rol.nombre}</option>`
    }


    /**
     * Función encargada de enviar los datos de un formulario para cambiar el rol de un usuario
     * @param {*} form 
     */
    function submitRolUserForm(form) {
        let formData = form.serialize() // Serializa el formulario (rol-select=2&user-id=123)
        loadRenderedData('POST', 'admin/users/change-rol', formData, (data) => {
            closeModal()
            //Una vez se cambie el rol, envío un mensaje de alerta con un modal
            showModal(data.title, data.message)
            
        })
    }

    /**
     * Muestra un modal dinámico para cambiar el rol de un usuario
     */
    function handleRolForm() {
        let userId = $(this).attr('data-id') //Id del usuario para tenerlo
        //Contrucción del formulario
        let rolForm = `<form action="" class="users__form" id="user-rol-form">
                            <div class="userss__field">
                                <label for="" class="users_field-label">Rol de usuario: </label>
                                <select name="rol-select" id="rol-select" class="users__select">`
        //Cargo los roles
        loadRenderedData('GET', 'admin/users/roles', {} ,(data)=>{
            let userRolTypes = data.user_rol_types //Los tipos de roles del sistema
            //Generación de los options
            let options = renderItems(userRolTypes, renderOptionRolTemplate)
            rolForm += options
            rolForm +=`         </select>
                            </div>
                            <input type="hidden" name="user-id" value="${userId}">
                        </form>`
            //Muestro un modal con los roles y mando una función que se ejecuta al completar el el formulario del modal
            showModal('Cambiar rol de usuario', rolForm,() => {
                submitRolUserForm($('#user-rol-form'))
            })
        })                         
    }
    $('#user-list').off('click', '.users_list-table-button--change-rol').on('click', '.users_list-table-button--change-rol', handleRolForm)

    function updateUserStatus() {
        let userId = $(this).attr('data-id'); // Obtiene el ID del usuario
        let userStatus = $('.users__list-status').attr('data-id');
        if (!userId || !userStatus) {
            showModal('Error', 'No se pudo obtener el ID o estado del usuario.')
        } else {
            loadRenderedData('GET', 'admin/users/change-status', {userId, userStatus}, (data) => {
                showModal(data.title, data.message)
            })
        }
    }
    $('#user-list').off('click', '.users_list-table-button--change-status').on('click', '.users_list-table-button--change-status', updateUserStatus)

});