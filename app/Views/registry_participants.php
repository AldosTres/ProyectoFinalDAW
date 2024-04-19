<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php base_url() ?>css/registry_participants.css">
    <title>Registro torneo</title>
</head>

<body>
    <div class="form-registry-participant">
        <h2 class="form-registry-participant__title">Inscribir Participante</h2>
        <form action="procesar_inscripcion.php" method="POST">
            <label for="nombre">Nombre de bailarin:</label>
            <input type="text" id="jls-jumper-name" name="jls-jumper-name" class="form-registry-participant__jumper_name" required>
            <input type="submit" value="Inscribir" class="form-registry-participant__submit-button">
        </form>
    </div>
</body>

</html>