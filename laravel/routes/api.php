<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthJWT;

Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);

Route::middleware([AuthJWT::class])->group(function(){
    Route::prefix('epresence')->group(function(){
        Route::post('/insert', [App\Http\Controllers\EpresenceController::class, 'insert']);
        Route::post('/approve/{id}', [App\Http\Controllers\EpresenceController::class, 'approve']);
        Route::get('/getData', [App\Http\Controllers\EpresenceController::class, 'getData']);
    });
});