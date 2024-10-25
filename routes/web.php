<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

Route::get('/redistest', function () {
    //Guarda un valor en Redis
    Redis::set('mykey', 'Hello, Redis from Laravel');

    //Recupera el valor almacenado en Redis
    $value = Redis::get('mykey');

    //Devuelve el valor como respuesta
    return $value;
});

Route::get('/', function(){
    return view('welcome');
});