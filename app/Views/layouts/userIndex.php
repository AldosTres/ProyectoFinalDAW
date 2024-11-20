<?php include_once TEMPLATES_VIEWS_PATH . 'header.php' ?>

<body>
    <!-- Insercion del navbar -->
    <?php include_once TEMPLATES_VIEWS_PATH . 'navbar_header.php' ?>
    <!-- Insercion del sidebar -->
    <?php include_once TEMPLATES_VIEWS_PATH . 'sidebar.php' ?>
    <!-- Insercion página entera -->
    <div id="fullpage">
        <div class="section aboutme" data-anchor="aboutme">
            <div class="aboutme__capa-vacia">
                <h1 style="color:white">DESCUBRE EL PODER DEL JUMPSTYLE</h1>
                <button class="aboutme__find-btn">Descubrir</button>
            </div>
            <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop" class="nav-brand__background-video">
                <source src="<?= base_url() ?>video/sN.mp4" type="video/mp4">
            </video>
        </div>

        <div class="about-us">
            <div class="about-us__card-container">
                <img src="<?= base_url() ?>img/allparticipantsjls.jpg" alt="origin-jumpers" class="about-us__img-origin-jumpers">
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
                    <form action="get_tournament_info_page" method="post">
                        <input type="hidden" name="tournament_id" value="<?= $tournament['id'] ?>">
                        <button type="submit" class="tournament-info-button">
                            <div class="available-tournaments__tournament-info">
                                <strong>Nombre del Torneo:</strong> <?= $tournament['nombre'] ?>
                            </div>
                            <div class="available-tournaments__tournament-info">
                                <strong>Fecha de Inicio:</strong> <?= $tournament['fecha_inicio'] ?>
                            </div>
                            <div class="available-tournaments__tournament-info">
                                <strong>Fecha de Fin:</strong> <?= $tournament['fecha_fin'] ?>
                            </div>
                        </button>
                    </form>

                </div>
        </div>

    <?php
            }
    ?>

    <!-- CONTENIDO DE PROXIMOS EVENTOS -->
    <div class="upcoming-events">
        <h2 class="upcoming-events__title">Próximos Eventos</h2>
        <div class="event">
            <img src="evento1.jpg" alt="Evento 1" class="event__image">
            <div class="event__info">
                <h3 class="event__title">Evento 1</h3>
                <p class="event__date">Fecha: 10 de mayo de 2024</p>
                <p class="event__location">Ubicación: Lugar del evento 1</p>
                <p class="event__description">Descripción del evento 1...</p>
            </div>
        </div>
        <div class="event">
            <img src="evento2.jpg" alt="Evento 2" class="event__image">
            <div class="event__info">
                <h3 class="event__title">Evento 2</h3>
                <p class="event__date">Fecha: 15 de mayo de 2024</p>
                <p class="event__location">Ubicación: Lugar del evento 2</p>
                <p class="event__description">Descripción del evento 2...</p>
            </div>
        </div>
        <!-- Agrega más eventos según sea necesario -->

        <!-- PROBANDO SPOTIFY -->
        <div class="music-recommendation">
            <h2 class="music-recommendation__title">¡Escucha la Playlist Oficial de JLS!</h2>
            <p class="music-recommendation__description">
                Disfruta de las canciones seleccionadas especialmente para acompañarte en cada torneo.
            </p>

            <!-- Contenedor estilo tarjeta para la playlist -->
            <div class="music-recommendation__card">
                <iframe
                    src="https://open.spotify.com/embed/playlist/3P5GOtafqK00O4dyy2jCNi?utm_source=generator"
                    width="100%"
                    height="380"
                    frameborder="0"
                    allowtransparency="true"
                    allow="encrypted-media">
                </iframe>
            </div>

            <!-- Opcional: Botones para compartir o acceder directamente -->
            <div class="music-recommendation__actions">
                <a href="https://open.spotify.com/embed/playlist/3P5GOtafqK00O4dyy2jCNi?utm_source=generator"
                    target="_blank"
                    class="music-recommendation__button">Abrir en Spotify</a>
                <button class="music-recommendation__button--share">Compartir</button>
            </div>
        </div>
    </div>
    <?php include_once TEMPLATES_VIEWS_PATH . 'footer.php'; ?>
</body>

</html>