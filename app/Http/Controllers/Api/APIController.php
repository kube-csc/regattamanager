<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegattaTeam;
use Illuminate\Http\Request;

class APIController extends Controller
{
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
                        $team->laufende_nummer,
                        $team->teamname,
                        $team->verein,
                        $team->getRaceType->typ,
                    ];
                    fputcsv($file, $row, ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        }
    }

    public function APITeamliste()
    {
        $event = $this->getEvent();

        if (empty($event->mitgliederSicherheitscode) || now()->gt($event->mitgliederSicherheitscodeEnddatum)) {
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
                        $team->laufende_nummer,
                        $team->teamname,
                        $team->verein,
                        $team->strasse,
                        $team->plz,
                        $team->ort,
                        $team->telefon,
                        $team->email,
                        $team->training,
                        $team->getRaceType->typ,
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
                ->get();

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
                        $team->laufende_nummer,
                        $team->mailendatum ? \Carbon\Carbon::parse($team->mailendatum)->format('d.m.Y') : '',
                        $team->teamname,
                        $team->verein,
                        $team->ort,
                        $team->getRaceType->typ,
                        $team->beschreibung,
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
