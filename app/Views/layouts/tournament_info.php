<?php include_once TEMPLATES_VIEWS_PATH . 'header.php' ?>

<body>

    <!-- Insercion del navbar -->
    <?php include_once TEMPLATES_VIEWS_PATH . 'navbar_header.php' ?>
    <!-- Insercion del sidebar -->
    <?php include_once TEMPLATES_VIEWS_PATH . 'sidebar.php' ?>

    <div class="tournament-page">
        <div class="tournament-page__presentation">
            <h1 class="tournament-page__title">Información del torneo</h1>
        </div>
        <div class="tournament-page__items">
            <div class="tournament-page__form-registry-participant">
                <div class="form-registry-participant__item1">
                    <h2 class="form-registry-participant__title">Inscribir Participante</h2>
                    <br>
                    <form action="add_new_participant" method="POST">
                        <label for="nombre">Nombre de Jumper:</label>
                        <input type="text" id="jls-jumper-name" name="jls-jumper-name" class="form-registry-participant__jumper-name" required>
                        <input type="hidden" name="jls-tournament-id" value="<?php if (isset($_POST["jls-tournament-id"])) {
                                                                                    echo $_POST["jls-tournament-id"];
                                                                                } ?>">
                </div>
                <div class="form-registry-participant__item2">
                    <input type="submit" value="Inscribir" class="form-registry-participant__submit-button">
                    <?php
                    if (isset($_SESSION['user_not_found_error'])) {
                    ?>
                        <p><?= $_SESSION['user_not_found_error'] ?></p>
                    <?php
                    }
                    ?>
                </div>
                </form>
            </div>
            <div class="tournament-page__info">
                <h3 class="tournament-page__info-title">Información del torneo</h3>
                <p class="tournament-page__info-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Adipisci atque maxime eum vitae asperiores non fuga nobis! Deleniti consequuntur enim quis incidunt omnis ex. Facere accusantium nesciunt placeat ut voluptate?</p>
            </div>

        </div>
        <div>
            <h2>Lista de participantes</h2>
            <div>
                <?php
                foreach ($participants as $key => $value) {
                    echo $value["alias"];
                }
                ?>

            </div>
        </div>
    </div>
</body>

</html>