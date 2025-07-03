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
        }
        else {
            $regattateams = Null;
        }

        return view('api.APIStarterliste', compact('event', 'regattateams'));
    }

    public function APITeamliste()
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
        }
        else {
            $regattateams = Null;
        }

        return view('api.APITeamliste', compact('event', 'regattateams'));
    }
}
