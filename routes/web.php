<?php

use App\Http\Controllers\Backend\TeamMailController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Backend\RegattaTeamController;
use App\Http\Controllers\Frontend\ImpressumController;
use App\Http\Controllers\Frontend\DatenschutzerklaerungController;
use App\Http\Controllers\Api\APIController;
use Illuminate\Support\Facades\Route;


Route::get('/',                                   [HomeController::class, 'index'])->name('pages.frontend.home');
Route::get('/Impressum',                          [ImpressumController::class, 'getImpressumDaten']);
Route::get('/Information/Datenschutzerklaerung',  [DatenschutzerklaerungController::class, 'getDatenschutzerklaerungDaten']);
Route::get('/Anfahrt',                            [HomeController::class, 'journey']);
Route::get('/Ausschreibung',                      [HomeController::class, 'information']);

Route::get('/API/Gemeldeteteams',                   [APIController::class, 'APIStartliste'])->name('api.regattaTeams.APIStartliste');
Route::get('/API/Sprecherkarten',                   [APIController::class, 'APISprecherkarte']);

if (env('API_TEAMDATEN') === 'ja') {
    Route::get('/API/Teamdaten', [APIController::class, 'APITeamliste'])->name('api.regattaTeams.APITeamliste');
}

Route::get('/Teamemailmeldebestaetigung',         [TeamMailController::class, 'TeamMeldungMail'])->name('RegattaTeam.TeamMeldungMail');

Route::get('/Regattateams',                       [RegattaTeamController::class, 'index'])->name('RegattaTeam.index');
Route::get('/Meldung',                            [RegattaTeamController::class, 'create'])->name('RegattaTeam.create');
Route::post('/Meldung/eintragen',                 [RegattaTeamController::class, 'store'])->name('RegattaTeam.store');
Route::get('/Meldung/Bestaetigung/{raceTeam_id}', [RegattaTeamController::class, 'show'])->name('RegattaTeam.show')
    ->middleware('check.meldung.session');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
