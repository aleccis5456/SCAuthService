<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alumno;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Helper\Helper;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->rol != 'admin') {            
            return response()->json(['message' => 'no autorizado'], 401);
        }                
        if (is_array($request->all())) {
            $elementCount = count($request->all());

            if ($elementCount === 6) { 
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string',
                    'cedula' => 'required|numeric',
                    'celular' => 'nullable|numeric',
                    'email' => 'required|string|email|unique:users',
                    'password' => 'required|string|min:6',
                    'rol' => 'required|string'
                ]);
        
                if ($validator->fails()) {
                    return response()->json([
                        'message' => 'error en la validacion',
                        'errors' => $validator->errors(),
                    ], 400);
                }
                if ($request->rol != 'alumno' and $request->rol != 'profesor') {
                    return response()->json(['message' => 'los roles solo puede ser `alumno` o `profesor`']);
                }
                $user = User::create([
                    'name' => $request->name,
                    'cedula' => $request->cedula,
                    'celular' => $request->celular ?? 0,
                    'email' => $request->email,
                    'password' => $request->password,
                    'rol' => $request->rol,
                ]);
                if ($user->rol == 'profesor') {
                    $profesor = Profesor::create(['user_id' => $user->id]);
                }
                if ($user->rol == 'alumno') {
                    $alumno = Alumno::create(['user_id' => $user->id]);
                }
        
                return response()->json([
                    'message' => 'usuario creado',
                    'user' => $user,
                    'rol' => $user->rol,
                ], 201);
            } else {
                return Helper::arrRegister($request);
            }
        }
        return response()->json([
            'message' => 'los datos se cargaron mal'
        ]);        
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error en la validacion',
                'errors' => $validator->errors(),
            ], 400);
        }

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();

        if (!$user and !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'credenciales incorrectos',
            ]);
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'logeado correctamente',
                'user' => $user,
                'token' => $token
            ], 200);
        }
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ], 200);
    }

    public function validarToken(Request $request)
    {
        try {                        
            $user = JWTAuth::parseToken()->authenticate();            
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['message' => 'token invalido, error en la validacion'], 401);
        }
    }
    /**
     * registro y login de admins
     */

    public function registerAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'cedula' => 'required|numeric',
            'celular' => 'nullable|numeric',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error en la validacion',
                'errors' => $validator->errors(),
            ], 400);
        }
        $user = User::create([
            'name' => $request->name,
            'cedula' => $request->cedula,
            'celular' => $request->celular ?? 0,
            'email' => $request->email,
            'password' => $request->password,
            'rol' => 'admin',
        ]);

        if ($user->rol != 'admin') {
            return response()->json(['message' => 'no se pudo completar el registro']);
        }

        return response()->json([
            'message' => 'usuario creado',
            'user' => $user,
            'rol' => $user->rol,
        ], 201);
    }

    public function loginAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error en la validacion',
                'errors' => $validator->errors(),
            ], 400);
        }

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();
        if ($user->rol != 'admin') {
            return response()->json([
                'message' => 'no autorizado'
            ], 401);
        }
        if (!$user and !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'credenciales incorrectos',
            ]);
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'logeado correctamente',
                'user' => $user,
                'token' => $token
            ], 200);
        }
    }

    public function whoIsLoged(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        dd($user);
    }
}
