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
            <div id="simpleModal" class="modal">
                <div class="modal-content">
                    <div id="modalMessage" class="modalMessage">
                        El contenido se ha subido correctamente Lorem ipsum dolor, sit amet consectetur adipisicing elit. Laudantium ipsa, hic minus velit ex tempora quos expedita voluptas dignissimos excepturi sequi sint ad praesentium eligendi nostrum similique exercitationem error aliquam.

                    </div>
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                </div>
            </div>
            <div id="tournaments" class="menu__section menu__section--hidden">
                <form action="upload_tournament" method="post" class="form-tournament" enctype="multipart/form-data">
                    <h2 class="form-tournament__title">Iniciar nuevo torneo</h2>
                    <div class="form-tournament__field">
                        <label for="tournament-name" class="form-tournament__label">Nombre del torneo:</label>
                        <input type="text" name="name" id="tournament-name" class="form-tournament__input">
                    </div>
                    <div class="form-tournament__field">
                        <label for="tournament-start-date" class="form-tournament__label">Fecha de inicio:</label>
                        <input type="date" name="start-date" id="tournament-start-date" class="form-tournament__input">
                    </div>
                    <div class="form-tournament__field">
                        <label for="tournament-end-date" class="form-tournament__label">Fecha de finalización:</label>
                        <input type="date" name="end-date" id="tournament-end-date" class="form-tournament__input">
                    </div>
                    <div class="form-tournament__field">
                        <label for="tournament-logo" class="form-tournament__label">Logotipo del torneo:</label>
                        <!-- <input type="text" name="logo" id="tournament-logo" class="form-tournament__input "> -->
                        <input type="file" name="logo" id="tournament-logo" class="form-tournament__input form-tournament__input--file" accept="image/*" required>
                    </div>
                    <div class="form-tournament__buttons">
                        <button type="submit" class="form-tournament__button form-tournament__button--submit">Crear</button>
                        <button type="reset" class="form-tournament__button form-tournament__button--reset">Borrar datos</button>
                    </div>
                </form>
            </div>
            <div id="users" class="menu__section menu__section--hidden">Contenido de Usuarios</div>
            <div id="events" class="menu__section menu__section--hidden">Contenido de Eventos</div>
            <div id="judges" class="menu__section menu__section--hidden">Contenido de Jueces</div>
            <div id="settings" class="menu__section menu__section--hidden">Contenido de Configuración</div>
        </div>
    </div>
    <script src="<?= base_url() ?>js/admin_page_controller.js"></script>
</body>

</html>