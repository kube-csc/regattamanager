<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
                'Content-Disposition' => 'attachment; filename="Sprecherkarte.csv"',
            ];

            $callback = function() use ($regattateams) {
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
                        "",
                        "",
                    ]);
                    fputcsv($file, $row, ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }

}
