import { showModal, closeModal } from './modals.js'
import { renderItems, loadRenderedData } from "./admin_page_utils.js"

 $(document).ready(function () {
    /**
     * Genera un template de criterio
     * @param {*} criteria 
     * @returns 
     */
    function generateCriteriaTemplate(criteria) {
        return `<li class="settings__list-item settings__criteria-item">
                    <span class="settings__criteria-name">${criteria.nombre}</span>
                    <div class="settings__actions settings-criteria-buttons">
                        <label class="switch">
                            <input type="checkbox" ${criteria.activo ? 'checked' : 'unchecked'}>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </li>`
    }

    // <button class="settings__button settings__criteria-edit">Editar</button>
    // <button class="settings__button settings__criteria-delete">Eliminar</button>

    /**
     * Muestra en la página los criterios
     */
    function showCriteria() {
        loadRenderedData('GET', 'admin/tournament/scoring-criteria', {}, (data)=> {
            let criteria = data.scoring_criteria
            let rows = renderItems(data.scoring_criteria, generateCriteriaTemplate)
            $('.settings__criteria-list').empty().append(rows)
        })
    }
    $('#sidebar-settings').on('click', showCriteria)

    /**
     * Genera un template de un tipo de ronda
     * @param {*} roundType 
     * @returns 
     */
    function generateRoundTypeTemplate(roundType) {
        return `<li class="settings__list-item settings__rounds-item">
                    <span class="settings__rounds-name">${roundType.nombre}</span>
                    <div class="settings__actions settings__rounds_buttons">
                        <label class="switch">
                            <input type="checkbox" ${roundType.activo ? 'checked' : 'unchecked'}>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </li>`
    }

    // <button class="settings__button settings__rounds-edit">Editar</button>
    // <button class="settings__button settings__rounds-delete">Eliminar</button>

    /**
     * Muestra por pantalla los tipos de ronda del sistema
     */
    function showRoundTypes() {
        loadRenderedData('GET', 'admin/settings/round-types', {}, (data)=>{
            let roundTypes = data.round_types
            let rows = renderItems(roundTypes, generateRoundTypeTemplate)
            $('.settings__rounds-list').empty().append(rows)
        })
    }
    $('#sidebar-settings').on('click', showRoundTypes)

    /**
     * Genera un template de rol
     * @param {*} rol 
     * @returns 
     */
    function generateRolTemplate(rol) {
        return `<li class="settings__list-item settings__roles-item">
                    <span class="settings__roles-name">${rol.nombre}</span>
                    <div class="settings__actions settings__roles_buttons">
                        <label class="switch">
                            <input type="checkbox" ${rol.activo ? 'checked' : 'unchecked'}>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </li>`
    }

    // <button class="settings__button settings__roles-edit">Editar</button>
    // <button class="settings__button settings__roles-delete">Eliminar</button>

    /**
     * Muestra por pantalla los tipos de roles del sistema
     */
    function showRoles() {
        loadRenderedData('GET', 'admin/users/roles', {}, (data)=>{
            let roles = data.user_rol_types
            let rows = renderItems(roles, generateRolTemplate)
            $('.settings__roles-list').empty().append(rows)
        })
    }
    $('#sidebar-settings').on('click', showRoles)

 });