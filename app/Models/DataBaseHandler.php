<?php

namespace App\models;

use CodeIgniter\Model;

class DataBaseHandler extends Model
{
    /**
     * Pre: 
     * Post: 
     */
    function jls_check_user($user, $password)
    {
        $result = $this->db->query("SELECT * FROM usuarios WHERE nombre_usuario_inicio = '{$user}' AND contraseña = '{$password}'");
        $user = $result->getRow();
        return isset($user) ? $user->id : 0;
    }

    /**
     * Pre:
     * Post: función que comprueba si un nombre de user ya existe, devuelve el código del user
     * si existe, y un 0 si no existe
     */
    function jls_check_user_name($user)
    {
        $result = $this->db->query("SELECT * FROM usuarios WHERE nombre_usuario_inicio = '{$user}'");
        $user = $result->getRow();
        if (isset($user)) {
            return $user->id;
        } else {
            return 0;
        }
    }

    /**
     * Pre:
     * Post:
     */
    function registrarUsuario($nombre, $user, $password)
    {
        $existe = $this->existeNombreUsuario($user);
        if ($existe == 0) {
            $this->db->query("INSERT INTO usuarios(`nombre_usuario`, `nombre_usuario_inicio`, `contraseña`) VALUES ('{$nombre}', '{$user}' , '{$password}')");
        }
    }

    /**
     * 
     */
    function jls_get_user_data($user_id)
    {
        $result = $this->db->query("SELECT * FROM usuarios WHERE id = {$user_id}");
        $row = $result->getRow();
        return $row;
    }
}
