<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CourseDate;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TrainingPlanningController extends Controller
{
    public function index(): View
    {
        $currentDomain = str_replace('www.', '', parse_url(url('/'), PHP_URL_HOST));
        $event = Event::whereHas('eventGroup', function ($query) use ($currentDomain) {
            $query->where('trainingDomain', $currentDomain);
        })
            ->orderBy('datumvon', 'desc')
            ->first();

        if (!$event) {
            $event = $this->getEvent();
        }

        if (!$event || !$event->id) {
            return view('pages.frontend.noEvent', compact('event'));
        }

        $previousEvent = null;
        if ($event->eventGroup_id && $event->datumvon) {
            $previousEvent = Event::where('eventGroup_id', $event->eventGroup_id)
                ->whereDate('datumvon', '<', $event->datumvon)
                ->orderBy('datumvon', 'desc')
                ->first();
        }

        $organiserId = DB::table('organisers')
            ->where('veranstaltungDomain', $currentDomain)
            ->value('id');

        $organiserIds = [];
        if ($event->sportSection_id) {
            $organiserIds = DB::table('organiser_sport_section')
                ->where('sport_section_id', $event->sportSection_id)
                ->pluck('organiser_id')
                ->all();
        }

        if ($organiserId) {
            $organiserIds[] = (int) $organiserId;
        }

        $organiserIds = array_values(array_unique(array_filter($organiserIds)));

        $query = CourseDate::with(['course:id,kursName,deleted_at', 'trainers'])
            ->whereNull('deleted_at')
            ->whereHas('course', function ($courseQuery) {
                $courseQuery->whereNull('deleted_at');
            })
            ->where('kursNichtDurchfuerbar', false)
            ->whereDate('kursstarttermin', '<', $event->datumvon)
            ->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('course_participant_bookeds')
                    ->whereColumn('course_participant_bookeds.kurs_id', 'coursedates.id')
                    ->where(function ($bookedQuery) {
                        $bookedQuery->whereNotNull('course_participant_bookeds.regattaTeam_id')
                            ->orWhereNotNull('course_participant_bookeds.participant_id')
                            ->orWhereNotNull('course_participant_bookeds.trainer_id');
                    });
            })
            ->orderBy('kursstarttermin');

        if ($previousEvent && $previousEvent->datumvon) {
            $query->whereDate('kursstarttermin', '>', $previousEvent->datumvon);
        }

        if (!empty($organiserIds)) {
            $query->whereIn('organiser_id', $organiserIds);
        } else {
            $query->whereRaw('1 = 0');
        }

        $courseDates = $query->get();

        return view('pages.frontend.trainingPlanning', compact('event', 'courseDates'));
    }
}
