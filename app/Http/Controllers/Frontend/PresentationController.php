<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\RegattaTeam;
use App\Models\Lane;
use Illuminate\Http\Request;

class PresentationController extends Controller
{
    protected $event = null;

    public function teamProfile(Request $request, $id = null)
    {
        $event = $this->getEvent();
        $eventId = $event->id;

        $prevTeamUrl = null;
        $nextTeamUrl = null;

        // Teams für Navigation (nur gemeldete Teams des aktuellen Events, Status != 'Gelöscht')
        $teams = RegattaTeam::with(['getRaceType', 'getRaceType.template'])
            ->where('regatta_id', $eventId)
            ->where('status', '!=', 'Gelöscht')
            ->orderBy('teamname')
            ->get();

        $teamCount = $teams->count();

        if ($teamCount === 0) {
            return view('pages.frontend.teamProfile', [
                'team' => null,
                'teamIndex' => 0,
                'teamCount' => 0,
                'nextTeamUrl' => route('pages.frontend.home'),
                'event' => $event,
            ]);
        }

        // Bestimme das anzuzeigende Team
        if ($id) {
            // Direkter Aufruf über die ID (z.B. aus der Teamliste)
            $team = $teams->firstWhere('id', (int) $id);

            if (!$team) {
                return redirect()->route('RegattaTeam.index')->with('error', 'Team nicht gefunden.');
            }

            // Index des Teams innerhalb der sortierten Liste (für Blättern)
            $teamIndex = (int) $teams->search(fn ($t) => (int) $t->id === (int) $team->id);
        } else {
            // Aufruf im Präsentationsmodus (über Index)
            $teamIndex = (int) $request->query('team', 0);
            $teamIndex = max(0, min($teamIndex, $teamCount - 1));
            $team = $teams[$teamIndex];
        }

        // Vorheriges/Nächstes Team (zyklisch)
        $prevTeamIndex = ($teamIndex - 1 + $teamCount) % $teamCount;
        $nextTeamIndex = ($teamIndex + 1) % $teamCount;

        $prevTeamUrl = route('RegattaTeam.steckbrief', ['team' => $prevTeamIndex]);
        $nextTeamUrl = route('RegattaTeam.steckbrief', ['team' => $nextTeamIndex]);

        // Fallback für Teambild
        $fallbackYear = null;
        if (!$team->bild && $team->teamlink > 0) {
            $fallbackTeam = RegattaTeam::join('events', 'regatta_teams.regatta_id', '=', 'events.id')
                ->where('regatta_teams.teamlink', $team->teamlink)
                ->whereNotNull('regatta_teams.bild')
                ->where('regatta_teams.bild', '!=', '')
                ->where('events.datumbisa', '<', $event->datumbisa ?? now()->format('Y-m-d'))
                ->orderBy('events.datumbisa', 'desc')
                ->select('regatta_teams.bild', 'events.datumbisa')
                ->first();

            if ($fallbackTeam) {
                $team->bild = $fallbackTeam->bild;
                $fallbackYear = \Carbon\Carbon::parse($fallbackTeam->datumbisa)->year;
            }
        }

        // Teilnahme-Statistik
        $participationCount = 0;
        $lastResults = collect();

        if ($team->teamlink > 0) {
            $participationBaseQuery = RegattaTeam::join('events', 'regatta_teams.regatta_id', '=', 'events.id')
                ->where('regatta_teams.teamlink', $team->teamlink)
                ->where('regatta_teams.status', 'Neuanmeldung')
                ->where('events.datumbisa', '<', now()->format('Y-m-d'));

            $teamIds = (clone $participationBaseQuery)
                ->select('regatta_teams.id as team_id')
                ->pluck('team_id')
                ->unique()
                ->values();

            $participationCount = $teamIds->count();

            if ($teamIds->isNotEmpty()) {
                $lastResults = Lane::whereIn('mannschaft_id', $teamIds)
                    ->whereHas('race', function ($q) {
                        $q->where('status', 4)
                              ->where('visible', 1)
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
                    ->get()
                    ->sortByDesc(function ($lane) {
                        return ($lane->race?->rennDatum ?? '0000-00-00') . ' ' . ($lane->race?->rennUhrzeit ?? '00:00:00');
                    })
                    ->filter(function ($lane) {
                        return $lane->race && $lane->race->event_id;
                    })
                    ->groupBy(function ($lane) {
                        return $lane->race->event_id;
                    })
                    ->map(function ($lanesPerEvent) {
                        return $lanesPerEvent->first();
                    })
                    ->values();
            }
        }

        return view('pages.frontend.teamProfile', [
            'team' => $team,
            'teamIndex' => $teamIndex,
            'teamCount' => $teamCount,
            'prevTeamUrl' => $prevTeamUrl,
            'nextTeamUrl' => $nextTeamUrl,
            'event' => $event,
            'participationCount' => $participationCount,
            'lastResults' => $lastResults,
            'fallbackYear' => $fallbackYear,
        ]);
    }
}
