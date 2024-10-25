<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cedula',
        'celular',
        'rol'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();  // Retorna el identificador único del usuario (normalmente 'id')
    }

    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'ci' => $this->cedula,
            'name' => $this->name,
            'rol' => $this->rol
        ];  // Si no tienes custom claims, regresa un array vacío
    }

    public function alumnos(){
        return $this->hasMany(Alumno::class);
    }
    public function profesores(){
        return $this->hasMany(Profesor::class);
    }

}
