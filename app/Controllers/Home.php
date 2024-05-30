<?php

namespace App\Controllers;

use App\models\DataBaseHandler;

class Home extends BaseController
{
    public function index(): string
    {
        $jls_database = new DatabaseHandler();
        $tournaments = $jls_database->jls_get_all_active_tournaments();
        $data['tournaments'] = $tournaments;
        if (session()->get("user_id")) {
            return view('layouts/userIndex', $data);
        } else {
            return view('layouts/index', $data);
        }
    }

    public function get_login_page(): string
    {
        return view('layouts/login');
    }

    public function get_register_user_page(): string
    {
        return view('layouts/registry');
    }

    public function register_user(): string
    {
        $jls_database = new DataBaseHandler();
        $name = $this->request->getPost('jls_username');
        $user = $this->request->getPost('jls_username_init');
        $password = $this->request->getPost('jls_user_password');
        $resultado = $jls_database->jls_register_user($name, $user, $password);
        return view('login');
    }
    public function logout(): string
    {
        return view('layouts/index');
    }

    public function check_login(): string
    {
        $jls_database = new DataBaseHandler();
        $user = $this->request->getPost('jls_username');
        $password = $this->request->getPost('jls_user_password');
        $result = $jls_database->jls_check_user($user, $password);
        if ($result == 0) {
            return view('layouts/login');
        } else {
            //Devolviendo los datos correspondientes al user
            $user_data = $jls_database->jls_get_user_data($result);
            $tournaments = $jls_database->jls_get_all_active_tournaments();
            $data['tournaments'] = $tournaments;
            session()->set("jumper_user_name", $user_data->nombre_usuario);
            session()->set("user_id", $result);

            return view('layouts/userIndex', $data);
        }
    }

    public function get_upload_tournament_page(): string
    {
        return view('layouts/upload_tournament');
    }

    public function upload_tournament(): string
    {
        $jls_database = new DataBaseHandler();
        $tournament_name = $this->request->getPost('jls_tournament_name');
        $tournament_init_date = $this->request->getPost('jls_tournament_init_date');
        $tournament_end_date = $this->request->getPost('jls_tournament_end_date');
        $result = $jls_database->jls_upload_new_tournament($tournament_name, $tournament_init_date, $tournament_end_date);
        return view('layouts/upload_tournament');
    }

    public function get_add_participant_page(): string
    {
        return view('layouts/registry_participants');
    }

    public function add_new_participant(): string
    {
        $jls_database = new DataBaseHandler();
        $jls_name = $this->request->getPost('jls-jumper-name');
        $jls_tournament_id = $this->request->getPost('jls-tournament-id');
        $jls_database->jls_add_new_participant($jls_name, $jls_tournament_id, session()->get("user_id"));
        $tournaments = $jls_database->jls_get_all_active_tournaments();
        $data['tournaments'] = $tournaments;
        if (session()->get("user_id")) {
            return view('layouts/userIndex', $data);
        } else {
            return view('layouts/index', $data);
        }
    }
    public function get_tournamente_info_page(): string
    {
        $jls_database = new DataBaseHandler();
        $jls_tournament_id = $this->request->getPost('tournament_id');
        $jls_participants = $jls_database->jls_get_tournament_participants($jls_tournament_id);
        $data["participants"] = $jls_participants;
        return view("layouts/tournament_info", $data);
    }
}
