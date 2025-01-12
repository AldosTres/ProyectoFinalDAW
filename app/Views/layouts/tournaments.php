<?php include_once TEMPLATES_VIEWS_PATH . 'html_header.php'; ?>

<body>
    <!-- Navbar -->
    <?php include_once TEMPLATES_VIEWS_PATH . 'header.php'; ?>

    <div class="available-tournaments-page container-fluid mt-5 pt-5">
        <h1 class="available-tournaments__title mt-5">Torneos Disponibles</h1>
        <div class="available-tournaments__container">
            <?php
            if (isset($tournaments)) {
                foreach ($tournaments as $tournament) {
            ?>
                    <!-- Tarjeta de torneo -->
                    <div class="available-tournaments__card">
                        <div class="available-tournaments__image-container">
                            <img src="<?= base_url() ?>/img/logos_torneos/<?= $tournament['logo_path'] ?>.jpg" alt="Torneo <?= htmlspecialchars($tournament['nombre'], ENT_QUOTES, 'UTF-8') ?>" class="available-tournaments__tournament-image">
                        </div>
                        <div class="available-tournaments__content">
                            <a href="<?= base_url('tournament') ?>/<?= $tournament['id'] ?>" class="available-tournaments__link">
                                <span class="available-tournaments__sub-title">
                                    <?= htmlspecialchars($tournament['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </a>
                            <p class="available-tournaments__description">
                                Este torneo forma parte de nuestra comunidad de baile. Únete a la competición y demuestra tus habilidades en un ambiente lleno de pasión y energía. ¡Te esperamos para vivir una experiencia única!
                            </p>
                            <a class="available-tournaments__action action-button" href="<?= base_url('tournament') ?>/<?= $tournament['id'] ?>">
                                Descubrir
                                <span aria-hidden="true" class="available-tournaments__arrow">
                                    →
                                </span>
                            </a>
                        </div>
                    </div>
                <?php
                }
            } else {
                ?>
                <p class="available-tournaments__no-tournaments">No hay torneos disponibles en este momento. ¡Vuelve pronto!</p>
            <?php
            }
            ?>
        </div>
    </div>
    <!-- Footer -->
    <?php include_once TEMPLATES_VIEWS_PATH . 'footer.php'; ?>
</body>

</html>