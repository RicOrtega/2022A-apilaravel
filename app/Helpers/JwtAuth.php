<?php

namespace App\Helpers;

use App\Models\User;
use Firebase\JWT\JWT;

class JwtAuth
{
    public $key;

    public function __construct()
    {
        $this->key = 'esta-es-mi-clave-secreta-*2626';
    }

    public function signup($email, $password, $getToken = null)
    {
        //Verifica si el usuario existe
        $user = User::where(
            array(
                'email' => $email,
                'password' => $password
            ))->first();
        $signup = false;
        if (is_object($user)) {
            $signup = true;
        }
        if ($signup) {
            //Generar el token
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            );
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            if (is_null($getToken)) {
                return $jwt;
            } else {
                return $decoded;
            }
        } else {
            //Devolver un error
            return array('status' => 'error', 'message' => 'Login ha fallado');
        }
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        //Comprueba que si es valido y si es true devuelve la identidad del usuario
        $auth = false;
        try {
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));

        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }
        if (isset($decoded) && is_object($decoded) && isset($decoded->sub)) {

            $auth = true;
        } else {
            $auth = false;
        }
        if ($getIdentity) {
            return $decoded;
        }
        return $auth;
    }
}
