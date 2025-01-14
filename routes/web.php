<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Backend\RegattaTeamController;
use App\Http\Controllers\Frontend\ImpressumController;
use App\Http\Controllers\Frontend\DatenschutzerklaerungController;
use Illuminate\Support\Facades\Route;


Route::get('/',                                  [HomeController::class, 'index'])->name('pages.frontend.home');
Route::get('/Impressum',                         [ImpressumController::class, 'getImpressumDaten']);
Route::get('/Information/Datenschutzerklaerung', [DatenschutzerklaerungController::class, 'getDatenschutzerklaerungDaten']);
Route::get('/Anfahrt',                           [HomeController::class, 'journey']);

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
