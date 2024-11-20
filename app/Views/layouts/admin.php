<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url() ?>css/admin.css">
    <script src="https://kit.fontawesome.com/d10a6cd004.js" crossorigin="anonymous"></script>
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
                    <li data-section="tournaments" class="sidebar__item">
                        <i class="fa-solid fa-trophy"></i>
                        <a class="sidebar__link" href="">Torneos</a>
                    </li>
                    <li data-section="users" class="sidebar__item">
                        <i class="fa-solid fa-users"></i>
                        <a class="sidebar__link" href="">Usuarios</a>
                    </li>
                    <li data-section="events" class="sidebar__item">
                        <i class="fa-solid fa-calendar-days"></i>
                        <a class="sidebar__link" href="">Eventos</a>
                    </li>
                    <li data-section="judges" class="sidebar__item">
                        <i class="fa-solid fa-gavel"></i>
                        <a class="sidebar__link" href="">Jueces</a>
                    </li>
                    <li data-section="settings" class="sidebar__item">
                        <i class="fa-solid fa-gear"></i>
                        <a class="sidebar__link" href="">Configuración</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="menu">
            <div id="tournaments" class="menu__section menu__section--hidden">Contenido de Torneos</div>
            <div id="users" class="menu__section menu__section--hidden">Contenido de Usuarios</div>
            <div id="events" class="menu__section menu__section--hidden">Contenido de Eventos</div>
            <div id="judges" class="menu__section menu__section--hidden">Contenido de Jueces</div>
            <div id="setttings" class="menu__section menu__section--hidden">Contenido de Configuración</div>
        </div>
    </div>
    <script src="<?= base_url() ?>js/admin_page_controller.js"></script>
</body>

</html>