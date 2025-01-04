<?php

namespace App\Controllers;

use App\models\DataBaseHandler;

class Home extends BaseController
{
    public function index(): string
    {
        $jls_database = new DatabaseHandler();

        $tournaments = $jls_database->jls_get_tournaments_by_filter('active');
        $data['tournaments'] = $tournaments;
        $data['title'] = 'Jumpstyle League Series';
        if (session()->get('user_id')) {
            return view('layouts/userIndex', $data);
        } else {
            return view('layouts/index', $data);
        }
    }

    public function get_login_page(): string
    {
        //Uso return porque solo necesito mostrar la página, sin realizar nada más
        return view('layouts/login');
    }

    public function get_register_user_page(): string
    {
        return view('layouts/registry');
    }

    public function register_user()
    {
        $jls_database = new DataBaseHandler();
        $name = $this->request->getPost('jls_username');
        $user = $this->request->getPost('jls_username_init');
        $password = $this->request->getPost('jls_user_password');

        if (empty($name) || empty($user) || empty($password)) {
            return redirect()->to('register')->with('error', 'Todos los campos son obligatorios.');
        }

        $resultado = $jls_database->jls_register_user($name, $user, $password);

        if ($resultado) {
            return redirect()->to('login')->with('success', 'Usuario registrado correctamente, inicia sesión para continuar.');
        } else {
            return redirect()->to('register')->with('error', 'El nombre de usuario o alias ya se encuentra en uso.');
        }
        // return view('layouts/login');
    }
    public function logout(): string
    {
        $data['title'] = 'Jumpstyle League Series';
        return view('layouts/index');
    }

    public function check_login(): string
    {
        $jls_database = new DataBaseHandler();
        $user = $this->request->getPost('jls_username');
        $password = $this->request->getPost('jls_user_password');
        $result = $jls_database->jls_check_user($user, $password);
        if ($result == 0) {
            //No se ha encontrado al usuario
            // session()->setFlashdata('login_error', 'El nombre de usuario o contraseña son incorrectos');
            $data['login_error'] = 'El nombre de usuario o contraseña son incorrectos';
            // return view('layouts/login');
            // return redirect()->to('/get_login_page');
            return view('layouts/login', $data);
        } else {
            //Devolviendo los datos correspondientes al user
            $user_data = $jls_database->jls_get_user_data($result);
            $tournaments = $jls_database->jls_get_tournaments_by_filter('active');
            $data['tournaments'] = $tournaments;
            $data['title'] = 'Jumpstyle League Series';
            $jls_database->jls_update_last_connection($result);
            session()->set('jumper_user_name', $user_data->nombre_usuario);
            session()->set('user_id', $result);

            return view('layouts/userIndex', $data);
        }
    }


    public function upload_tournament()
    {
        $jls_database = new DataBaseHandler();
        $tournament_name = $this->request->getPost('name');
        $tournament_init_date = $this->request->getPost('start-date');
        $tournament_end_date = $this->request->getPost('end-date');
        $tournament_logo = $this->request->getFile('logo');

        //Identificador unico para el logotipo del torneo
        $unique_id = uniqid("torneo_", true);

        // Verifico si el archivo es válido
        if ($tournament_logo->isValid() && !$tournament_logo->hasMoved()) {
            // Obtengo la extensión del archivo
            $file_extension = $tournament_logo->getExtension();
            // Muevo el archivo al destino
            $tournament_logo->move(LOGO_TOURNAMENTS_PATH, $unique_id . '.' . $file_extension);
        }


        //Obtengo la extension de la imagen para permitir que se suba imágenes con diferentes extensiones convirtiéndolo a minúsculas
        // $file_extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

        //Creo la ruta que se almacenará en BBDD y en la carpeta de imágenes
        // $logo_path = LOGO_TOURNAMENTS_PATH . $unique_id . '.' . $file_extension;

        // Guarda la foto en la carpeta de imágenes
        // move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path);

        $result = $jls_database->jls_upload_new_tournament($tournament_name, $tournament_init_date, $tournament_end_date, $unique_id);
        if ($result) {
            return redirect()->to('admin')->with('success', 'El torneo se ha creado correctamente');
        } else {
            return redirect()->to('admin')->with('error', 'No se ha podido crear el torneo. Verrifica si los datos introducidos son correctos');
        }
    }


    public function add_new_participant()
    {
        // MEJORAR
        // ENVIAR A LA PAGINA DEL TORNEO POR GET

        $jls_database = new DataBaseHandler();
        $jls_name = $this->request->getPost('jls-jumper-name');
        $jls_tournament_id = $this->request->getPost('jls-tournament-id');
        if (session()->get("user_id")) {
            $result = $jls_database->jls_add_new_participant($jls_name, $jls_tournament_id, session()->get("user_id"));
            $tournaments = $jls_database->jls_get_tournaments_by_filter();
            $data['tournaments'] = $tournaments;
            $data['title'] = 'Jumpstyle League Series';
            return view('layouts/userIndex', $data);
            // return redirect()->to('tournament');
        } else {
            //Lo que hacemos es establecer un mensaje de un solo uso que se elimina después de ser utilizado, uso correcto en este caso
            session()->setFlashdata("user_not_found_error", "Tiene que iniciar sesión para poder inscribirse");

            //Redirijo a la página de inicio de sesión
            return redirect()->to('/login');
        }
    }
    public function get_tournament_info_page($jls_tournament_id): string
    {
        $jls_database = new DataBaseHandler();
        // $jls_tournament_id = $this->request->getPost('tournament_id');
        // $jls_tournament_id = $_GET['tournament_id'];
        $jls_participants = $jls_database->jls_get_tournament_participants($jls_tournament_id);
        $data['participants'] = $jls_participants;
        $data['title'] = 'Torneo';
        return view("layouts/tournament_info", $data);
    }

    public function admin(): string
    {
        return view('layouts/admin');
    }

    public function get_tournaments($page, $items_per_page)
    {
        $jls_database = new DataBaseHandler();
        $status = $_GET['status'] ?? null; //Cuando no está seteado, por defecto es todos. Recibo del js.
        $name = $_GET['name'] ?? null;

        $offset = max(0, ($page - 1) * $items_per_page);

        $tournaments = $jls_database->jls_get_tournaments_by_filter($status, $name, $items_per_page, $offset);
        $total_tournaments = $jls_database->jls_count_tournaments_by_filter($status, $name);

        $response = [
            'status' => 'success',
            'tournaments' => $tournaments,
            'total_pages' => $items_per_page > 0 ? ceil($total_tournaments / $items_per_page) : 0

        ];

        return json_encode($response);
    }

    public function get_tournament_for_edit()
    {
        $jls_database = new DataBaseHandler();
        if (isset($_GET['tournamentId'])) {
            $tournament_id = $_GET['tournamentId'];
        }
        $tournament_info = $jls_database->jls_get_tournament_info($tournament_id);
        $response = [
            'status' => 'success',
            'tournament_info' => $tournament_info,
        ];
        return json_encode($response);
    }

    public function edit_tournament()
    {
        $jls_database = new DataBaseHandler();
        $id = $this->request->getPost('tournament-id');
        $name = $this->request->getPost('edit-name');
        $start_date = $this->request->getPost('edit-start-date');
        $end_date = $this->request->getPost('edit-end-date');
        $logo = $this->request->getFile('edit-logo');

        $old_logo_name = $jls_database->jls_get_tournament_logo_name($id);
        //Identificador unico para el logotipo del torneo
        $unique_id = uniqid("torneo_", true);

        // Verifico si el archivo es válido
        if ($logo->isValid() && !$logo->hasMoved()) {
            // Obtengo la extensión del archivo
            $file_extension = $logo->getExtension();
            //Logotipo que tenía antes el torneo
            $old_logo_path = LOGO_TOURNAMENTS_PATH . $old_logo_name . '.jpg';
            // Muevo el archivo al destino
            $logo->move(LOGO_TOURNAMENTS_PATH, $unique_id . '.' . $file_extension);

            // Verifico si el archivo existe y lo elimino
            if (file_exists($old_logo_path)) {
                unlink($old_logo_path);  // Elimino el archivo antiguo
            }
        }

        $result = $jls_database->jls_update_tournament_data($id, $name, $start_date, $end_date, 1, $unique_id);
        if ($result) {
            $response = [
                'status' => 'success',
                'title' => 'Torneo modificado',
                'message' => 'El torneo ' . $name . ' se ha modificado correctamente',
                'a' => $old_logo_path
            ];
        } else {
            $response = [
                'status' => 'error',
                'title' => 'Error de modificación',
                'message' => 'Ha ocurrido un error al modificar el torneo'
            ];
        }
        return json_encode($response);
    }

    public function change_tournament_status($tournament_id)
    {
        $jls_database = new DataBaseHandler();
        $tournament_status = $this->request->getGet('tournamentStatus');
        $result = $jls_database->jls_change_tournament_status($tournament_id, $tournament_status);
        $response = [
            'status' => 'success',
            'title' => 'Cambio exitoso',
            'message' => 'Se ha cambiado correctamente el estado del torneo'
        ];
        return json_encode($response);
    }

    public function get_tournament_participants()
    {
        $jls_database = new DataBaseHandler();
        if (isset($_GET['tournamentId'])) {
            $tournament_id = $_GET['tournamentId'];
            $exists_bracket = $_GET['existsBracket'] ?? false;
            $participants = $jls_database->jls_get_tournament_participants($tournament_id, $exists_bracket);
            $response = [
                'status' => 'success',
                'participants' => $participants
            ];
            return json_encode($response);
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Fallo al recibir respuesta del servidor'
            ];
            return json_encode($response);
        }
    }

    public function change_participant_status($participant_id)
    {
        $jls_database = new DataBaseHandler();
        if (isset($_GET['participantStatus']) && isset($participant_id)) {
            $result = $jls_database->jls_change_participant_status($participant_id, $_GET['participantStatus']);
            $response = [
                'status' => 'error',
                'title' => 'Modificación estado',
                'message' => $result ? 'Se ha modificado el estado del participante' : 'Ha ocurrido un error al modificar el estado del participante'
            ];
        } else {
            $response = [
                'status' => 'error',
                'title' => 'No se encontraron datos',
                'message' => 'No se ha podido encontrar el id del participante ni su estado'
            ];
        }
        return json_encode($response);
    }

    public function get_users($page, $items_per_page)
    {
        // alias, role, status, registrationStart, registrationEnd
        $jls_database = new DataBaseHandler();
        $alias = $_GET['alias'] ?? 'all';
        $role = $_GET['role'] ?? 'all';
        $status = $_GET['status'] ?? 'all';;
        $registration_start = $this->request->getGet('registrationStart') ?? null;
        $registration_end = $this->request->getGet('registrationEnd') ?? null;

        $offset = max(0, ($page - 1) * $items_per_page);

        $users = $jls_database->jls_get_users_by_filter($alias, $role, $status, $registration_start, $registration_end, $items_per_page, $offset);
        $total_users = $jls_database->jls_count_users_by_filter($alias, $role, $status, $registration_start, $registration_end);

        $response = [
            'status' => 'success',
            'users' => $users,
            'total_pages' => $items_per_page > 0 ? ceil($total_users / $items_per_page) : 0
        ];
        return json_encode($response);
    }

    public function get_user_rol_types()
    {
        $jls_database = new DataBaseHandler();
        $user_rol_types = $jls_database->jls_get_user_rol_types();
        $response = [
            'status' => 'success',
            'user_rol_types' => $user_rol_types
        ];
        return json_encode($response);
    }

    public function change_user_rol()
    {
        $jls_database = new DataBaseHandler();
        $user_id = $this->request->getPost('user-id');
        $rol_id = $this->request->getPost('rol-select');
        $result = $jls_database->jls_change_user_rol($user_id, $rol_id);
        if ($result) {
            $response = [
                'status' => 'succes',
                'title' => 'Rol de usuario modificado',
                'message' => 'se ha modificado correctamente el rol de usuario'
            ];
        } else {
            $response = [
                'status' => 'error',
                'title' => 'Rol de usuario',
                'message' => 'No se ha podido modificar el rol de usuario'
            ];
        }
        return json_encode($response);
    }

    public function change_user_status()
    {
        $jls_database = new DataBaseHandler();
        if (isset($_GET['userId']) && isset($_GET['userStatus'])) {
            $user_id = $_GET['userId'];
            $user_status = $_GET['userStatus'];
            $result = $jls_database->jls_change_user_status($user_id, $user_status);
            if ($result) {
                $response = [
                    'status' => 'success',
                    'title' => 'Cambio de estado',
                    'message' => 'Estado de usuario cambiado correctamente'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'title' => 'Cambio de estado',
                    'message' => 'No se ha podido completar el cambio de estado'
                ];
            }
            return json_encode($response);
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Ha ocurrido un fallo al encontrar al usuario o al estado del usuaro'
            ];
            return json_encode($response);
        }
    }

    public function get_tournament_bracket($tournament_id)
    {
        // Lógica para obtener el bracket del torneo con el ID dado
        $jls_database = new DataBaseHandler();
        $rounds_type = $jls_database->jls_get_rounds();
        $created_rounds = $jls_database->jls_get_round_info($tournament_id);

        $response = [
            'status' => 'success',
            'rounds_type' => $rounds_type,
            'tournament_id' => $tournament_id,
            'matches' => $created_rounds ?? null
        ];
        // Responder con los datos en formato JSON
        return json_encode($response);
    }

    public function add_participant_to_bracket($tournament_id)
    {
        $jls_database = new DataBaseHandler();
        // matchPosition, roundId, firstParticipantId, secondParticipantId
        $first_participant_id = $this->request->getPost('firstParticipantId');
        $second_participant_id = $this->request->getPost('secondParticipantId');
        $round_type_id = $this->request->getPost('roundId');
        $match_position = $this->request->getPost('matchPosition');
        $result = $jls_database->jls_add_new_tournament_match($tournament_id, $first_participant_id, $second_participant_id, $round_type_id, $match_position);
        if ($result) {
            $response = [
                'status' => 'success',
                'title' => 'Enfrentamiento añadido',
                'message' => 'Se ha añadido correctamente el enfrentamiento'
            ];
        } else {
            $response = [
                'status' => 'error',
                'title' => 'Error enfrentamiento',
                'message' => 'Ha ocurrido un error al añadir el enfrentamiento'
            ];
        }

        // Responder con los datos en formato JSON
        return json_encode($response);
    }
    public function get_scoring_criteria()
    {
        $jls_database = new DataBaseHandler();
        $scoring_criteria = $jls_database->jls_get_tournament_scoring_criteria();
        $response = [
            'status' => 'success',
            'scoring_criteria' => $scoring_criteria
        ];
        return json_encode($response);
    }

    public function upload_participant_scores($tournament_id, $round_id, $participant_id)
    {
        $jls_database = new DataBaseHandler();
        // Captura y decodifica el array de puntuaciones
        $scores = $this->request->getPost('scores');
        $decodedScores = json_decode($scores, true);
        // Validar el array
        if (!is_array($decodedScores) || empty($decodedScores)) {
            return json_encode([
                'status' => 'Error',
                'title' => 'Puntuaciones no válidas o vacías.'
            ]);
        } else {
            $result = $jls_database->jls_upload_participant_scores($tournament_id, $round_id, $participant_id, $decodedScores);
            if ($result['status'] === 'success') { // determinar ganador si es posible
                $winnerResult = $jls_database->jls_determine_and_register_winner($tournament_id, $round_id);
                if ($winnerResult['status'] === 'success' && $round_id != 3) {
                    // Crear o actualizar siguiente ronda
                    $nextRoundResult = $jls_database->add_next_round($tournament_id, $round_id, $winnerResult['winner']);
                    if ($nextRoundResult['status'] === 'success') {
                        return json_encode([
                            'status' => 'success',
                            'title' => 'Puntuaciones y enfrentamiento',
                            'message' => 'Se añadieron las puntuaciones y se generaron nuevo enfrentamientos'
                        ]);
                    }
                }
                return json_encode([
                    'status' => 'success',
                    'title' => 'Puntuaciones añadidas',
                    'message' => 'Se añadieron las puntuaciones del participante pero ' . $winnerResult['message']
                ]);
            } else {
                return json_encode([
                    'status' => 'success',
                    'title' => 'Puntuaciones',
                    'message' => $result['message']
                ]);
            }
        }
    }

    /**
      Funciones del apartado de eventos
     */

    public function upload_event()
    {
        $jls_database = new DataBaseHandler();
        $event_name = $this->request->getPost('event-name');
        $event_description = $this->request->getPost('event-description');
        $event_start_date = $this->request->getPost('event-start-date');
        $event_end_date = $this->request->getPost('event-end-date');
        $event_location = $this->request->getPost('event-location');
        $event_image = $this->request->getFile('event-image');

        // Generar un identificador único para la imagen del evento
        $unique_id = uniqid("evento_", true);

        // Verificar si el archivo es válido
        if ($event_image->isValid() && !$event_image->hasMoved()) {
            // Obtener la extensión del archivo
            $file_extension = $event_image->getExtension();
            // Mover el archivo al destino
            $event_image->move(LOGO_EVENTS_PATH, $unique_id . '.' . $file_extension);
            // return redirect()->to('admin')->with('success', 'Entra a crear la imagen');
        }

        // Llamar al método de la base de datos para guardar los datos
        $result = $jls_database->jls_upload_new_event(
            $event_name,
            $event_description,
            $event_start_date,
            $event_end_date,
            $event_location,
            $unique_id
        );

        // Redirigir según el resultado
        if ($result) {
            return redirect()->to('admin')->with('success', 'El evento se ha creado correctamente');
        } else {
            return redirect()->to('admin')->with('error', 'No se ha podido crear el evento. Verifica si los datos introducidos son correctos');
        }
    }

    public function get_events_by_filter($page, $items_per_page)
    {
        // nombre, estado, fechaInicioStart, fechaFinEnd
        $jls_database = new DataBaseHandler();
        $event_name = $_GET['name'] ?? 'all';
        $event_status = $_GET['status'] ?? 'all';
        $event_start_date = $this->request->getGet('startDate') ?? null;
        $event_end_date = $this->request->getGet('endDate') ?? null;
        $event_active = $this->request->getGet('eventActive') ?? null;

        $offset = max(0, ($page - 1) * $items_per_page);

        $events = $jls_database->jls_get_events_by_filter($event_name, $event_status, $event_active, $event_start_date, $event_end_date, $items_per_page, $offset);
        $total_events = $jls_database->jls_count_events_by_filter($event_name, $event_status, $event_active, $event_start_date, $event_end_date);

        $response = [
            'status' => 'success',
            'events' => $events,
            'total_pages' => $items_per_page > 0 ? ceil($total_events / $items_per_page) : 0
        ];
        return json_encode($response);
    }
    public function get_event_details($event_id)
    {
        $jls_database = new DataBaseHandler();
        $event = $jls_database->jls_get_event_details($event_id);
        $response = [
            'status' => 'success',
            'title' => 'Detalles de evento',
            'event' => $event,

        ];
        return json_encode($response);
    }

    public function edit_event($event_id)
    {
        $jls_database = new DataBaseHandler();
        $event_name = $this->request->getPost('edit-event-name');
        $event_description = $this->request->getPost('edit-event-description');
        $event_start_date = $this->request->getPost('edit-event-start-date');
        $event_end_date = $this->request->getPost('edit-event-end-date');
        $event_location = $this->request->getPost('edit-event-location');
        $event_image = $this->request->getFile('edit-event-image');

        $old_event_image = $jls_database->jls_get_event_image_name($event_id);
        //Identificador unico para el logotipo del torneo
        $unique_id = uniqid("evento_", true);

        // Verifico si el archivo es válido
        if ($event_image->isValid() && !$event_image->hasMoved()) {
            // Obtengo la extensión del archivo
            $file_extension = $event_image->getExtension();
            //Logotipo que tenía antes el torneo
            $old_image_path = LOGO_EVENTS_PATH . $old_event_image . '.jpg';
            // Muevo el archivo al destino
            $event_image->move(LOGO_EVENTS_PATH, $unique_id . '.' . $file_extension);

            // Verifico si el archivo existe y lo elimino
            if (file_exists($old_image_path)) {
                unlink($old_image_path);  // Elimino el archivo antiguo
            }
        }
        $result = $jls_database->jls_update_event_data(
            $event_id,
            $event_name,
            $event_description,
            $event_start_date,
            $event_end_date,
            $event_location,
            $unique_id
        );
        if ($result) {
            $response = [
                'status' => 'success',
                'title' => 'Evento modificado',
                'message' => 'El evento ' . $event_name . ' se ha modificado correctamente'
            ];
        } else {
            $response = [
                'status' => 'error',
                'title' => 'Error de modificación',
                'message' => 'Ha ocurrido un error al modificar el Evento'
            ];
        }
        return json_encode($response);
    }

    public function change_event_active_status($event_id)
    {
        $jls_database = new DataBaseHandler();
        if (isset($_GET['eventActiveStatus']) && isset($event_id)) {
            $result = $jls_database->jls_change_event_active_status($event_id, $_GET['eventActiveStatus']);
            $response = [
                'status' => 'error',
                'title' => 'Modificación estado Activo',
                'message' => $result ? 'Se ha modificado el estado Activo del evento' : 'Ha ocurrido un error al modificar el estado activo'
            ];
        } else {
            $response = [
                'status' => 'error',
                'title' => 'No se encontraron datos',
                'message' => 'No se ha podido encontrar el id del evento ni el estado del activo'
            ];
        }
        return json_encode($response);
    }

    /**
     * * Apartado settings
     */
    public function get_round_types()
    {
        $jls_database = new DataBaseHandler();
        $round_types = $jls_database->jls_get_rounds();
        $response = [
            'status' => 'success',
            'round_types' => $round_types
        ];
        return json_encode($response);
    }
}
