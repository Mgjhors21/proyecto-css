<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\userapi;

Route::get('/users', [userapi::class, 'index']);

Route::post('/users', [userapi::class, 'store']);
