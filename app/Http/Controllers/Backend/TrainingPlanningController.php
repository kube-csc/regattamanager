<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CourseDate;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TrainingPlanningController extends Controller
{
    private function hasBookedReference($query): void
    {
        $query->where(function ($bookedQuery) {
            $bookedQuery->whereNotNull('course_participant_bookeds.regattaTeam_id')
                ->orWhereNotNull('course_participant_bookeds.participant_id')
                ->orWhereNotNull('course_participant_bookeds.trainer_id');
        });
    }

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

        $baseQuery = CourseDate::query()
            ->whereNull('deleted_at')
            ->whereHas('course', function ($courseQuery) {
                $courseQuery->whereNull('deleted_at');
            })
            ->where('kursNichtDurchfuerbar', false)
            ->whereDate('kursstarttermin', '<', $event->datumvon);

        if ($previousEvent && $previousEvent->datumvon) {
            $baseQuery->whereDate('kursstarttermin', '>', $previousEvent->datumvon);
        }

        if (!empty($organiserIds)) {
            $baseQuery->whereIn('organiser_id', $organiserIds);
        } else {
            $baseQuery->whereRaw('1 = 0');
        }

        $courseDates = (clone $baseQuery)
            ->with(['course:id,kursName,deleted_at', 'trainers'])
            ->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('course_participant_bookeds')
                    ->whereNull('course_participant_bookeds.deleted_at')
                    ->whereColumn('course_participant_bookeds.kurs_id', 'coursedates.id');
                $this->hasBookedReference($subQuery);
            })
            ->orderBy('kursstarttermin')
            ->get();

        $bookedCourseDates = (clone $baseQuery)
            ->with(['course:id,kursName,deleted_at', 'trainers'])
            ->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('course_participant_bookeds')
                    ->whereNull('course_participant_bookeds.deleted_at')
                    ->whereColumn('course_participant_bookeds.kurs_id', 'coursedates.id');
                $this->hasBookedReference($subQuery);
            })
            ->orderBy('kursstarttermin')
            ->get();

        $bookedByRows = DB::table('course_participant_bookeds')
            ->leftJoin('regatta_teams', 'regatta_teams.id', '=', 'course_participant_bookeds.regattaTeam_id')
            ->leftJoin('course_participants', 'course_participants.id', '=', 'course_participant_bookeds.participant_id')
            ->leftJoin('users as trainer_users', 'trainer_users.id', '=', 'course_participant_bookeds.trainer_id')
            ->whereNull('course_participant_bookeds.deleted_at')
            ->whereIn('course_participant_bookeds.kurs_id', $bookedCourseDates->pluck('id')->all())
            ->select(
                'course_participant_bookeds.kurs_id',
                'course_participant_bookeds.regattaTeam_id',
                'course_participant_bookeds.participant_id',
                'course_participant_bookeds.trainer_id',
                'regatta_teams.teamname as team_name',
                'course_participants.vorname as participant_vorname',
                'course_participants.nachname as participant_nachname',
                'trainer_users.vorname as trainer_vorname',
                'trainer_users.nachname as trainer_nachname',
                'trainer_users.name as trainer_name'
            )
            ->get();

        $bookedByMap = [];
        foreach ($bookedByRows as $bookedByRow) {
            $bookedBy = null;
            if ($bookedByRow->regattaTeam_id && $bookedByRow->team_name) {
                $bookedBy = 'Team: '.$bookedByRow->team_name;
            } elseif ($bookedByRow->participant_id) {
                $participantName = trim(($bookedByRow->participant_vorname ?? '').' '.($bookedByRow->participant_nachname ?? ''));
                if ($participantName !== '') {
                    $bookedBy = 'Teilnehmer: '.$participantName;
                }
            } elseif ($bookedByRow->trainer_id) {
                $trainerName = trim(($bookedByRow->trainer_vorname ?? '').' '.($bookedByRow->trainer_nachname ?? ''));
                $bookedBy = $trainerName !== '' ? 'Trainer: '.$trainerName : 'Trainer: '.($bookedByRow->trainer_name ?? '-');
            }

            if ($bookedBy) {
                $bookedByMap[$bookedByRow->kurs_id][] = $bookedBy;
            }
        }

        $bookedCourseDates->transform(function ($courseDate) use ($bookedByMap) {
            $bookedByEntries = $bookedByMap[$courseDate->id] ?? [];
            $courseDate->bookedBy = implode(', ', array_values(array_unique($bookedByEntries)));
            return $courseDate;
        });

        return view('pages.frontend.trainingPlanning', compact('event', 'courseDates', 'bookedCourseDates'));
    }
}
