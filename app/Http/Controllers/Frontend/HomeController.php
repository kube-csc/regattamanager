<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\RaceType;
use App\Models\RegattaInformation;
use App\Models\RegattaTeam;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $event = $this->getEvent();
        $teamRaceCount = RegattaTeam::where('regatta_id', $event->id)
            ->where('status', '!=', 'gelöscht')
            ->count();

        if ($event->id != null) {
            $raceTypes = RaceType::where('regatta_id', $event->id)->get();

            $temp = 0;
            $eventDokumentes = Report::where('event_id', $event->id)
                ->where('visible', 1)
                ->where('webseite', 1)
                ->where('verwendung', '>', 1)
                ->where('verwendung', '<', 6)
                ->where('typ', '>', 9)
                ->where('typ', '<', 13)
                ->where(function ($query) use ($temp) {
                    $query->where('bild', "!=", NULL)
                        ->orwhere('image', "!=", NULL);
                })
                ->orderby('verwendung')
                ->orderby('position')
                ->get();

            $temp = 0;
            $regattaInformations = RegattaInformation::where('event_id', $event->id)
                ->where(function ($query) use ($temp) {
                    $query->where('startDatumVerschoben', "<=", Carbon::now())
                        ->orwhere('startDatumAktiv', 0);
                })
                ->where(function ($query) use ($temp) {
                    $query->where('endDatumVerschoben', ">=", Carbon::now())
                        ->orwhere('endDatumAktiv', 0);
                })
                ->where('visible', 1)
                ->orderby('position')
                ->get();
        }
        else {
            return view('pages.frontend.noEvent', compact('event'));
        }

        return view('pages.frontend.home',
            compact('event', 'raceTypes', 'eventDokumentes', 'regattaInformations', 'teamRaceCount'));
    }

    public function imprint()
    {
       return view('home.imprint');
    }

    public function journey()
    {
        $event = $this->getEvent();

        return view('pages.frontend.journey', compact('event'));
    }

    public function information()
    {
        $event = $this->getEvent();
        $teamRaceCount = RegattaTeam::where('regatta_id', $event->id)
            ->where('status', '!=', 'gelöscht')
            ->count();

        return view('pages.frontend.information', compact('event', 'teamRaceCount'));
    }

}
