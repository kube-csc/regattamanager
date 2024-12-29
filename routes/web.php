<?php

use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/',                         [HomeController::class, 'index'])->name('pages.frontend.home');
Route::get('/Impressum', 'App\Http\Controllers\frontend\ImpressumController@getImpressumDaten');
Route::get('/Information/Datenschutzerklaerung', 'App\Http\Controllers\frontend\DatenschutzerklärungController@getDatenschutzerklärungDaten');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
