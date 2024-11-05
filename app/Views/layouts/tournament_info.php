<?php include_once TEMPLATES_VIEWS_PATH . 'header.php' ?>

<body>

    <!-- Insercion del navbar -->
    <?php include_once TEMPLATES_VIEWS_PATH . 'navbar_header.php' ?>
    <!-- Insercion del sidebar -->
    <?php include_once TEMPLATES_VIEWS_PATH . 'sidebar.php' ?>

    <div class="fullpage">
        <h1>Información del torneo</h1>
        <form action="get_add_participant_page" method="post">
            <input type="hidden" name="jls-tournament-id" value="<?php if (isset($_POST["tournament_id"])) {
                                                                        echo $_POST["tournament_id"];
                                                                    } ?>">

            <input type="submit" value="Participar">
        </form>
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