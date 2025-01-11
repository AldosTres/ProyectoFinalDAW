<!-- Insercion del <head> -->
<?php include_once TEMPLATES_VIEWS_PATH . 'html_header.php' ?>

<body>
    <!-- Insercion del navbar -->
    <?php include_once TEMPLATES_VIEWS_PATH . 'header.php' ?>

    <div class="container profile-page">
        <!-- Header del perfil -->
        <div class="row profile-page__header align-items-center">
            <div class="col-md-3 profile-page__avatar text-center">
                <!-- Foto de perfil -->
                <div class="profile-page__avatar-container">
                    <img src="<?= base_url() ?>img/perfil_usuarios/<?= $profile_picture ?>" alt="Foto de perfil" id="profile-picture" class="profile-page__avatar-img rounded-circle  img-fluid mb-3">
                </div>
                <!-- https://pixabay.com/es/vectors/avatar-icono-marcador-de-posici%C3%B3n-3814049/ -->
                <form action="<?= base_url() ?>profile/upload/profile-picture/<?= session()->get('user_id') ?>" method="POST" enctype="multipart/form-data" class="profile-page__avatar-form">
                    <input type="file" name="profile-picture" accept="image/*" class="profile-page__avatar-input form-control mb-2" required>
                    <button type="submit" class="profile-page__avatar-button btn btn-primary btn-sm">Subir nueva foto</button>
                </form>
                <?php
                if (session()->getFlashdata('profile_picture_error')) {
                ?>
                    <div class="alert-message">
                        <?= session()->getFlashdata('profile_picture_error') ?>
                    </div>
                <?php
                }
                ?>
            </div>
            <div class="col-md-9 profile-page__info">
                <h1 class="profile-page__title mb-3">Hola, <span id="username"><?= session()->get('jumper_user_name') ?></span>!</h1>
                <p class="profile-page__member-info text-muted">Miembro desde: <span id="member-since"><?= $registration_date ?></span></p>
            </div>
        </div>

        <hr>

        <!-- Sección de información del perfil -->
        <div class="row profile-page__section">
            <?php if (session()->has('error')) { ?>
                <div class="alert alert-danger">
                    <?= session('error') ?>
                </div>
            <?php
            }
            if (session()->has('success')) { ?>
                <div class="alert alert-success">
                    <?= session('success') ?>
                </div>
            <?php
            }
            ?>
            <div class="col-md-6 profile-page__personal-info">
                <h3 class="profile-page__section-title">Información Personal</h3>
                <form action="<?= base_url() ?>profile/update" method="POST" class="profile-page__form">
                    <div class="profile-page__form-group mb-3">
                        <label for="username" class="profile-page__form-label">Nombre de Usuario:</label>
                        <input type="text" id="new_username" name="new_username" value="<?= $user_name ?>" class="profile-page__form-input form-control">
                    </div>
                    <div class="profile-page__form-group mb-3">
                        <label for="alias" class="profile-page__form-label">alias</label>
                        <input type="text" id="new_alias" name="new_alias" value="<?= $user_alias ?>" class="profile-page__form-input form-control">
                    </div>
                    <div class="profile-page__form-group mb-3">
                        <label for="password" class="profile-page__form-label">Nueva Contraseña:</label>
                        <input type="password" id="new-password" name="new-password" class="profile-page__form-input form-control">
                    </div>
                    <button type="submit" class="profile-page__form-submit btn btn-success">Actualizar Información</button>
                </form>
            </div>

            <div class="col-md-6 profile-page__stats">
                <h3 class="profile-page__section-title">Estadísticas</h3>
                <ul class="profile-page__stats-list list-group">
                    <li class="profile-page__stats-item list-group-item">Torneos en los que ha participado: <span id="tournaments-count">[Número]</span></li>
                    <li class="profile-page__stats-item list-group-item">Videos subidos: <span id="videos-count">[Número]</span></li>
                    <li class="profile-page__stats-item list-group-item">Clasificación promedio: <span id="average-ranking">[Clasificación]</span></li>
                </ul>
            </div>
        </div>

        <hr>

        <!-- Sección de videos -->
        <div class="row profile-page__section">
            <div class="col-md-12 profile-page__videos">
                <h3 class="profile-page__section-title">Mis Videos</h3>
                <div class="row profile-page__videos-list" id="videos-list">
                    <?php
                    foreach ($sin_subir as $key => $value) {
                    ?>
                        <div class="col-md-4 profile-page__video-item">

                            <div class="card profile-page__video-card">

                                <div class="card-body profile-page__video-card-body">
                                    <h5 class="profile-page__video-title card-title"><?= $value['torneo_nombre'] ?></h5>
                                    <p class="profile-page__video-date card-text">Ronda:<?= $value['ronda_nombre'] ?></p>
                                    <form action="<?= base_url() ?>uploadVideo" method="POST">
                                        <input type="hidden" name="round_id" value="<?= $value['ronda_id'] ?>">
                                        <input type="hidden" name="participant_role" value="<?= $value['participant_role'] ?>">
                                        <input type="hidden" name="tournament_id" value="<?= $value['id_torneo'] ?>">
                                        <input type="hidden" name="user_id" value="<?= session()->get('user_id') ?>">
                                        <label for="video_url">Enlace del Video (YouTube):</label>
                                        <input type="text" name="video_url" id="video_url" placeholder="https://youtube.com/..." required>
                                        <button type="submit" class="btn btn-primary profile-page__form-submit">Subir Video</button>
                                    </form>

                                </div>
                            </div>
                        </div>

                    <?php
                    }
                    foreach ($subidos as $key => $value) {
                    ?>
                        <div class="col-md-4 profile-page__video-item">
                            <div class="card profile-page__video-card">
                                <div class="profile-page__video-preview card-img-top">
                                    <?= $value['user_video'] ?? 'Video no disponible' ?>
                                </div>
                                <div class="card-body profile-page__video-card-body">
                                    <h5 class="profile-page__video-title card-title"><?= $value['torneo_nombre'] ?></h5>
                                    <p class="profile-page__video-date card-text"><?= $value['ronda_nombre'] ?></p>
                                    <a href="[Ruta para eliminar]" class="profile-page__video-delete btn btn-danger btn-sm">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <hr>

        <!-- Sección de torneos -->
        <div class="row profile-page__section">
            <div class="col-md-12 profile-page__tournaments">
                <h3 class="profile-page__section-title">Mis Torneos</h3>
                <div id="tournaments-list" class="profile-page__tournaments-list accordion">
                    <!-- Ejemplo de un torneo -->
                    <div class="accordion-item profile-page__tournament-item">
                        <h2 class="accordion-header profile-page__tournament-header">
                            <button class="accordion-button profile-page__tournament-button" type="button" data-bs-toggle="collapse" data-bs-target="#tournament-1">
                                [Nombre del Torneo]
                            </button>
                        </h2>
                        <div id="tournament-1" class="accordion-collapse collapse profile-page__tournament-body">
                            <div class="accordion-body">
                                <p><strong>Fecha:</strong> [Fecha del torneo]</p>
                                <p><strong>Clasificación:</strong> [Clasificación]</p>
                                <a href="[Ruta de detalles]" class="btn btn-primary btn-sm">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                    <!-- Fin del ejemplo -->
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        const BASE_URL = "<?= base_url() ?>"; //Constante para poder usar desde el js la base_url()
    </script>
    <!-- Insercion del footer -->
    <?php include_once TEMPLATES_VIEWS_PATH . 'footer.php'; ?>
</body>

</html>