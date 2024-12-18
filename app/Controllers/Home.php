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
    public function get_tournament_info_page(): string
    {
        $jls_database = new DataBaseHandler();
        $jls_tournament_id = $this->request->getPost('tournament_id');
        $jls_participants = $jls_database->jls_get_tournament_participants($jls_tournament_id);
        $data['participants'] = $jls_participants;
        $data['title'] = 'Torneo';
        return view("layouts/tournament_info", $data);
    }

    public function admin(): string
    {
        return view('layouts/admin');
    }

    public function get_tournaments()
    {
        $jls_database = new DataBaseHandler();
        $status = isset($_GET['status']) ? $_GET['status'] : 'all'; //Cuando no está seteado, por defecto es todos. Recibo del js.
        $tournaments = $jls_database->jls_get_tournaments_by_filter($status);

        $response = [
            'status' => 'success',
            'tournaments' => $tournaments
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
                'message' => 'El torneo ' . $name . ' se ha modificado correctamente'
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
    public function get_tournament_participants()
    {
        $jls_database = new DataBaseHandler();
        if (isset($_GET['tournamentId'])) {
            $tournament_id = $_GET['tournamentId'];
            $participants = $jls_database->jls_get_tournament_participants($tournament_id, true);
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

    public function get_users()
    {
        // alias, role, status, registrationStart, registrationEnd
        $jls_database = new DataBaseHandler();
        $alias = $_GET['alias'] ?? 'all';
        $role = $_GET['role'] ?? 'all';
        $status = $_GET['status'] ?? 'all';;
        $registration_start = $this->request->getGet('registrationStart') ?? null;
        $registration_end = $this->request->getGet('registrationEnd') ?? null;
        $users = $jls_database->jls_get_users_by_filter($alias, $role, $status, $registration_start, $registration_end);

        $response = [
            'status' => 'success',
            'users' => $users
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
        //revisar
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
                'title' => 'Error enfrentamiento, id torneo=' . $tournament_id,
                'message' => 'id torneo: ' . $tournament_id . 'id_u_1: ' . $first_participant_id . ' id_u_2: ' . $second_participant_id . ' id_ronda: ' . $round_type_id . ' posMatch: ' . $match_position
            ];
        }

        // Responder con los datos en formato JSON
        return json_encode($response);
    }
}
