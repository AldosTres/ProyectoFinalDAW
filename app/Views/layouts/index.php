<?php include_once TEMPLATES_VIEWS_PATH . 'header.php' ?>

<body>
    <?php include_once TEMPLATES_VIEWS_PATH . 'navbar_header.php' ?>

    <?php include_once TEMPLATES_VIEWS_PATH . 'sidebar.php' ?>

    <div id="fullpage">
        <div class="section aboutme" data-anchor="aboutme">
            <div class="aboutme__capa-vacia">
                <h1 style="color:white">DESCUBRE EL PODER DEL JUMPSTYLE</h1>
                <button class="aboutme__find-btn">Descubrir</button>
            </div>
            <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop" class="nav-brand__background-video">
                <source src="<?php base_url() ?>video/sN.mp4" type="video/mp4">
            </video>
        </div>

        <div class="about-us">
            <div class="about-us__card-container">
                <!-- <img src="<?php echo base_url() ?>img/jumpstyleleagueseries_origin.jpg" alt="origin-jumpers" class="about-us__img-origin-jumpers"> -->
                <img src="<?php echo base_url() ?>img/allparticipantsjls.jpg" alt="origin-jumpers" class="about-us__img-origin-jumpers">
            </div>
            <div class="about-us__text-container">
                <span class="about-us__title-label-container">Nuestros inicios</span>
                <h2 class="about-us__title-container">¿Qué es Jumpstyle League series?</h2>
                <p class="about-us__info-text-container">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias, sed ipsam. Inventore consectetur ipsum optio maiores in ipsam dolorem perferendis. Omnis cum unde adipisci excepturi hic in ea, repudiandae delectus.</p>
                <p class="about-us__info-text-container">Lorem ipsum dolor sit amet consectetur adipisicing elit. Maiores perspiciatis, quibusdam temporibus quam voluptate quisquam inventore. Mollitia eius, corrupti adipisci unde minus maxime natus distinctio, blanditiis quibusdam labore, quasi aliquid.</p>
            </div>
        </div>

        <div class="available-tournaments">
            <h2 class="available-tournaments__title">Torneos Disponibles</h2>
            <?php
            foreach ($tournaments as $key => $tournament) {
            ?>
                <div class="available-tournaments__tournament-element">
                    <form action="get_tournamente_info_page" method="post">
                        <input type="hidden" name="tournament_id" value="<?= $tournament['id'] ?>">
                        <button type="submit" class="available-tournaments__tournament-info">
                            <div class="available-tournaments__tournament-img">
                                <img src="<?php echo base_url() ?>img/copa_torneo.png" alt="origin-jumpers" class="" alt="tournament-image">
                            </div>
                            <div class="available-tournaments__tournament-desc">
                                <div class="available-tournaments__tournament-title">
                                    <strong><?php echo $tournament['nombre'] ?> </strong>
                                </div>
                                <div class="available-tournaments__tournament-start-date">
                                    <strong>Fecha de Inicio:</strong> <?php echo $tournament['fecha_inicio'] ?>
                                </div>
                                <div class="available-tournaments__tournament-end-date">
                                    <strong>Fecha de Fin:</strong> <?php echo $tournament['fecha_fin'] ?>
                                </div>
                            </div>
                        </button>
                    </form>

                </div>
            <?php } ?>
        </div>
    </div>
    <?php include_once TEMPLATES_VIEWS_PATH . 'footer.php';
    ?>
</body>

</html>