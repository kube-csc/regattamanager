<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\RegattaTeam;
use Illuminate\Http\Request;
use App\Services\RegattaTeamHistoryService;

class PresentationController extends Controller
{
    protected $event = null;

    public function __construct(
        private readonly RegattaTeamHistoryService $historyService
    ) {
    }

    public function teamProfile(Request $request, $id = null)
    {
        $event = $this->getEvent();
        $eventId = $event->id;

        // Optional: Finale-Filter steuerbar (default: nur Finale wie im Steckbrief)
        $finaleOnly = $request->boolean('finale', true);

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

        // Finale-Filter beim Blättern beibehalten
        $prevTeamUrl = route('RegattaTeam.steckbrief', ['team' => $prevTeamIndex, 'finale' => $finaleOnly ? 1 : 0]);
        $nextTeamUrl = route('RegattaTeam.steckbrief', ['team' => $nextTeamIndex, 'finale' => $finaleOnly ? 1 : 0]);

        // Fallback für Teambild
        $fallbackYear = null;
        if (!$team->bild && $team->teamlink > 0) {
            $fallbackTeam = RegattaTeam::join('events', 'regatta_teams.regatta_id', '=', 'events.id')
                ->where('regatta_teams.teamlink', $team->teamlink)
                ->whereNotNull('regatta_teams.bild')
                ->where('regatta_teams.bild', '!=', '')
                // Für historische Auswahl zählt das Veranstaltungs-Enddatum (datumbis), nicht der Anmeldezeitraum (datumbisa).
                ->where('events.datumbis', '<', $event->datumbis ?? now()->format('Y-m-d'))
                ->orderBy('events.datumbis', 'desc')
                ->select('regatta_teams.bild', 'events.datumbis')
                ->first();

            if ($fallbackTeam) {
                $team->bild = $fallbackTeam->bild;
                $fallbackYear = \Carbon\Carbon::parse($fallbackTeam->datumbis)->year;
            }
        }

        // Teilnahme-Statistik
        $participationCount = 0;
        $lastResults = collect();

        if ($team->teamlink > 0) {
            $teamIdToTeamlink = $this->historyService->getPastTeamIdToTeamlink(
                collect([(int) $team->teamlink]),
                (int) $eventId
            );

            $teamIds = $teamIdToTeamlink->keys();
            $participationCount = $teamIds->count();
            $lastResults = $this->historyService->getLastResultsByTeamIdsGroupedByEvent($teamIds, (int) $eventId, $finaleOnly);
        }

        return view('pages.frontend.teamProfile', [
            'team' => $team,
            'teamIndex' => $teamIndex,
            'teamCount' => $teamCount,
            'prevTeamUrl' => $prevTeamUrl,
            'nextTeamUrl' => $nextTeamUrl,
            'finaleOnly' => $finaleOnly,
            'event' => $event,
            'participationCount' => $participationCount,
            'lastResults' => $lastResults,
            'fallbackYear' => $fallbackYear,
        ]);
    }
}
