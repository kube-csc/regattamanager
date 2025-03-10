<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegattaTeamRequest;
use App\Http\Requests\UpdateRegattaTeamRequest;
use App\Models\RaceType;
use App\Models\RegattaTeam;
use App\Models\Event;
use Str;

class RegattaTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $event = $this->getEvent();

        $regattaTeams = RegattaTeam::where('regatta_id', $event->id)->get();

       $regattaTeamCounts = RegattaTeam::where('regatta_id', $event->id)
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

        $raceTypes = RaceType::where('regatta_id', $event->id)->get();

        $num1 = rand(1, 10);
        $num2 = rand(1, 10);

        return view('pages.frontend.meldung', compact('event', 'raceTypes', 'num1', 'num2'));
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

            if($request->mailen){
                $request->mailen='a';
            }
            else{
                $request->mailen='n';
            }

            if($request->einverstaendnis){
                $request->einverstaendnis=1;
            }
            else{
                $request->einverstaendnis=0;
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
                'mailen' => $request->mailen,
                'mailendatum' => now(),
                'datum' => now(),
                'werbung' => $request->werbung,
                'training' => $raceType->training,
                'teamlink' => 0, //ToDo: Teamlink ermitteln
                'passwort' => Str::random(10), //ToDo: Passwort ermitteln
                'status' => 'Neuanmeldung',
                'mannschaftsmail' => 'M',
                'einverstaendnis' => $request->einverstaendnis
            ]);

            $event   = Event::find($request->regatta_id);
            $wertung = RaceType::find($request->gruppe_id);

            // Erstelle eine Instanz des TeamMailController
            $teamMailController = new TeamMailController();
            // Rufe die TeamMeldungMail Methode auf
            $teamMailController->TeamMeldungMail();

            return redirect('/Meldung/Bestaetigung/'.$regattaTeam->id)
                ->with('success', 'Ihr Team '. $regattaTeam->teamname . ' wurde erfolgreich gemeldet.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RegattaTeam $regattaTeam, $raceTeam_id)
    {
        $event = $this->getEvent();

        $regattaTeam = RegattaTeam::find($raceTeam_id);

        return view('pages.frontend.notificationTeam', compact('event', 'regattaTeam'))
              ->with('success', 'Ihr Team '. $regattaTeam->teamname . ' wurde erfolgreich gemeldet.');
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

}
