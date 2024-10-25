<?php

namespace App\Helper;

use App\Models\User;
use App\Models\Profesor;
use App\Models\Alumno;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class Helper
{
    public static function arrRegister(Request $request)
    {
        $users = $request->all();
        $createdUsers = [];
        foreach ($users as $userData) {
            $validator = Validator::make($userData, [
                'name' => 'required|string',
                'cedula' => 'required|numeric',
                'celular' => 'nullable|numeric',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:6',
                'rol' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'error en la validacion del helper',
                    'errors' => $validator->errors(),
                ], 400);
            }
            if ($userData['rol'] != 'alumno' && $userData['rol'] != 'profesor') {
                return response()->json(['message' => 'los roles solo pueden ser `alumno` o `profesor`'], 400);
            }

            $newUser = User::create([
                'name' => $userData['name'],
                'cedula' => $userData['cedula'],
                'celular' => $userData['celular'] ?? 0,
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'rol' => $userData['rol'],
            ]);

            if ($newUser->rol == 'profesor') {
                Profesor::create(['user_id' => $newUser->id]);
            } elseif ($newUser->rol == 'alumno') {
                Alumno::create(['user_id' => $newUser->id]);
            }
            $createdUsers[] = $newUser; 
        }

        return response()->json([
            'message' => 'usuarios creados',
            'users' => $createdUsers
        ], 201);
    }
}
