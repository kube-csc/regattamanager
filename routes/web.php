<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Backend\RegattaTeamController;
use Illuminate\Support\Facades\Route;


Route::get('/',                         [HomeController::class, 'index'])->name('pages.frontend.home');
Route::get('/Impressum', 'App\Http\Controllers\frontend\ImpressumController@getImpressumDaten');
Route::get('/Information/Datenschutzerklaerung', 'App\Http\Controllers\frontend\DatenschutzerklärungController@getDatenschutzerklärungDaten');

Route::get('/Meldung',              [RegattaTeamController::class, 'create'])->name('RegattaTeam.create');
Route::post('/Meldung/eintragen',   [RegattaTeamController::class, 'store'])->name('RegattaTeam.store');
Route::get('/Meldung/Bestaetigung', [RegattaTeamController::class, 'notificationTeam'])->name('RegattaTeam.notificationTeam');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
