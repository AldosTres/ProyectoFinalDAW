<?php include_once TEMPLATES_VIEWS_PATH . 'html_header.php'; ?>

<body>
    <?php include_once TEMPLATES_VIEWS_PATH . 'header.php'; ?>

    <main class="main-content mt-10">
        <section class="about">
            <div class="about__container">
                <h2 class="about__title">Nuestra Historia</h2>
                <p class="about__description">
                    El Jumpstyle nació a finales de los años 90 en los Países Bajos como un estilo de baile que acompañaba a la música electrónica hardstyle y techno. Su energía contagiosa y movimientos rítmicos pronto se extendieron por Europa, ganando adeptos en países como Alemania, Francia y Bélgica. Más que un baile, el Jumpstyle se convirtió en una cultura, uniendo a personas de distintas edades y orígenes bajo una misma pasión: el baile.

                    Inspirados por esta fuerza unificadora, Jumpstyle League Series surgió como un proyecto para preservar y promover esta cultura única. Nuestro sueño comenzó con pequeños meetings al rededor de España, pero con el tiempo evolucionamos hasta convertirnos en un punto de referencia internacional para jumpers de todas partes del mundo. Desde entonces, hemos trabajado para consolidar un espacio donde el talento, la creatividad y la compañía sean los pilares.

                    Hoy en día, Jumpstyle League Series se dedica a organizar torneos que trascienden lo competitivo. Queremos que el Jumpstyle nunca se pierda, fomentar el crecimiento de la comunidad y crear lazos entre naciones a través del baile. Cada evento no solo es un espectáculo, sino una oportunidad para escribir nuevas páginas en la historia de esta cultura.

                    Unimos culturas. Celebramos el talento. Mantenemos vivo el espíritu del Jumpstyle.
                </p>
                <div class="about__description-video d-flex justify-content-center pt-2 mt-4">
                    <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop" class="img-fluid rounded">
                        <source src="<?php base_url() ?>video/Our_history.mp4" type="video/mp4">
                    </video>
                </div>
            </div>
        </section>

        <section class="values">
            <div class="values__container">
                <h2 class="values__title">Nuestros Valores</h2>
                <div class="values__list mt-3">

                </div>
            </div>
        </section>

        <section class="team">
            <div class="team__container">
                <h2 class="team__title">Conoce al Equipo</h2>
                <div class="team__owners-container d-flex flex-row justify-content-center gap-5 mb-5 mt-5">

                </div>
                <p class="team__description">
                    Nuestro equipo está compuesto por profesionales apasionados con experiencia en el mundo del baile y la organización de eventos.
                </p>
            </div>
        </section>
    </main>
    <?php include_once TEMPLATES_VIEWS_PATH . 'footer.php'; ?>
    <script>
        const BASE_URL = "<?= base_url() ?>"; //Constante para poder usar desde el js la base_url()
    </script>
</body>
</body>

</html>
<!-- ========== End Section ========== -->