<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function registro(Request $request)
    {
        //Recoger el post
        $json = $request->input('json', null);
        $params = json_decode($json); //Permite convertir a formato de objeto de php lo que llegue
        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;

        $role = 'ROLE_USER';
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        if (!is_null($email) && !is_null($password) && !is_null($name)) {
            //Crear el usuario
            $user = new User();
            $user->email = $email;
            $user->name = $name;
            $pwd = hash('sha256', $password);
            $user->password = $pwd;
            //Comprobar usuario duplicado
            $isset_user = User::where('email', '=', $email)->get();
            if (count($isset_user) == 0) {
                //Guardar el usuario
                $user->save();
                $data = array(
                    'status' => 'success',
                    'codigo' => 200,
                    'message' => 'Usuario Registrado correctamente'
                );

            } else {
                //No guardarlo
                $data = array(
                    'status' => 'error',
                    'codigo' => 400,
                    'message' => 'Usuario Duplicado, no puede registrarse'
                );
            }
            return response()->json($data, 200);

        } else {
            $data = array(
                'status' => 'error',
                'codigo' => 400,
                'message' => 'Usuario no Creado'
            );
            return response()->json($data, 200);
        }
    }//Fin del método register

    //echo "Acción Registro"; die();

    public function login(Request $request)
    {
        $jwtAuth = new JwtAuth();
        //Recibir POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $getToken = (!is_null($json) && isset($params->getToken)) ? $params->getToken : null;
        //Cifrar el password
        $pwd = hash('sha256', $password);
        if (!is_null($email) && !is_null($password) && ($getToken == null || $getToken == false)) {
            $signup = $jwtAuth->signup($email, $pwd);
        } elseif ($getToken != null) {
            $signup = $jwtAuth->signup($email, $pwd, $getToken);
        } else {
            $signup = array(
                'status' => 'error',
                'message' => 'Envía tus datos por post',
            );
        }
        return response()->json($signup, 200);
    }
}
