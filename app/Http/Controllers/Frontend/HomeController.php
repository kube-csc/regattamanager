<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\CourseDate;
use App\Models\RaceType;
use App\Models\RegattaInformation;
use App\Models\RegattaTeam;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $event = $this->getEvent();
        $teamRaceCount = RegattaTeam::where('regatta_id', $event->id)
            ->where('status', '!=', 'Gelöscht')
            ->count();
        $hasTrainingPlanning = $this->hasTrainingPlanning($event);

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
            compact('event', 'raceTypes', 'eventDokumentes', 'regattaInformations', 'teamRaceCount', 'hasTrainingPlanning'));
    }

    private function hasTrainingPlanning($event): bool
    {
        if (!$event || !$event->id || !$event->eventGroup_id || !$event->datumvon) {
            return false;
        }

        $currentDomain = str_replace('www.', '', parse_url(url('/'), PHP_URL_HOST));

        $organiserIds = DB::table('organiser_sport_section')
            ->where('sport_section_id', $event->sportSection_id)
            ->pluck('organiser_id')
            ->all();

        $organiserId = DB::table('organisers')
            ->where('veranstaltungDomain', $currentDomain)
            ->value('id');

        if ($organiserId) {
            $organiserIds[] = (int) $organiserId;
        }

        $organiserIds = array_values(array_unique(array_filter($organiserIds)));

        if (empty($organiserIds)) {
            return false;
        }

        $previousEvent = Event::where('eventGroup_id', $event->eventGroup_id)
            ->whereDate('datumvon', '<', $event->datumvon)
            ->orderBy('datumvon', 'desc')
            ->first();

        $query = CourseDate::query()
            ->whereNull('deleted_at')
            ->whereHas('course', function ($courseQuery) {
                $courseQuery->whereNull('deleted_at');
            })
            ->where('kursNichtDurchfuerbar', false)
            ->whereDate('kursstarttermin', '>', $event->datumvon)
            ->whereIn('organiser_id', $organiserIds);

        if ($previousEvent && $previousEvent->datumvon) {
            $query->whereDate('kursstarttermin', '>', $previousEvent->datumvon);
        }

        return $query->exists();
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
            ->where('status', '!=', 'Gelöscht')
            ->count();

        return view('pages.frontend.information', compact('event', 'teamRaceCount'));
    }

}
