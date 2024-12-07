import { showModal, closeModal } from './modals.js'
import { renderItems, loadRenderedData } from "./admin_page_utils.js"

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
                            <span class="users__list-status users__list-status--${isActive ? 'active' : 'inactive'}" data-id="${user.activo}">${isActive ? 'ACTIVO' : 'INACTIVO'}</span>
                        </td>
                        <td class="users__list-table-item">${user.fecha_registro}</td>
                        <td class="users__list-table-item">${user.ultima_conexion}</td>
                        <td class="users__list-table-item users__list-table-item--actions">
                            <button class="users__list-table-button users_list-table-button--change-rol" id="user-change-rol" data-id="${user.id}">Cambiar Rol</button>
                            <button class="users__list-table-button users_list-table-button--${isActive ? 'desactivate' : 'activate'}" id="user-change-status" data-id="${user.id}">${isActive? 'DESACTIVAR' : 'ACTIVAR'}</button>
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
        loadRenderedData('GET', 'admin/users/list', {alias, role, status, registrationStart, registrationEnd}, (data) => {
            let users = data.users
            let rows = renderItems(users, generateUserRowTemplate)
            $('#user-list').empty().append(rows)
        })
    }

    $('#sidebar-users').on('click', loadUsersByStatus)
    $('#user-filter-button').on('click', loadUsersByStatus)

    //

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
        let userId = $('#user-change-rol').attr('data-id') //Id del usuario para tenerlo
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
    $('#user-list').off('click', '#user-change-rol').on('click', '#user-change-rol', handleRolForm)

    function updateUserRol() {
        let userId = $('#user-change-status').attr('data-id'); // Obtiene el ID del usuario
        let userStatus = $('.users__list-status').attr('data-id');
        if (!userId || !userStatus) {
            showModal('Error', 'No se pudo obtener el ID o estado del usuario.')
        } else {
            loadRenderedData('GET', 'admin/users/change-status', {userId, userStatus}, (data) => {
                showModal(data.title, data.message)
            })
        }
    }
    $('#user-list').off('click', '#user-change-status').on('click', '#user-change-status', updateUserRol)

});