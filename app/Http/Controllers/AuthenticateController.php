<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\User;

class AuthenticateController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }


    public function index()
    {
        $users = User::all();
        if($users)
        {
            return response()->json(['usuarios' => $users]);
        }
    }


    public function authenticate(Request $request)
    {
        $credenciales = $request->only('email','password');
        /*$usuario = DB::table('usuarios')
            ->where('email', '=', $credenciales['email'])
            ->get();
        if($usuario) {*/
            try {
                if (! $token = JWTAuth::attempt(['email' => $credenciales['email'], 'password' => $credenciales['password']])) {
                    return response()->json(
                        ['error' => 'Credenciales Invalidas'], 401
                    );
                }
            } catch (JWTException $e) {
                return response()->json
                (['error' => 'Falta Token'], 500);
            }
        //}
            return response()->json(compact('token'));
            /*$token = JWTAuth::fromUser($usuario);
            return response()->json($token);*/
        //}
    }
}
