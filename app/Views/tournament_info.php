<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torneo</title>
</head>

<body>
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
</body>

</html>