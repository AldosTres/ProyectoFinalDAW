<div id="fullpage">
    <div class="section about-me" data-anchor="aboutme">
        <div class="about-me__empty-layer">
            <h1 class="about-me__title" style="color:white">DESCUBRE EL PODER DEL JUMPSTYLE</h1>
            <a href="https://www.youtube.com/watch?v=omiErsv8pLg" target="_blank">
                <button class="about-me__button about-me__button--find ">Descubrir</button>
            </a>
        </div>
        <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop" class="about-me__background-video">
            <source src="<?php base_url() ?>video/sN_1.mp4" type="video/mp4">
        </video>
    </div>

    <div class="about-us">
        <div class="about-us__card-container">
            <img src="<?php echo base_url() ?>img/allparticipantsjls.jpg" alt="origin-jumpers" class="about-us__img-origin-jumpers img-fluid rounded">
        </div>
        <div class="about-us__text-container">
            <span class="about-us__label about-us__label--title">Nuestros inicios</span>
            <h2 class="about-us__title">¿Qué es Jumpstyle League series?</h2>
            <p class="about-us__info-text">Jumpstyle League Series nació con la pasión de preservar y celebrar el arte del Jumpstyle, un estilo de baile que trasciende fronteras y conecta culturas. Nuestro propósito es fomentar la longevidad de este movimiento, uniendo a personas de todas las naciones a través de la música, el talento y la competencia.

                En cada torneo, reunimos a los mejores jumpers, aficionados y jueces, creando un espacio donde el respeto, la creatividad y la comunidad son los protagonistas. Jumpstyle League Series no solo es una competición, es un punto de encuentro para quienes vibran al ritmo del Jumpstyle y quieren dejar huella en su historia.

                ¡Únete a nosotros y mantén vivo el espíritu del Jumpstyle!</p>
        </div>
    </div>

    <div class="available-tournaments">
        <h2 class="available-tournaments__title index-title">Torneos Disponibles</h2>
        <div class="available-tournaments__container">
            <?php
            foreach ($tournaments as $key => $tournament) {
                if ($key < 6) {
            ?>
                    <!-- /* From Uiverse.io by Yaya12085 */ -->
                    <div class="available-tournaments__card">
                        <div class="available-tournaments__image-container">
                            <img src="<?= base_url() ?>/img/logos_torneos/<?= $tournament['logo_path'] ?>.jpg" alt="" class="available-tournaments__tournament-image">
                        </div>
                        <div class="available-tournaments__content">
                            <a href="#" class="available-tournaments__link">
                                <span class="available-tournaments__sub-title">
                                    <?= $tournament['nombre'] ?>
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
            }
            ?>
        </div>
    </div>
    <div class="available-events d-flex flex-column justify-content-center align-items-center">
        <h3 class="available-events__title index-title">Eventos</h3>
        <div class="available-events__container d-flex flex-row flex-wrap justify-content-center">
            <?php
            foreach ($events as $key => $event) {

            ?>
                <!-- From Uiverse.io by Javierrocadev  -->
                <div class="available-events__item d-flex flex-column">
                    <div class="available-events__item-container d-flex flex-row justify-content-around mt-2 img-fluid">
                        <div class="available-events__item-image-container img-rounded">
                            <img src="<?= base_url() ?>img/logos_eventos/<?= $event['url_imagen'] ?>.jpg" class="available-events__item-image rounded img-fluid" alt="" />
                        </div>
                        <div class="available-events__item-presentation">
                            <span class="available-events__item-title"><?= $event['nombre'] ?></span>
                            <span class="text-truncate available-events__item-description"><?= $event['descripcion'] ?></span>
                        </div>
                    </div>
                    <a href="" class="available-events__item-action action-button">Saber más</a>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <!-- Playlist Spotify -->
    <div class="music-recommendation">
        <div class="music-recommendation__info">
            <h2 class="music-recommendation__title index-title">¡Escucha la Playlist Oficial de JLS!</h2>
            <p class="music-recommendation__description">
                Disfruta de las canciones seleccionadas para dar tu máximo en los torneos
            </p>
        </div>

        <!-- Contenedor estilo tarjeta para la playlist -->
        <div class="music-recommendation__card">
            <iframe style="border-radius:12px" src="https://open.spotify.com/embed/playlist/3P5GOtafqK00O4dyy2jCNi?utm_source=generator" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div>
    </div>
</div>
<script>
    const BASE_URL = "<?= base_url() ?>"; //Constante para poder usar desde el js la base_url()
</script>