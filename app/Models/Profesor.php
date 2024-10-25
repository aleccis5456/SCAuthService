<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    protected $fillable = ['user_id'];

    public $table = 'profesores';

    public function user(){
        return $this->belongsTo(User::class);
    }
}
