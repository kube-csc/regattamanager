<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegattaTeam;
use Illuminate\Http\Request;

class APIController extends Controller
{
    // Hilfsfunktion im Controller ergänzen
    private function cleanCsvField($value) {
        return str_replace([";", "\r", "\n"], [",", "<br>", "<br>"], $value);
    }

    public function APIStartliste()
    {
        $event = $this->getEvent();

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

        if (empty($event->mitgliederSicherheitscode == $code) || now()->gt($event->mitgliederSicherheitscodeEnddatum)) {
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
