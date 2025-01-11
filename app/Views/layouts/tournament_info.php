<?php include_once TEMPLATES_VIEWS_PATH . 'html_header.php' ?>

<body>
    <!-- Navbar -->
    <?php include_once TEMPLATES_VIEWS_PATH . 'header.php' ?>

    <!-- Sidebar -->
    <?php include_once TEMPLATES_VIEWS_PATH . 'sidebar.php' ?>

    <div class="tournament-page">
        <div class="tournament-page__tournament-image col-12 d-flex justify-content-center">
            <img src="<?= base_url() ?>img/logos_torneos/<?= $tournament->logo_path ?>.jpg" alt="" class="img-fluid">
        </div>
        <div class="tournament-page__presentation">
            <h1 class="tournament-page__title"><?= $title ?></h1>
        </div>

        <!-- Contenido principal -->
        <div class="tournament-page__content d-flex flex-row justify-content-between">
            <!-- Formulario de registro -->
            <div class="tournament-page__form form-registry col-6">
                <h2 class="form-registry__title">Inscribir Participante</h2>
                <form action="<?= base_url() ?>tournament/add-participant" method="POST" class="form-registry__form">
                    <div class="form-registry__field">
                        <label for="jls-jumper-name" class="form-registry__label">Nombre de Jumper:</label>
                        <input type="text" id="jls-jumper-name" name="jls-jumper-name" class="form-registry__input" required>
                        <input type="hidden" name="jls-tournament-id" value="<?= $tournament_id ?>">
                    </div>
                    <div class="form-registry__actions">
                        <input type="submit" value="Inscribir" class="form-registry__button">
                        <?php
                        if (session()->getFlashdata('participant_added')) {
                        ?>
                            <div class="alert-message">
                                <?= session()->getFlashdata('participant_added') ?>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </form>
            </div>

            <!-- Información del torneo -->
            <div class="tournament-page__info col-6">
                <h3 class="tournament-page__info-title">Reglas del Torneo</h3>
                <ul>
                    <li>Prohibido acelerar o estirar los videos</li>
                    <li>El solo debe verse con claridad</li>
                    <li>El vídeo grabado debe estar subido a Youtube, después postearlo aquí</li>
                    <li>Solo un clip por cada ronda</li>
                </ul>
            </div>
        </div>

        <!-- Lista de participantes -->
        <div class="tournament-page__participants">
            <h2 class="tournament-page__participants-title">Lista de Participantes</h2>
            <ul class="tournament-page__participants-list">
                <?php foreach ($participants as $participant): ?>
                    <li class="tournament-page__participant"><?= htmlspecialchars($participant["alias"], ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <!-- Clasificación -->
        <div class="tournament-page__ranking">
            <h3 class="tournament-page__ranking-title">Clasificación del Torneo</h3>
            <!-- Clasificación pendiente -->
        </div>
    </div>
</body>

</html>