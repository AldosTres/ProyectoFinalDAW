<?php

namespace App\Controllers;

use App\models\DataBaseHandler;

class Home extends BaseController
{
    public function index(): string
    {
        return view('index');
    }

    public function login(): string
    {
        return view('login');
    }

    public function check_login(): string
    {
        $jls_database = new DataBaseHandler();
        $user = $this->request->getPost('jls_username');
        $password = $this->request->getPost('jls_user_password');
        $result = $jls_database->jls_check_user($user, $password);
        if ($result == 0) {
            return view('login');
        } else {
            //Devolviendo los datos correspondientes al user
            $user_data = $jls_database-> jls_get_user_data($result);

            //Devolviendo artículos y categorías de toda la página
            // $articulosTienda = $jls_database->devolverTodosArticulos();
            // $maleta["user"] = $datosUsuario;
            // $maleta["articulos"] = $articulosTienda;
            // $categoriasTienda = $jls_database->devolverCategorias();
            // $maleta["categorias"] = $categoriasTienda;

            //Guardando en sesion el nombre del user logeado
            session()->set("nombreCompletoUsuario", $user_data-> nombre_usuario);
            session()->set("codigoUsuario", $result);

            return view('userIndex');
        }
    }
}
