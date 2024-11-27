<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/d10a6cd004.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= base_url() ?>css/login.css">
    <link rel="stylesheet" href="<?= base_url() ?>css/global.css">
    <title>Registro</title>
</head>

<body>
    <form action="user/register" class="form-login" method="post">
        <div class="form-login__mark">
            <a href="index" class="form_login__link form_login__link--no_color">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
        <p class="form-login__heading">Registro</p>
        <div class="form-login__input-container">
            <div class="form-login__input-icon">
                <i class="fa-solid fa-user-ninja"></i>
            </div>
            <input type="text" class="form-login__input-field" id="jls_username" name="jls_username" placeholder="Nombre Jumper">
        </div>

        <div class="form-login__input-container">
            <!-- <svg class="form-login__input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#2e2e2e" viewBox="0 0 16 16">
                <path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z"></path>
            </svg> -->
            <div class="form-login__input-icon">
                <i class="fa-solid fa-user"></i>
            </div>
            <input type="text" class="form-login__input-field" id="jls_username_init" name="jls_username_init" placeholder="Nombre usuario">
        </div>

        <div class="form-login__input-container">
            <div class="form-login__input-icon">
                <i class="fa-solid fa-lock"></i>
            </div>
            <input type="password" class="form-login__input-field" id="jls_user_password" name="jls_user_password" placeholder="Contraseña">
        </div>


        <button id="form-login__button">Registrarse</button>
        <a class="form-login__forgot-link form_login__link" href="login">¿Tienes cuenta? Inicia sesión</a>

    </form>

</body>

</html>