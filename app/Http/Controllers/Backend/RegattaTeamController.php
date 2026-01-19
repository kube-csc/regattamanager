<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreRegattaTeamRequest;
use App\Http\Requests\UpdateRegattaTeamRequest;
use App\Models\RaceType;
use App\Models\RegattaTeam;
use App\Models\Event;
use Str;

/**
 * Feld 'status' - Status der Team-Meldung.
 * Mögliche Werte:
 * - Neumeldung: Aktiv gemeldetes Team
 * - Warteliste: Team steht auf der Warteliste
 * - Nicht angetreten: Team ist nicht angetreten
 * - Disqualifiziert: Team wurde disqualifiziert
 * - Ausgeschieden: Team ist ausgeschieden
 * - Gelöscht: Team wurde gelöscht (nicht mehr sichtbar)
 */
class RegattaTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $event = $this->getEvent();

        if (!$event || !$event->id) {
            return view('pages.frontend.noEvent', compact('event'));
        }

        $regattaTeams = RegattaTeam::where('regatta_id', $event->id)
           ->where('status', '!=', 'Gelöscht')
           ->orderBy('datum')
           ->get();

        $regattaTeamCounts = RegattaTeam::where('regatta_id', $event->id)
            ->where('status', '!=', 'Gelöscht')
            ->select('gruppe_id', \DB::raw('count(*) as total'))
           ->groupBy('gruppe_id')
           ->get();

        return view('pages.frontend.regattaTeams', compact('event','regattaTeams', 'regattaTeamCounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $event = $this->getEvent();

        // Wenn kein Event vorhanden ist, zeige die No-Event-Seite
        if (!$event || !$event->id) {
            return view('pages.frontend.noEvent', compact('event'));
        }

        // Prüfe VOR dem Laden/Anzeigen des Formulars, ob die Meldung geöffnet ist
        $now = \Carbon\Carbon::now();
        $start = $event->datumvona ? \Carbon\Carbon::parse($event->datumvona)->startOfDay() : null;
        $end = $event->datumbisa ? \Carbon\Carbon::parse($event->datumbisa)->endOfDay() : null;

        if ($start && $end && ! $now->between($start, $end)) {
            return view('pages.frontend.meldungClosed', ['message' => 'Die Meldung ist außerhalb des erlaubten Meldezeitraums.']);
        }
        if ($start && ! $end && $now->lt($start)) {
            return view('pages.frontend.meldungClosed', ['message' => 'Die Meldung ist noch nicht geöffnet.']);
        }
        if (! $start && $end && $now->gt($end)) {
            return view('pages.frontend.meldungClosed', ['message' => 'Die Meldung ist bereits geschlossen.']);
        }

        // Daten für die Meldungsseite
        $raceTypes = RaceType::where('regatta_id', $event->id)->get();

        $regattaTeamCount = RegattaTeam::where('regatta_id', $event->id)
            ->where('status', '!=', 'Gelöscht')
            ->count();

        $num1 = rand(1, 10);
        $num2 = rand(1, 10);

        return view('pages.frontend.meldung', compact('event', 'raceTypes', 'num1', 'num2', 'regattaTeamCount'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegattaTeamRequest $request)
    {
        $validated = $request->validated();

        $num1 = $request->input('num1');
        $num2 = $request->input('num2');
        $captcha = $request->input('captcha');

        if ($captcha != ($num1 + $num2)) {
            return back()->with(['notification' => 'Keine korrekte Kontrollzahl.']);
        }

        $raceType = RaceType::find($request->gruppe_id);

        // Kein dynamisches Setzen am Request (PHP 8.2+), stattdessen lokale Variablen
        $mailen = ($request->has('mailen') && $request->mailen) ? 'a' : 'n';
        $einverstaendnis = ($request->has('einverstaendnis') && $request->einverstaendnis) ? 1 : 0;

        $event = $this->getEvent();

        if (!$event || !$event->id) {
            return view('pages.frontend.noEvent', compact('event'));
        }

        // Defensive Prüfung: auch beim POST die Meldezeit prüfen
        $now = \Carbon\Carbon::now();
        $start = $event->datumvona ? \Carbon\Carbon::parse($event->datumvona)->startOfDay() : null;
        $end = $event->datumbisa ? \Carbon\Carbon::parse($event->datumbisa)->endOfDay() : null;

        if ($start && $end && ! $now->between($start, $end)) {
            return back()->with(['notification' => 'Die Meldung ist außerhalb des erlaubten Meldezeitraums.']);
        }
        if ($start && ! $end && $now->lt($start)) {
            return back()->with(['notification' => 'Die Meldung ist noch nicht geöffnet.']);
        }
        if (! $start && $end && $now->gt($end)) {
            return back()->with(['notification' => 'Die Meldung ist bereits geschlossen.']);
        }

        $regattaTeamCount = RegattaTeam::where('regatta_id', $event->id)
            ->where('status', '!=', 'Gelöscht')
            ->count();

        $status = "Neuanmeldung";
        $successText ='Ihr Team '. $request->teamname . ' wurde erfolgreich gemeldet.';
        if($event->teilnehmer < $regattaTeamCount && $event->teilnehmermax == '2'){
          $status = "Warteliste";
          $successText ='Ihr Team '. $request->teamname . ' wurde auf der Warteliste gesetzte.';
        }

        $request->session()->put('meldung_done', true);

        $regattaTeam = RegattaTeam::create([
            'regatta_id' => $request->regatta_id,
            'gruppe_id' => $request->gruppe_id,
            'teamname' => $request->teamname,
            'verein' => $request->verein,
            'teamcaptain' => $request->teamcaptain,
            'strasse' => $request->strasse,
            'plz' => $request->plz,
            'ort' => $request->ort,
            'email' => $request->email,
            'telefon' => $request->telefon,
            'homepage' => $request->homepage,
            'beschreibung' => $request->beschreibung,
            'kommentar' => $request->kommentar,
            'mailen' => $mailen,
            'mailendatum' => now(),
            'datum' => now(),
            'werbung' => $request->werbung,
            'training' => $raceType ? $raceType->training : null,
            'teamlink' => 0, //ToDo: Teamlink ermitteln
            'passwort' => Str::random(10), //ToDo: Passwort ermitteln
            'status' => $status,
            'mannschaftsmail' => 'M',
            'einverstaendnis' => $einverstaendnis
        ]);

        if($request->Teamfoto) {
           $extension = $request->Teamfoto->extension();
           $newPictureName = $this->saveInmage($request->Teamfoto, $regattaTeam->id, $extension);
           $regattaTeam->update(['bild' => $newPictureName]);
        }

        // Erstelle eine Instanz des TeamMailController
        $teamMailController = new TeamMailController();
        // Rufe die TeamMeldungMail Methode auf
        $teamMailController->TeamMeldungMail();

        return redirect('/Meldung/Bestaetigung/'.$regattaTeam->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(RegattaTeam $regattaTeam, $raceTeam_id)
    {
        $event = $this->getEvent();

        if (!$event || !$event->id) {
            return view('pages.frontend.noEvent', compact('event'));
        }

        $regattaTeam = RegattaTeam::find($raceTeam_id);
        $regattaTeamCount = RegattaTeam::where('regatta_id', $event->id)
            ->where('status', '!=', 'Gelöscht')
            ->count();

        if($event->teilnehmer < $regattaTeamCount && $event->teilnehmermax == '2'){
            $successText ='Ihr Team '. $regattaTeam->teamname . ' wurde auf der Warteliste gesetzte.';
        }
        else{
            $successText ='Ihr Team '. $regattaTeam->teamname . ' wurde erfolgreich gemeldet.';
        }

        return view('pages.frontend.notificationTeam', compact('event', 'regattaTeam'))
              ->with('success', $successText);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RegattaTeam $regattaTeam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRegattaTeamRequest $request, RegattaTeam $regattaTeam)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegattaTeam $regattaTeam)
    {
        //
    }

    public function saveInmage($imageInput , $regattaTeam_id , $extension){

        $newPictureName="teamImage" . $regattaTeam_id . "_" . \Illuminate\Support\Str::random(4) . "." . $extension;
        Storage::disk('public')->putFileAs(
            'teamImage/',
            $imageInput,
            $newPictureName
        );

        return  $newPictureName;
    }
}
