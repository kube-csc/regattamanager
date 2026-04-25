<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lane;
use App\Models\RegattaTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class APIController extends Controller
{
    /**
     * Ersetzt unerwünschte Zeichen und gibt bei fehlendem Wert einen Leerstring zurück.
     */
    private function cleanCsvField($value) {
        // Wenn kein Wert vorhanden ist, gib einen Leerstring zurück
        if (empty($value) && $value !== "0") {
            return " ";
        }
        return str_replace([ "<br>", "</p>","<p>"], [ "\n", "\n",""], $value);
    }

    /**
     * Prüft, ob der übergebene Sicherheitscode gültig ist und (falls gesetzt) nicht abgelaufen ist.
     *
     * Annahme: `mitgliederSicherheitscodeEnddatum = null` bedeutet "läuft nicht ab".
     */
    private function isTeamdatenAccessAllowed($event, string $code): bool
    {
        $storedCode = (string) ($event->mitgliederSicherheitscode ?? '');
        if ($storedCode === '' || !hash_equals($storedCode, (string) $code)) {
            return false;
        }

        $end = $event->mitgliederSicherheitscodeEnddatum ?? null;
        if (empty($end)) {
            return true;
        }

        try {
            // Unterstützt Carbon/DateTime, Strings und Timestamps
            $endAt = $end instanceof \DateTimeInterface ? Carbon::instance($end) : Carbon::parse($end);
        } catch (\Throwable $e) {
            // Wenn das Datum kaputt/ungültig ist, sperren wir lieber.
            return false;
        }

        return now()->lte($endAt);
    }

    public function APIStartliste()
    {
        $event = $this->getEvent();
        abort_unless($event && $event->id != null, 404);

        if ($event->id != null) {
            $regattateams = RegattaTeam::where('regatta_id', $event->id)
                ->where('status', '!=', 'gelöscht')
                ->orderBy('datum')
                ->get()
                ->values()
                ->each(function ($item, $key) {
                    $item->laufende_nummer = $key + 1;
                });

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="mannschaftengemeldet.csv"',
            ];

            $callback = function() use ($regattateams) {

                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                foreach ($regattateams as $key => $team) {
                    $row = [
                        $this->cleanCsvField($team->laufende_nummer),
                        $this->cleanCsvField($team->teamname),
                        $this->cleanCsvField($team->verein),
                        $this->cleanCsvField($team->getRaceType->typ),
                    ];
                    fputcsv($file, $row, ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }

    public function APITeamliste( $code )
    {
        $event = $this->getEvent();
        abort_unless($event && $event->id != null, 404);

        if (!$this->isTeamdatenAccessAllowed($event, (string) $code)) {
            abort(403, 'Bearbeitung nicht erlaubt.');
        }

        if ($event->id != null) {
            $regattateams = RegattaTeam::where('regatta_id', $event->id)
                ->where('status', '!=', 'gelöscht')
                ->orderBy('datum')
                ->get()
                ->values()
                ->each(function ($item, $key) {
                    $item->laufende_nummer = $key + 1;
                });

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="Meldedaten.csv"',
            ];

            $callback = function() use ($regattateams) {

                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                fputcsv($file, ['Nr.', 'Teamname', 'Verein', 'Straße', 'PLZ', 'Ort', 'Telefon', 'E-Mail', 'Training', 'Renn-Typ'], ';');
                foreach ($regattateams as $key => $team) {
                    $row = [
                        $this->cleanCsvField($team->laufende_nummer),
                        $this->cleanCsvField($team->teamname),
                        $this->cleanCsvField($team->verein),
                        $this->cleanCsvField($team->strasse),
                        $this->cleanCsvField($team->plz),
                        $this->cleanCsvField($team->ort),
                        $this->cleanCsvField($team->telefon),
                        $this->cleanCsvField($team->email),
                        $this->cleanCsvField($team->training),
                        $this->cleanCsvField($team->getRaceType->typ),
                    ];
                    fputcsv($file, $row, ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }

    public function APISprecherkarte()
    {
        $event = $this->getEvent();
        abort_unless($event && $event->id != null, 404);

        if ($event->id != null) {
            $regattateams = RegattaTeam::with(['getRaceType'])
                ->where('regatta_id', $event->id)
                ->where('status', '!=', 'gelöscht')
                ->orderBy('datum')
                ->get()
                ->values()
                ->each(function ($item, $key) {
                    $item->laufende_nummer = $key + 1;
                });

            // Teilnahme-/Erfolgs-Statistik (gebündelt für alle Teams, um N+1-Queries zu vermeiden)
            $teamLinks = $regattateams
                ->pluck('teamlink')
                ->filter(fn ($teamlink) => (int) $teamlink > 0)
                ->unique()
                ->values();

            $participationCountByTeamlink = collect();
            $lastResultsTextByTeamlink = collect();

            if ($teamLinks->isNotEmpty()) {
                $participationBaseQuery = RegattaTeam::join('events', 'regatta_teams.regatta_id', '=', 'events.id')
                    ->whereIn('regatta_teams.teamlink', $teamLinks)
                    ->where('regatta_teams.status', 'Neuanmeldung')
                    // Vergangene Teilnahmen zählen wir über das Veranstaltungs-Enddatum (nicht Anmeldezeitraum).
                    ->where('events.datumbis', '<', now()->format('Y-m-d'))
                    // Aktuelle Regatta (aktuelles Event) soll bei "Teilnahmen"/"Erfolge" nicht mitgezählt werden.
                    ->where('events.id', '!=', (int) $event->id);

                // Mapping: team_id => teamlink (für spätere Zuordnung der Ergebnisse)
                $teamIdToTeamlink = (clone $participationBaseQuery)
                    ->select('regatta_teams.id as team_id', 'regatta_teams.teamlink')
                    ->get()
                    ->mapWithKeys(fn ($row) => [(int) $row->team_id => (int) $row->teamlink]);

                $participationCountByTeamlink = $teamIdToTeamlink
                    ->values()
                    ->countBy();

                $teamIds = $teamIdToTeamlink->keys();

                if ($teamIds->isNotEmpty()) {
                    $lanes = Lane::whereIn('mannschaft_id', $teamIds)
                        ->whereHas('race', function ($q) use ($event) {
                            $q->where('status', 4)
                                ->where('visible', 1)
                                // Aktuelle Regatta (aktuelles Event) soll bei "Erfolge" nicht berücksichtigt werden.
                                ->where('event_id', '!=', (int) $event->id)
                                ->whereHas('raceTabele', function ($q2) {
                                    $q2->where('finale', 1);
                                })
                                ->where(function ($query) {
                                    $today = now()->format('Y-m-d');
                                    $now = now()->format('H:i:s');
                                    $query->where('rennDatum', '<', $today)
                                        ->orWhere(function ($q2) use ($today, $now) {
                                            $q2->where('rennDatum', $today)
                                                ->where('veroeffentlichungUhrzeit', '<=', $now);
                                        });
                                });
                        })
                        ->with('race')
                        ->get();

                    // Pro Teamlink: letztes Ergebnis je Event (wie im Steckbrief)
                    $lastResultsTextByTeamlink = $lanes
                        ->filter(function ($lane) use ($teamIdToTeamlink) {
                            return isset($teamIdToTeamlink[(int) $lane->mannschaft_id]);
                        })
                        ->sortByDesc(function ($lane) {
                            return ($lane->race?->rennDatum ?? '0000-00-00') . ' ' . ($lane->race?->rennUhrzeit ?? '00:00:00');
                        })
                        ->filter(function ($lane) {
                            return $lane->race && $lane->race->event_id;
                        })
                        ->groupBy(function ($lane) use ($teamIdToTeamlink) {
                            return (int) $teamIdToTeamlink[(int) $lane->mannschaft_id];
                        })
                        ->map(function ($lanesPerTeamlink) {
                            $perEvent = $lanesPerTeamlink
                                ->groupBy(fn ($lane) => (int) $lane->race->event_id)
                                ->map(fn ($lanesPerEvent) => $lanesPerEvent->first())
                                ->values();

                            return $perEvent
                                ->map(function ($res) {
                                    $platz = $res->platz ?? '-';
                                    $rennen = $res->race->rennBezeichnung ?? 'Rennen';
                                    $datum = $res->race->rennDatum ? Carbon::parse($res->race->rennDatum)->format('d.m.Y') : '-';
                                    return "Platz {$platz} – {$rennen} – {$datum}";
                                })
                                ->implode("\n");
                        });
                }
            }

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="Sprecherkarte.csv"',
            ];

            $callback = function() use ($regattateams, $participationCountByTeamlink, $lastResultsTextByTeamlink) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");
                fputcsv($file, [
                    'Nr.',
                    'Datum',
                    'Teamname',
                    'Verein / Firma',
                    'Ort',
                    'Wertung',
                    'Teambeschreibung',
                    'Teilnahmen',
                    'Erfolge'
                ], ';');
                foreach ($regattateams as $team) {
                    $teamlink = (int) ($team->teamlink ?? 0);
                    $participationCount = $teamlink > 0 ? (int) ($participationCountByTeamlink[$teamlink] ?? 0) : 0;
                    $lastResultsText = $teamlink > 0 ? (string) ($lastResultsTextByTeamlink[$teamlink] ?? '') : '';

                    $row = array_map(function($value) {
                        return mb_convert_encoding($value, 'UTF-8', 'auto');
                    }, [
                        $this->cleanCsvField($team->laufende_nummer),
                        $this->cleanCsvField($team->mailendatum ? \Carbon\Carbon::parse($team->mailendatum)->format('d.m.Y') : ''),
                        $this->cleanCsvField($team->teamname),
                        $this->cleanCsvField($team->verein),
                        $this->cleanCsvField($team->ort),
                        $this->cleanCsvField($team->getRaceType->typ),
                        $this->cleanCsvField($team->beschreibung),
                        $this->cleanCsvField($participationCount > 0 ? (string) $participationCount : ''),
                        $this->cleanCsvField($lastResultsText),
                    ]);
                    fputcsv($file, $row, ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }

}
