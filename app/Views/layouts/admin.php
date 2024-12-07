<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url() ?>css/admin.css">
    <script src="https://kit.fontawesome.com/d10a6cd004.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="<?= base_url() ?>js/admin_page_utils.js" type="module"></script>
    <script src="<?= base_url() ?>js/modals.js" type="module"></script>
    <script src="<?= base_url() ?>js/tournaments.js" type="module"></script>
    <script src="<?= base_url() ?>js/users.js" type="module"></script>
    <!-- <script src="<?= base_url() ?>js/list_all_tournaments.js" type="module"></script> -->
    <link href='https://fonts.googleapis.com/css?family=Raleway:100,200,400,600' rel='stylesheet' type='text/css'>

    <title>Administracion | JLS</title>
</head>

<body>
    <div class="admin">
        <div class="sidebar">
            <div class="sidebar__logo">
                <img src="" alt="image_logo">
            </div>
            <div class="sidebar__content">
                <ul class="sidebar__list">
                    <li data-section="tournaments" class="sidebar__item" id="sidebar-tournaments">
                        <i class="fa-solid fa-trophy"></i>
                        <a class="sidebar__link" href="">Torneos</a>
                    </li>
                    <li data-section="users" class="sidebar__item" id="sidebar-users">
                        <i class="fa-solid fa-users"></i>
                        <a class="sidebar__link" href="">Usuarios</a>
                    </li>
                    <li data-section="events" class="sidebar__item" id="sidebar-events">
                        <i class="fa-solid fa-calendar-days"></i>
                        <a class="sidebar__link" href="">Eventos</a>
                    </li>
                    <li data-section="judges" class="sidebar__item" id="sidebar-judges">
                        <i class="fa-solid fa-gavel"></i>
                        <a class="sidebar__link" href="">Jueces</a>
                    </li>
                    <li data-section="settings" class="sidebar__item" id="sidebar-settings">
                        <i class="fa-solid fa-gear"></i>
                        <a class="sidebar__link" href="">Configuración</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="menu">
            <?php
            if (session()->getFlashdata('success')) {
            ?>
                <!-- Con esc() evito que se inyecte código ya que convierte cualquier caracter especial en su representación en html < = &lt; -->
                <input type="hidden" id="flash-data" name="success" value="<?= esc(session()->getFlashdata('success')) ?>">
            <?php
            } else if (session()->getFlashdata('error')) {
            ?>
                <input type="hidden" id="flash-data" name="error" value="<?= esc(session()->getFlashdata('error')) ?>">
            <?php
            }
            ?>
            <div class="modal" id="modal">
                <!-- /* From Uiverse.io by vinodjangid07 */ -->
                <div class="modal__container">
                    <!-- Contenido del modal -->
                    <div class="modal__content">
                        <p class="modal__title" id="modal-title"></p>
                        <div class="modal__body-content" id="modal-content"></div>
                    </div>
                    <!-- Botones de acción -->
                    <div class="modal__buttons">
                        <button class="modal__button modal__button--cancel" id="cancel-button">Cancel</button>
                        <button class="modal__button modal__button--confirm" id="confirm-button">Confirmar</button>
                    </div>
                    <!-- Botón para cerrar el modal -->
                    <button class="modal__close-button" id="close-button">
                        <i class="fa-solid fa-xmark"></i>
                        <!-- link: https://fontawesome.com/v6/icons/xmark?f=classic&s=solid -->
                    </button>
                </div>
                <!-- Licencia de Copyright -->
                <!--
                    Copyright - 2024 vinodjangid07 (Vinod Jangid)

                    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

                    The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
                -->
            </div>
            <div id="tournaments" class="tournaments menu__section menu__section--hidden">
                <h2 class="tournaments__title">Torneos</h2>
                <span class="tournament__description">En este apartado encontrarás todas la operaciones que puedes realizar con los torneos: visualizar el listado, crearlos, borrarlos y modificarlos.</span>
                <form action="admin/tournament/upload" method="post" class="tournaments__form" enctype="multipart/form-data">
                    <div class="tournaments__field">
                        <label for="tournament-name" class="tournaments__field-label">Nombre del torneo:</label>
                        <input type="text" name="name" id="tournament-name" class="tournaments__field-input">
                    </div>
                    <div class="tournaments__field">
                        <label for="tournament-start-date" class="tournaments__field-label">Fecha de inicio:</label>
                        <input type="date" name="start-date" id="tournament-start-date" class="tournaments__field-input">
                    </div>
                    <div class="tournaments__field">
                        <label for="tournament-end-date" class="tournaments__field-label">Fecha de finalización:</label>
                        <input type="date" name="end-date" id="tournament-end-date" class="tournaments__field-input">
                    </div>
                    <div class="tournaments__field">
                        <label for="tournament-logo" class="tournaments__field-label">Logotipo del torneo:</label>
                        <!-- <input type="text" name="logo" id="tournament-logo" class="form-tournament__input "> -->
                        <input type="file" name="logo" id="tournament-logo" class="tournaments__field-input tournaments__field-input--file" accept=".jpg" required>
                    </div>
                    <div class="tournaments__buttons">
                        <button type="submit" class="tournaments__button form-tournament__button--submit">Crear</button>
                        <button type="reset" class="tournaments__button form-tournament__button--reset">Borrar datos</button>
                    </div>

                </form>

                <!-- Listado de torneos existentes -->

                <div class="tournaments__list">
                    <h3 class="tournaments__list-title">Listado de torneos</h3>
                    <table class="tournaments__list-table table">
                        <thead class="tournaments__list-table-header">
                            <!-- color: #dd5; -->
                            <tr class="tournaments__list-table-row">
                                <th class="tournaments__list-table-header-item">Id</th>
                                <th class="tournaments__list-table-header-item">Nombre</th>
                                <th class="tournaments__list-table-header-item">Fecha de Inicio</th>
                                <th class="tournaments__list-table-header-item">Fecha de Finalización</th>
                                <th class="tournaments__list-table-header-item">Estado</th>
                                <th class="tournaments__list-table-header-item">Opciones</th>
                            </tr>
                        </thead>
                        <tbody class="tournaments__list-table-body" id="tournament-list">

                        </tbody>
                    </table>
                </div>

                <!-- Filtrado y búsqueda de torneos -->
                <div class="tournaments__filter">
                    <div class="tournaments__field">
                        <label class="tournaments__field-label" for="tournament-search">Nombre: </label>
                        <input type="text" class="tournaments__field-input" id="tournament-search" placeholder="Buscar torneo por nombre...">
                    </div>
                    <select class="tournaments__select" id="tournament-filter-status" name="filter-status">
                        <option class="tournaments__select-option tournaments__select-option--hidden" value="" hidden>Filtrar por estado</option>
                        <option class="tournaments__select-option" value="all" selected>Mostrar todos</option>
                        <option class="tournaments__select-option" value="ongoing">En curso</option>
                        <option class="tournaments__select-option" value="active">Activos</option>
                        <option class="tournaments__select-option" value="inactive">Inactivos</option>
                        <option class="tournaments__select-option" value="finished">Finalizados</option>
                    </select>
                    <button class="tournaments_button tournaments_button--filter" id="tournaments-filter-button">Filtrar</button>
                </div>


                <!-- 3. Gestión de participantes
                Agrega un subapartado donde se pueda:

                Ver los participantes registrados en cada torneo.
                Agregar o eliminar participantes.
                Exportar la lista de participantes en formatos como CSV o PDF -->

                <!-- <div class="tournaments-participants">
                    <h3>Gestión de Participantes</h3>
                    <button id="view-participants">Ver participantes</button>
                    <button id="add-participant">Añadir participante</button>
                    <button id="export-participants">Exportar lista</button>
                </div> -->

                <!-- 4. Estadísticas y reportes
                Proporciona un apartado para visualizar estadísticas de los torneos, como:

                Número total de torneos creados.
                Cantidad de participantes por torneo.
                Torneos más populares o con más inscripciones.
                5. Configuración avanzada
                Agrega configuraciones opcionales al crear o gestionar torneos, como:

                Capacidad máxima de participantes: Un límite que el sistema debe respetar.
                Tipos de torneos: Individual, equipos, eliminación directa, etc.
                Rondas: Define cuántas rondas habrá y los criterios de clasificación. -->


            </div>
            <div id="users" class="users menu__section menu__section--hidden">
                <h2 class="users__title">Usuarios</h2>
                <span class="users__description">En este apartado encontrarás todas las operaciones que puedes realizar con los usuarios: visualizar el listado, crearlos, desactivarlos y modificarlos.</span>
                <h3>Filtrar Usuarios</h3>

                <div class="users__filter">
                    <!-- Campo de búsqueda por alias -->
                    <div class="users__field">
                        <label class="users__field-label" for="user-alias-search">Alias:</label>
                        <input type="text" class="users__field-input" id="user-alias-search" name="alias" placeholder="Buscar usuario por alias...">
                    </div>

                    <!-- Selección por rol -->
                    <div class="users__field">
                        <label class="users__field-label" for="filter-role">Rol:</label>
                        <select class="users__select" id="user-filter-role" name="role">
                            <option class="users__select-option users__select-option--hidden" value="" hidden>Filtrar por rol</option>
                            <option class="users__select-option" value="all" selected>Mostrar todos</option>
                            <option class="users__select-option" value="admin">Administrador</option>
                            <option class="users__select-option" value="judge">Juez</option>
                            <option class="users__select-option" value="participant">Participante</option>
                        </select>
                    </div>

                    <!-- Selección por estado -->
                    <div class="users__field">
                        <label class="users__field-label" for="user-filter-status">Estado:</label>
                        <select class="users__select" id="filter-status" name="status">
                            <option class="users__select-option users__select-option--hidden" value="" hidden>Filtrar por estado</option>
                            <option class="users__select-option" value="all" selected>Mostrar todos</option>
                            <option class="users__select-option" value="active">Activo</option>
                            <option class="users__select-option" value="inactive">Inactivo</option>
                        </select>
                    </div>

                    <!-- Fechas de registro -->
                    <div class="users__field">
                        <label class="users__field-label" for="registration-start">Registro desde:</label>
                        <input type="date" class="users__field-input" id="user-registration-start" name="registration_start">
                    </div>
                    <div class="users__field">
                        <label class="users__field-label" for="registration-end">Registro hasta:</label>
                        <input type="date" class="users__field-input" id="user-registration-end" name="registration_end">
                    </div>

                    <!-- Botón de filtrado -->
                    <button class="users_button users_button--filter" id="user-filter-button" type="submit">Filtrar</button>
                </div>


                <div class="users__list">
                    <h3 class="users__list-title">Listado de usuarios</h3>
                    <table class="users__list-table table">
                        <thead class="users__list-table-header">
                            <tr class="users__list-table-row">
                                <th class="users__list-table-header-item">Id</th>
                                <th class="users__list-table-header-item">Alias</th>
                                <th class="users__list-table-header-item">Rol</th>
                                <th class="users__list-table-header-item">Estado</th>
                                <th class="users__list-table-header-item">Fecha de Registro</th>
                                <th class="users__list-table-header-item">Última Conexión</th>
                                <th class="users__list-table-header-item">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="users__list-table-body" id="user-list">
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="events" class="menu__section menu__section--hidden">Contenido de Eventos</div>
            <div id="judges" class="menu__section menu__section--hidden">Contenido de Jueces</div>
            <div id="settings" class="menu__section menu__section--hidden">Contenido de Configuración</div>
        </div>
    </div>

    <form action="" class="users__form">
        <div class="userss__field">
            <label for="" class="users_field-label"></label>
            <select name="" id="" class="users__select">
                <option value="" class="users__select-option"></option>
            </select>
        </div>
    </form>

</body>

</html>