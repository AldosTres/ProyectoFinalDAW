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
    <script src="<?= base_url() ?>js/events.js" type="module"></script>
    <script src="<?= base_url() ?>js/settings.js" type="module"></script>
    <link href='https://fonts.googleapis.com/css?family=Raleway:100,200,400,600' rel='stylesheet' type='text/css'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="icon" href="<?= base_url() ?>img/logoTipo_JLS.png">
    <title>Administracion | JLS</title>
</head>

<body>
    <div class="admin">
        <div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark sidebar" style="width: 280px;">
            <a href="admin" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none sidebar__logo">
                <img src="<?= base_url() ?>img/logoTipo_JLS.png" alt="" width="40">
                <span class="fs-4">JLS</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto sidebar__content">
                <li class="nav-item sidebar__item" data-section="home" id="sidebar-home">
                    <a href="#" id="link-home" class="nav-link text-white active">
                        <i class="fa-solid fa-house"></i>
                        Inicio
                    </a>
                </li>
                <li class=" nav-item sidebar__item" data-section="tournaments" id="sidebar-tournaments">
                    <a href="#" id="link-tournaments" class="nav-link text-white">
                        <i class="fa-solid fa-trophy"></i>
                        Torneos
                    </a>
                </li>
                <?php
                if (session()->get('rol_name') == 'admin') {
                ?>
                    <li class="sidebar__item" data-section="users" id="sidebar-users">
                        <a href="#" id="link-users" class="nav-link text-white">
                            <i class="fa-solid fa-user-group"></i>
                            Usuarios
                        </a>
                    </li>
                    <li class="sidebar__item" data-section="events" id="sidebar-events">
                        <a href="#" id="link-events" class="nav-link text-white">
                            <i class="fa-solid fa-calendar-days"></i>
                            Eventos
                        </a>
                    </li>
                    <li class="sidebar__item" data-section="settings" id="sidebar-settings">
                        <a href="#" id="link-settings" class="nav-link text-white">
                            <i class="fa-solid fa-gear"></i>
                            Configuración
                        </a>
                    </li>
                <?php
                }
                ?>
            </ul>
            <hr>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= base_url() ?>img/perfil_usuarios/<?= session()->get('admin_image') ?>" alt="" width="32" height="32" class="rounded-circle me-2">
                    <strong><?= session()->get('admin_name') ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                    <li><a class="dropdown-item" href="<?= base_url() ?>logout-admin">Cerrar sesión</a></li>
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
                        <button class="modal__button modal__button--cancel" id="cancel-button">Cancelar</button>
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
            <div id="home" class="home menu__section">
                <h1 class="home__title">Bienvenido</h1>
                <p class="home__description">Gestiona fácilmente tus torneos de baile, usuarios y configuraciones desde esta plataforma.</p>

                <div class="home__stats">
                    <div class="home__stat">
                        <span class="home__stat-number"><?= $total_active_tournaments ?></span>
                        <span class="home__stat-label">Torneos activos</span>
                    </div>
                    <div class="home__stat">
                        <span class="home__stat-number"><?= $total_active_judges ?></span>
                        <span class="home__stat-label">Jueces registrados</span>
                    </div>
                    <div class="home__stat">
                        <span class="home__stat-number"><?= $total_active_participants ?></span>
                        <span class="home__stat-label">Participantes totales</span>
                    </div>
                </div>

                <div class="home__actions">
                    <a href="/torneos" class="home__button">Gestionar Torneos</a>
                    <a href="/jueces" class="home__button">Gestionar Usuarios</a>
                    <a href="/configuracion" class="home__button">Configuración</a>
                </div>

                <div class="home__tasks">
                    <h2 class="home__tasks-title">Tareas Recientes</h2>
                    <ul class="home__tasks-list">
                        <li class="home__tasks-item">Torneo "Summer Dance Battle" creado.</li>
                        <li class="home__tasks-item">Criterio de calificación "Originalidad" añadido.</li>
                        <li class="home__tasks-item">Juez "Carlos Pérez" registrado.</li>
                    </ul>
                </div>
            </div>

            <div id="tournaments" class="tournaments menu__section menu__section--hidden">
                <h2 class="tournaments__title menu__section-title">Torneos</h2>
                <span class="tournament__description menu__section-description">En este apartado encontrarás todas la operaciones que puedes realizar con los torneos: visualizar el listado, crearlos, borrarlos y modificarlos.</span>
                <!-- cambiar titulo -->
                <h5 class="tournaments__filter_title menu__section-subtitle">Creación de torneo</h5>
                <form action="admin/tournament/upload" method="post" class="tournaments__form form" enctype="multipart/form-data">
                    <div class="tournaments__field form__field">
                        <label for="tournament-name" class="tournaments__field-label form__field-label">Nombre del torneo:</label>
                        <input type="text" name="name" id="tournament-name" class="tournaments__field-input form__field-input" placeholder="Buscar torneo por nombre">
                    </div>
                    <div class="tournaments__field form__field">
                        <label for="tournament-start-date" class="tournaments__field-label form__field-label">Fecha de inicio:</label>
                        <input type="date" name="start-date" id="tournament-start-date" class="tournaments__field-input form__field-input">
                    </div>
                    <div class="tournaments__field form__field">
                        <label for="tournament-end-date" class="tournaments__field-label form__field-label">Fecha de finalización:</label>
                        <input type="date" name="end-date" id="tournament-end-date" class="tournaments__field-input form__field-input">
                    </div>
                    <div class="tournaments__field form__field">
                        <label for="tournament-logo" class="tournaments__field-label form__field-label">Logotipo del torneo:</label>
                        <input type="file" name="logo" id="tournament-logo" class="tournaments__field-input tournaments__field-input--file form__field-input form-control " accept=".jpg" required>
                    </div>
                    <div class="tournaments__buttons form__buttons">
                        <button type="submit" class="tournaments__button form-tournament__button--submit form__button">Crear</button>
                        <button type="reset" class="tournaments__button form-tournament__button--reset form__button form__button--reset">Borrar datos</button>
                    </div>
                </form>


                <!-- Listado de torneos existentes -->

                <div class="tournaments__list">
                    <h5 class="tournaments__list-title menu__section-subtitle">Listado de torneos</h5>
                    <table class="tournaments__list-table table table-hover table-striped">
                        <thead class="tournaments__list-table-header table-header">
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
                    <div id="pagination-container" class="pagination pagination-container-tournaments"></div>
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
                <!-- 5. Configuración avanzada
                Agrega configuraciones opcionales al crear o gestionar torneos, como:

                Capacidad máxima de participantes: Un límite que el sistema debe respetar.
                Rondas: Define cuántas rondas habrá y los criterios de clasificación. -->
            </div>
            <div id="tournament-bracket" class="tournament-bracket menu__section menu__section--hidden">

            </div>
            <?php
            if (session()->get('rol_name') == 'admin') {
            ?>
                <div id="users" class="users menu__section menu__section--hidden">
                    <h2 class="users__title menu__section-title">Usuarios</h2>
                    <span class="users__description menu__section-description">En este apartado encontrarás todas las operaciones que puedes realizar con los usuarios: visualizar el listado, crearlos, desactivarlos y modificarlos.</span>
                    <h5 class="users__filter-title menu__section-subtitle">Filtrar Usuarios</h5>
                    <div class="users__filter form">
                        <!-- Campo de búsqueda por alias -->
                        <div class="users__field form__field">
                            <label class="users__field-label form__field-label" for="user-alias-search">Alias:</label>
                            <input type="text" class="users__field-input form__field-input" id="user-alias-search" name="alias" placeholder="Buscar usuario por alias...">
                        </div>

                        <!-- Selección por rol -->
                        <!-- revisar roles -->
                        <div class="users__field form__field">
                            <label class="users__field-label form__field-label" for="filter-role">Rol:</label>
                            <select class="users__select form__field-select" id="user-filter-role" name="role">
                                <option class="users__select-option users__select-option--hidden form__select-option--hidden" value="" hidden>Filtrar por rol</option>
                                <option class="users__select-option form__select-option" value="all" selected>Mostrar todos</option>
                                <option class="users__select-option form__select-option" value="admin">Administrador</option>
                                <option class="users__select-option form__select-option" value="judge">Juez</option>
                                <option class="users__select-option form__select-option" value="participant">Participante</option>
                            </select>
                        </div>

                        <!-- Selección por estado -->
                        <div class="users__field form__field">
                            <label class="users__field-label form__field-label" for="user-filter-status">Estado:</label>
                            <select class="users__select form__field-select" id="filter-status" name="status">
                                <option class="users__select-option users__select-option--hidden form__select-option--hidden" value="" hidden>Filtrar por estado</option>
                                <option class="users__select-option form__select-option" value="all" selected>Mostrar todos</option>
                                <option class="users__select-option form__select-option" value="active">Activo</option>
                                <option class="users__select-option form__select-option" value="inactive">Inactivo</option>
                            </select>
                        </div>

                        <!-- Fechas de registro -->
                        <div class="users__field form__field">
                            <label class="users__field-label form__field-label" for="registration-start">Registro desde:</label>
                            <input type="date" class="users__field-input form__field-input" id="user-registration-start" name="registration_start">
                        </div>
                        <div class="users__field form__field">
                            <label class="users__field-label form__field-label" for="registration-end">Registro hasta:</label>
                            <input type="date" class="users__field-input form__field-input" id="user-registration-end" name="registration_end">
                        </div>

                        <!-- Botón de filtrado -->
                        <button class="users_button users_button--filter form__button" id="user-filter-button" type="submit">Filtrar</button>
                    </div>



                    <div class="users__list">
                        <h5 class="users__list-title menu__section-subtitle">Listado de usuarios</h5>
                        <table class="users__list-table table table-hover table-striped">
                            <thead class="users__list-table-header table-header">
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
                        <div id="pagination-container" class="pagination pagination-container-users"></div>
                    </div>
                </div>
                <div id="events" class="events menu__section menu__section--hidden">
                    <h2 class="events__title menu__section-title">Eventos</h2>
                    <span class="events__description menu__section-description">En este apartado encontrarás todas las operaciones que puedes realizar con los eventos: visualizar el listado, crearlos, desactivarlos y modificarlos.</span>
                    <h5 class="menu__section-subtitle">Creación de evento</h5>
                    <form action="admin/events/upload" method="post" class="events__form form" enctype="multipart/form-data">
                        <div class="events__field form__field">
                            <label for="event-name" class="events__field-label form__field-label">Nombre del evento:</label>
                            <input type="text" name="event-name" id="event-name" class="events__field-input form__field-input form-control" placeholder="Introduce el nombre del evento">
                        </div>
                        <div class="events__field form__field">
                            <label for="event-description" class="events__field-label form__field-label">Descripción:</label>
                            <textarea name="event-description" id="event-description" class="events__field-input form__field-input form-control " placeholder="Añade una breve descripción del evento"></textarea>
                        </div>
                        <div class="events__field form__field">
                            <label for="event-start-date" class="events__field-label form__field-label">Fecha de inicio:</label>
                            <input type="date" name="event-start-date" id="event-start-date" class="events__field-input form__field-input form-control">
                        </div>
                        <div class="events__field form__field">
                            <label for="event-end-date" class="events__field-label form__field-label">Fecha de finalización:</label>
                            <input type="date" name="event-end-date" id="event-end-date" class="events__field-input form__field-input form-control">
                        </div>
                        <div class="events__field form__field">
                            <label for="event-location" class="events__field-label form__field-label">Ubicación:</label>
                            <input type="text" name="event-location" id="event-location" class="events__field-input form__field-input" placeholder="Ubicación del evento">
                        </div>
                        <div class="events__field form__field">
                            <label for="event-logo" class="events__field-label form__field-label">Logotipo del evento:</label>
                            <input type="file" name="event-image" id="event-image" class="events__field-input events__field-input--file form__field-input form-control" accept=".jpg" required>
                        </div>
                        <div class="events__buttons form__buttons">
                            <button type="submit" class="events__button form-event__button--submit form__button">Crear</button>
                            <button type="reset" class="events__button form-event__button--reset form__button form__button--reset">Borrar datos</button>
                        </div>
                    </form>
                    <div class="events__filter form">
                        <!-- Campo de búsqueda por nombre de evento -->
                        <div class="events__field form__field">
                            <label class="events__field-label form__field-label" for="event-name-search">Nombre del evento:</label>
                            <input type="text" class="events__field-input form__field-input" id="event-name-search" name="nombre" placeholder="Buscar evento por nombre...">
                        </div>

                        <!-- Selección por estado -->
                        <div class="events__field form__field">
                            <label class="events__field-label form__field-label" for="filter-status">Estado:</label>
                            <select class="events__select form__field-select" id="event-filter-status" name="estado">
                                <option class="events__select-option events__select-option--hidden form__select-option--hidden" value="" hidden>Filtrar por estado</option>
                                <option class="events__select-option form__select-option" value="" selected>Mostrar todos</option>
                                <option class="events__select-option form__select-option" value="Próximo">Próximo</option>
                                <option class="events__select-option form__select-option" value="En curso">En curso</option>
                                <option class="events__select-option form__select-option" value="Finalizado">Finalizado</option>
                                <option class="events__select-option form__select-option" value="Cancelado">Cancelado</option>
                            </select>
                        </div>

                        <!-- Fecha de inicio -->
                        <div class="events__field form__field">
                            <label class="events__field-label form__field-label" for="event-start-date">Fecha de inicio desde:</label>
                            <input type="date" class="events__field-input form__field-input" id="event-filter-start-date" name="fecha_inicio_start">
                        </div>

                        <!-- Fecha de fin -->
                        <div class="events__field form__field">
                            <label class="events__field-label form__field-label" for="event-end-date">Fecha de fin hasta:</label>
                            <input type="date" class="events__field-input form__field-input" id="event-filter-end-date" name="fecha_fin_end">
                        </div>
                        <!-- Activo -->
                        <div class="events__field form__field">
                            <label class="events__field-label form__field-label" for="event-active">Activo</label>
                            <input type="checkbox" name="event-filter-active" id="event-filter-active" class="events__field-input form__field-input" checked>
                        </div>

                        <!-- <label class="switch">
                        Activo
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label> -->
                        <!-- Botón de filtrado -->
                        <button class="events_button events_button--filter form__button" id="event-filter-button" type="submit">Filtrar</button>
                    </div>

                    <h5 class="events__list-title menu__section-subtitle">Listado de usuarios</h5>
                    <div class="events__list">
                        <table class="events__list-table table table-hover table-striped">
                            <thead class="events__list-table-header table-header">
                                <tr class="events__list-table-row">
                                    <th class="events__list-table-header-item">Id</th>
                                    <th class="events__list-table-header-item">Nombre</th>
                                    <th class="events__list-table-header-item">Descripción</th>
                                    <th class="events__list-table-header-item">Fecha Inicio</th>
                                    <th class="events__list-table-header-item">Estado</th>
                                    <th class="events__list-table-header-item">Activo</th>
                                    <th class="events__list-table-header-item">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="events__list-table-body" id="events-list">
                            </tbody>
                        </table>
                        <div id="pagination-container" class="pagination pagination-container-events"></div>
                    </div>
                </div>
                <div id="settings" class="settings menu__section menu__section--hidden">
                    <h2 class="settings__title menu__section-title">Configuración</h2>
                    <span class="settings__description menu__section-description">
                        En este apartado encontrarás todas las configuraciones del sistema: criterios de calificación, tipos de rondas, roles y más.
                    </span>

                    <!-- Criterios de Calificación -->
                    <div class="settings__section settings__criteria">
                        <h5 class="settings__section-title settings__criteria-title">Criterios de Calificación</h5>
                        <p class="settings__section-description settings__criteria-description">
                            Visualiza, agrega o edita los criterios usados para calificar.
                        </p>
                        <button class="settings__button settings__criteria-add">Añadir Criterio</button>
                        <ul class="settings__list settings__criteria-list">
                            <!-- Aquí van los distintos criterios -->
                        </ul>
                    </div>

                    <!-- Tipos de Rondas -->
                    <div class="settings__section settings__rounds">
                        <h5 class="settings__section-title settings__rounds-title">Tipos de Rondas</h5>
                        <p class="settings__section-description settings__rounds-description">
                            Administra las diferentes rondas utilizadas en los torneos.
                        </p>
                        <button class="settings__button settings__rounds-add">Añadir Tipo de Ronda</button>
                        <ul class="settings__list settings__rounds-list">
                            <!-- Aquí van los distintos tipos de rondas -->
                        </ul>
                    </div>

                    <!-- Roles -->
                    <div class="settings__section settings__roles">
                        <h5 class="settings__section-title settings__roles-title">Roles</h5>
                        <p class="settings__section-description settings__roles-description">
                            Visualiza y gestiona los roles de los usuarios.
                        </p>
                        <button class="settings__button settings__roles-add">Añadir Rol</button>
                        <ul class="settings__list settings__roles-list">
                            <!-- Aquí van los distintos tipos de roles -->
                        </ul>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
    <script>
        const BASE_URL = "<?= base_url() ?>"; //Constante para poder usar desde el js la base_url()
    </script>

</body>

</html>