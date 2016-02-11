<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\programa_universidad;
use Illuminate\Support\Facades\DB;
use Psy\Exception\ErrorException;
use App\Universidad;
use App\Programa;
use App\User;

class programa_universidadController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
        $this->middleware('jwt.auth', ['except' => ['index']]);
    }

    private function usuario_activo($id_usuario)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                $this->usuario = $usuario;
                return true;
            }
            else
            {
                return false;
            }
        }
        catch(\Exception $e)
        {
            return false;
        }
    }

    public function index()
    {
        try{
            $programa_universidad = DB::table('programa_universidad')
                ->join('programas', 'programas.id_programa', '=', 'programa_universidad.id_programa')
                ->join('universidades', 'universidades.id_universidad', '=', 'programa_universidad.id_universidad')
                ->where('programa_universidad.estado', '=', "activo")
                ->where('universidades.estado', '=', 'activo')
                ->where('programas.estado', '=', 'activo')
                ->select('programa_universidad.id_universidad', 'universidades.nombre_universidad', 'programa_universidad.id_programa', 'programas.nombre_programa')
                ->get();
            if($programa_universidad)
            {
                return response()->json([
                    'error' => false,
                    'programas_universidades' => $programa_universidad
                ]);
            }
            else{
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El Programa no se encuentra asociado a ninguna Universidad'
                ]);
            }
        }catch (\Exception $e)
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error,',
                'excepcion' => $e
            ]);
        }
    }

    public function get_universidad($id_usuario, $id_programa)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                $programa = Programa::find($id_programa);
                if($programa && $programa->estado == "activo")
                {
                    $programa_universidad = DB::table('programa_universidad')
                        ->join('programas', 'programas.id_programa', '=', 'programa_universidad.id_programa')
                        ->join('universidades', 'universidades.id_universidad', '=', 'programa_universidad.id_universidad')
                        ->where('programa_universidad.id_programa', '=', $id_programa)
                        ->where('programa_universidad.estado', '=', "activo")
                        ->where('universidades.estado', '=', 'activo')
                        ->where('programas.estado', '=', 'activo')
                        ->select('programa_universidad.id_universidad', 'universidades.nombre_universidad', 'programa_universidad.id_programa', 'programas.nombre_programa')
                        ->get();
                    if($programa_universidad)
                    {
                        return response()->json([
                            'error' => false,
                            'programa_universidad' => $programa_universidad
                        ]);
                    }
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'El Programa no se encuentra asociado a ninguna Universidad'
                    ]);
                }
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error, el Programa del cual quiere obtener la universidad no existe'
                ]);
            }
            else
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El usuario que desea hacer la petición no se encuentra Registrado'
                ]);
            }
        }
        catch(\Exception $e)
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al verificar la existencia del Usuario que realiza la peticion',
                'excepcion' => $e
            ]);
        }
    }

    public function show($id_usuario, $id_universidad)
    {
        try
        {
            $usuario = User::find($id_usuario);
            if ($usuario && $usuario->estado == "activo")
            {
                $universidad = Universidad::find($id_universidad);
                if($universidad && $universidad->estado == 'activo')
                {
                    $programa_universidad = DB::table('programa_universidad')
                        ->join('universidades', 'universidades.id_universidad', '=', 'programa_universidad.id_universidad')
                        ->join('programas', 'programas.id_programa', '=', 'programa_universidad.id_programa')
                        ->where('programa_universidad.id_universidad', '=', $id_universidad)
                        ->where('programa_universidad.estado', '=', "activo")
                        ->where('universidades.estado', '=', 'activo')
                        ->where('programas.estado', '=', 'activo')
                        ->select('programa_universidad.id_universidad', 'universidades.nombre_universidad', 'programa_universidad.id_programa', 'programas.nombre_programa')
                        ->get();
                    if($programa_universidad)
                    {
                        return response()->json([
                            'error' => false,
                            'programa_universidad' => $programa_universidad
                        ]);
                    }
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'no hay programas asociados a la universidad'
                    ]);
                }
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error, la Universidad de la cual quiere consultar los programas no existe'
                ]);
            }
            else
            {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'El usuario que desea hacer la petición no se encuentra Registrado'
                ]);
            }
        }
        catch(\Exception $e)
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al verificar la existencia del Usuario que realiza la peticion',
                'excepcion' => $e
            ]);
        }
    }

    public function store(Request $request, $id_usuario)
    {
        if($this->usuario_activo($id_usuario))
        {
            if(!empty($request->get('id_universidad')) && !empty($request->get('id_programa')))
            {
                try{
                    $universidad = Universidad::find($request->get('id_universidad'));
                    if($universidad && $universidad->estado == "activo")
                    {
                        $programa = Programa::find($request->get('id_programa'));
                        if($programa && $programa->estado == "activo")
                        {
                            $programa_universidad = DB::table('programa_universidad')
                                ->insert([
                                    'id_universidad' => $request->get('id_universidad'),
                                    'id_programa' => $request->get('id_programa')
                                ]);
                            if($programa_universidad)
                            {
                                return response()->json([
                                    'error' => false,
                                    'mensaje' => 'datos registrados exitosamente',
                                    'programa_universidad' => $programa_universidad
                                ]);
                            }
                            else{
                                return response()->json([
                                    'error' => true,
                                    'mensaje' => 'Error al registrar mensajes'
                                ]);
                            }
                        }
                        else{
                            return response()->json([
                                'error' => true,
                                'mensaje' => 'Error, el Programa que desea asociar a la universidad no existe'
                            ]);
                        }
                    }
                    else{
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error, la Universidad a la cual quiere asociar el programa no existe'
                        ]);
                    }
                }
                catch(\Exception $e)
                {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error, ya existe registro con esos datos',
                        'exception' => $e
                    ]);
                }
            }
        }
        else
        {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al verificar la existencia del usuario que hace la peticion'
            ]);
        }
    }

    public function cambiar_estado_programa_inactivo($id_programa)
    {
        $programa_universidad = DB::table('programa_universidad')
            ->where('id_programa', '=', $id_programa)
            ->update(['estado' => 'inactivo']);
        if($programa_universidad)
        {
            return true;
        }
        return false;
    }

    public function cambiar_estado_universidad_inactiva($id_universidad)
    {
        $programa_universidad = DB::table('programa_universidad')
            ->where('id_universidad', '=', $id_universidad)
            ->update(['estado' => 'inactivo']);
        if($programa_universidad)
        {
            return true;
        }
        return false;
    }

}
