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

        //Identificador unico para el logotipo del torneo
        $unique_id = uniqid("torneo_", true);

        //Obtengo la extension de la imagen para permitir que se suba imágenes con diferentes extensiones convirtiéndolo a minúsculas
        $file_extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

        //Creo la ruta que se almacenará en BBDD y en la carpeta de imágenes
        $logo_path = LOGO_TOURNAMENTS_PATH . $unique_id . '.' . $file_extension;

        // Guarda la foto en la carpeta de imágenes
        move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path);

        $result = $jls_database->jls_upload_new_tournament($tournament_name, $tournament_init_date, $tournament_end_date, $unique_id);
        if ($result) {
            return redirect()->to('admin')->with('success', 'El torneo se ha creado correctamente');
        } else {
            return redirect()->to('admin')->with('error', 'No se ha podido crear el torneo. Verrifica si los datos introducidos son correctos');
        }
    }


    public function add_new_participant()
    {
        $jls_database = new DataBaseHandler();
        $jls_name = $this->request->getPost('jls-jumper-name');
        $jls_tournament_id = $this->request->getPost('jls-tournament-id');
        if (session()->get("user_id")) {
            $jls_database->jls_add_new_participant($jls_name, $jls_tournament_id, session()->get("user_id"));
            $tournaments = $jls_database->jls_get_all_active_tournaments();
            $data['tournaments'] = $tournaments;
            $data['title'] = 'Jumpstyle League Series';
            // return view('layouts/userIndex', $data);
            return redirect()->to('layouts/userIndex');
        } else {
            //Lo que hacemos es establecer un mensaje de un solo uso que se elimina después de ser utilizado, uso correcto en este caso
            session()->setFlashdata("user_not_found_error", "Tiene que iniciar sesión para poder inscribirse");

            //Redirijo a la página de inicio de sesión
            return redirect()->to('/get_login_page');
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

    public function edit_tournaments()
    {
        $jls_database = new DataBaseHandler();
        if (isset($_GET['t'])) {
            $tournament_id = $_GET['t'];
        }
        $tournament_info = $jls_database->jls_get_tournament_info($tournament_id);
        $response = [
            'status' => 'success',
            'tournament_info' => $tournament_info,
        ];
        return json_encode($response);
    }
}
