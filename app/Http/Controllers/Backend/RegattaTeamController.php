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
 * Es soll dei Warteliste Aktiviert werden <span>events->teilnehmer. Der Modus ist unter</span>events->teilnehmermax angeben. Wenn eine Meldung gemacht wird soll geprüft werden ob das team auf die Warteliste Kommt. Diese soll in regatta_teams->status angeben werden. Ein hinweis soll in der Mailbestätigung augegeben werden. Bei Teilnehmermax = keien Warteliste;  1 = maximale Teilnehmerzahl keine Meldung möglich keine Warteliste;  2 = maximale Teilnehmerzahl mit Warteliste;   3 = maximale Teilnehmerzahl mit Warteliste aber automatischer Bahnauffühlung Es konnen immer die Rennen aufgefüllt wird. Also ein vielfachen $race_types->bahnen.
 *
 */


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
     *
     * Prüft für jede Wertung/Klasse (RaceType), ob sie voll ist und fügt entsprechende
     * Status-Informationen hinzu, die im Frontend angezeigt werden:
     * - Modus 0: Unbegrenzte Plätze
     * - Modus 1: Ausgebuchte Klassen werden ausgeblendet (nicht auswählbar)
     * - Modus 2: Volle Klassen zeigen "(Meldung nur auf Warteliste)"
     * - Modus 3: Bahnauffüllung - volle Blöcke zeigen "(Meldung nur auf Warteliste)"
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

        // Prüfe für jede Wertung/Klasse, ob sie voll ist (Warteliste-Status)
        $teilnehmerLimit = (int) ($event->teilnehmer ?? 0);
        $modus = (int) ($event->teilnehmermax ?? 0);

        $raceTypeStatus = [];

        foreach ($raceTypes as $raceType) {
            $isWaitingList = false;
            $statusText = '';
            $isDisabled = false; // Für Modus 1: Option wird ausgeblendet

            // Modus 0: unbegrenzt - nie Warteliste
            if ($modus === 0) {
                $isWaitingList = false;
            }
            // Modus 1: hartes Limit ohne Warteliste - blockiert
            elseif ($modus === 1) {
                $activeCount = RegattaTeam::where('regatta_id', $event->id)
                    ->where('status', '!=', 'Gelöscht')
                    ->where('status', '!=', 'Warteliste')
                    ->count();

                if ($teilnehmerLimit > 0 && $activeCount >= $teilnehmerLimit) {
                    $isDisabled = true; // Option wird ausgeblendet
                    $statusText = ' (Ausgebucht)';
                }
            }
            // Modus 2: mit Warteliste
            elseif ($modus === 2) {
                $activeCount = RegattaTeam::where('regatta_id', $event->id)
                    ->where('status', '!=', 'Gelöscht')
                    ->where('status', '!=', 'Warteliste')
                    ->count();

                if ($teilnehmerLimit > 0 && $activeCount >= $teilnehmerLimit) {
                    $isWaitingList = true;
                    $statusText = ' (Meldung nur auf Warteliste)';
                } elseif ($teilnehmerLimit === 0) {
                    $isWaitingList = true;
                    $statusText = ' (Meldung nur auf Warteliste)';
                }
            }
            // Modus 3: mit Warteliste und Bahnauffüllung pro Wertung
            elseif ($modus === 3) {
                $activeCount = RegattaTeam::where('regatta_id', $event->id)
                    ->where('gruppe_id', $raceType->id)
                    ->where('status', '!=', 'Gelöscht')
                    ->where('status', '!=', 'Warteliste')
                    ->count();

                $bahnen = (int) ($raceType->bahnen ?? 0);

                if ($bahnen <= 0) {
                    // Fallback wie Modus 2
                    if ($teilnehmerLimit === 0 || ($teilnehmerLimit > 0 && $activeCount >= $teilnehmerLimit)) {
                        $isWaitingList = true;
                        $statusText = ' (Meldung nur auf Warteliste)';
                    }
                } else {
                    // Wenn aktive Teams pro Klasse >= bahnen, dann Warteliste
                    // Beispiel: bahnen=4, activeCount=4 => Warteliste
                    if ($activeCount >= $bahnen) {
                        $isWaitingList = true;
                        $statusText = ' (Meldung nur auf Warteliste)';
                    }
                }
            }

            $raceTypeStatus[$raceType->id] = [
                'isWaitingList' => $isWaitingList,
                'statusText' => $statusText,
                'isDisabled' => $isDisabled
            ];
        }

        // Hinweis: Alle weiteren Meldungen gehen auf die Warteliste
        $allWaitlist = false;
        $isEventFullyBooked = false;

        if ($modus === 1) {
            // Modus 1: Wenn Event voll ist
            $activeCount = RegattaTeam::where('regatta_id', $event->id)
                ->where('status', '!=', 'Gelöscht')
                ->where('status', '!=', 'Warteliste')
                ->count();
            $isEventFullyBooked = ($teilnehmerLimit > 0 && $activeCount >= $teilnehmerLimit);
        } elseif ($modus === 2) {
            $activeCount = RegattaTeam::where('regatta_id', $event->id)
                ->where('status', '!=', 'Gelöscht')
                ->where('status', '!=', 'Warteliste')
                ->count();
            $allWaitlist = ($teilnehmerLimit === 0) || ($teilnehmerLimit > 0 && $activeCount >= $teilnehmerLimit);
        } elseif ($modus === 3) {
            $allWaitlist = !empty($raceTypeStatus);
            foreach ($raceTypeStatus as $status) {
                if (empty($status['isWaitingList'])) {
                    $allWaitlist = false;
                    break;
                }
            }
        }

        $num1 = rand(1, 10);
        $num2 = rand(1, 10);

        return view('pages.frontend.meldung', compact('event', 'raceTypes', 'num1', 'num2', 'regattaTeamCount', 'raceTypeStatus', 'allWaitlist', 'isEventFullyBooked', 'modus'));
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

        $teilnehmerLimit = (int) ($event->teilnehmer ?? 0);
        $modus = (int) ($event->teilnehmermax ?? 0);

        // Für die Kapazität zählen nur "aktive" Teams (Warteliste zählt nicht gegen das Limit)
        $activeCountQuery = RegattaTeam::where('regatta_id', $event->id)
            ->where('status', '!=', 'Gelöscht')
            ->where('status', '!=', 'Warteliste');

        // Modus 3: Kapazität in Bahn-Blöcken pro Wertung/Klasse (gruppe_id)
        if ($modus === 3) {
            $activeCountQuery->where('gruppe_id', $request->gruppe_id);
        }

        $activeCount = (int) $activeCountQuery->count();

        $status = 'Neuanmeldung';

        // Entscheidungen gemäß Modus
        if ($modus === 0) {
            // unbegrenzt
            $status = 'Neuanmeldung';
        } elseif ($modus === 1) {
            // hartes Limit, keine Warteliste
            if ($teilnehmerLimit > 0 && $activeCount >= $teilnehmerLimit) {
                return back()->with(['notification' => 'Dieses Event ist leider ausgebucht. Eine Meldung ist nicht mehr möglich.']);
            }
            if ($teilnehmerLimit === 0) {
                // Limit 0 in Modus 1 bedeutet effektiv: keine Plätze
                return back()->with(['notification' => 'Für dieses Event sind aktuell keine Plätze verfügbar.']);
            }
        } elseif ($modus === 2) {
            // Limit + Warteliste
            if ($teilnehmerLimit > 0 && $activeCount >= $teilnehmerLimit) {
                $status = 'Warteliste';
            }
            if ($teilnehmerLimit === 0) {
                // Limit 0 => direkt Warteliste
                $status = 'Warteliste';
            }
        } elseif ($modus === 3) {
            // Limit + Warteliste + automatische Bahnauffüllung (pro Gruppe/Klasse)
            if (! $raceType) {
                // Ohne RaceType keine Bahnlogik möglich -> sichere Variante: Warteliste
                $status = 'Warteliste';
            } else {
                $bahnen = (int) ($raceType->bahnen ?? 0);

                if ($bahnen <= 0) {
                    // Fallback wie Modus 2
                    if ($teilnehmerLimit === 0 || ($teilnehmerLimit > 0 && $activeCount >= $teilnehmerLimit)) {
                        $status = 'Warteliste';
                    }
                } else {
                    // Wenn aktive Teams pro Klasse >= bahnen, dann Warteliste
                    if ($activeCount >= $bahnen) {
                        $status = 'Warteliste';
                    }
                }
            }
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

        if (! $regattaTeam) {
            return view('pages.frontend.noEvent', compact('event'));
        }

        if ($regattaTeam->status === 'Warteliste') {
            $successText = 'Ihr Team ' . $regattaTeam->teamname . ' wurde auf die Warteliste gesetzt.';
        } else {
            $successText = 'Ihr Team ' . $regattaTeam->teamname . ' wurde erfolgreich gemeldet.';
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
