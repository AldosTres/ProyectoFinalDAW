<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://kit.fontawesome.com/d10a6cd004.js" crossorigin="anonymous"></script> -->
    <script src="https://kit.fontawesome.com/d10a6cd004.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?php base_url() ?>css/login.css">
    <link rel="stylesheet" href="<?php base_url() ?>css/global.css">
    <title>Inicia sesión</title>
</head>

<body>
    <form action="check_login" class="form-login" method="post">
        <div class="form-login__mark">
            <a href="index" class="form-login__link form-login__link--no-color">
                <i class="fa-solid fa-arrow-left form-login__icon"></i>
            </a>
        </div>
        <p class="form-login__heading">Inicia sesión</p>
        <div class="form-login__input-container">
            <div class="form-login__input-icon">
                <i class="fa-solid fa-user form-login__icon"></i>
            </div>
            <input type="text" class="form-login__input-field" id="jls_username" name="jls_username" placeholder="Nombre usuario">
        </div>

        <div class="form-login__input-container">
            <div class="form-login__input-icon">
                <i class="fa-solid fa-lock form-login__icon"></i>
            </div>
            <input type="password" class="form-login__input-field" id="jls_user_password" name="jls_user_password" placeholder="Contraseña">
        </div>


        <button id="form-login__button">Entrar</button>
        <a class="form-login__forgot-link form_login__link" href="#">¿Olvidaste la contraseña?</a>
        <a href="get_register_user_page" class="form-login__new_user form_login__link">¿Nuevo miembro?</a>
    </form>

</body>

</html>