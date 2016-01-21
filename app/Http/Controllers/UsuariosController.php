<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UsuariosController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    public function index()
    {
        $usuarios = User::all();
        if($usuarios->count() != 0)
        {
            return response()->json([
                'error'   => false,
                'usuarios' => $usuarios
            ]);
        }

        return response()->json([
            'error' => true,
            'mensaje' => 'No hay Usuarios registrados'
        ]);
    }

    public function store(Request $request)
    {
        $data = Input::all();
        $usuario = new User();
        $usuario->nombre = $data['nombre'];
        $usuario->apellido = $data['apellido'];
        $usuario->email = $data['email'];
        $usuario->password = Hash::make($data['password']);
        $usuario->save();
        //$usuario = User::create($request->all());
        if($usuario)
        {
            return response()->json(
                [
                    'error'   => false,
                    'mensaje' => 'Usuario Almacenado Exitosamente',
                    'usuario' => $usuario
                ]
            );
        }
        return response()->json([
            'error' => false,
            'mensaje' => 'Error al registrar Usuario'
        ]);
    }

    public function show($id)
    {
        $usuario = User::find($id);
        if($usuario)
        {
            return response()->json([
                'error' => false,
                'usuario' => $usuario
            ]);
        }
        return response()->json([
            'error'   => true,
            'mensaje' => 'Usuario no registrado'
        ]);
    }

    public function update(Request $request, $id)
    {
        $usuario = User::find($id);
        $data = Input::all();
        //return response()->json(['user' => $usuario]);
        if($usuario)
        {
            $usuario->nombre = $request->get('nombre');
            $usuario->apellido = $request->get('apellido');
            $usuario->email = $request->get('email');
            $usuario->password = Hash::make($request->get('password'));
            if($usuario->save())
            {
                return response()->json([
                    'error' => false,
                    'mensaje' => 'Datos Actualizados Exitosamente',
                    'usuario' => $usuario
                ]);
            }
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al actualizar los datos'
            ]);
        }
        return response()->json([
            'error' => true,
            'mensaje' => 'Error, usuario no registrado'
        ]);
    }
}
