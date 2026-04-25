<?php

namespace App\Services;

use App\Models\Lane;
use App\Models\RegattaTeam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class RegattaTeamHistoryService
{
    /**
     * Liefert eine Basis-Query für vergangene Teilnahmen (Teams anderer, bereits beendeter Veranstaltungen).
     *
     * Kriterien:
     * - Status = "Neuanmeldung"
     * - Veranstaltungsende (events.datumbis) liegt in der Vergangenheit
     * - aktuelles Event wird explizit ausgeschlossen
     */
    private function basePastParticipationQuery(Collection $teamlinks, int $excludeEventId): Builder
    {
        return RegattaTeam::query()
            ->join('events', 'regatta_teams.regatta_id', '=', 'events.id')
            ->whereIn('regatta_teams.teamlink', $teamlinks)
            ->where('regatta_teams.status', 'Neuanmeldung')
            ->where('events.datumbis', '<', now()->format('Y-m-d'))
            ->where('events.id', '!=', $excludeEventId);
    }

    /**
     * Mapping: team_id => teamlink für vergangene Teilnahmen.
     */
    public function getPastTeamIdToTeamlink(Collection $teamlinks, int $excludeEventId): Collection
    {
        if ($teamlinks->isEmpty()) {
            return collect();
        }

        return $this->basePastParticipationQuery($teamlinks, $excludeEventId)
            ->select('regatta_teams.id as team_id', 'regatta_teams.teamlink')
            ->get()
            ->mapWithKeys(fn ($row) => [(int) $row->team_id => (int) $row->teamlink]);
    }

    /**
     * Anzahl vergangener Teilnahmen je teamlink.
     */
    public function getParticipationCountByTeamlink(Collection $teamlinks, int $excludeEventId): Collection
    {
        $teamIdToTeamlink = $this->getPastTeamIdToTeamlink($teamlinks, $excludeEventId);

        return $teamIdToTeamlink
            ->values()
            ->countBy();
    }

    /**
     * Liefert die letzten (je Event) veröffentlichten/abgeschlossenen Ergebnisse.
     *
     * @param Collection<int,int> $teamIds
     */
    public function getLastResultsByTeamIdsGroupedByEvent(Collection $teamIds, int $excludeEventId, bool $finaleOnly = true): Collection
    {
        if ($teamIds->isEmpty()) {
            return collect();
        }

        $lanes = $this->loadRelevantLanes($teamIds, $excludeEventId, $finaleOnly);

        // Wie im Steckbrief: nach Datum/Uhrzeit absteigend und je Event das letzte Ergebnis nehmen
        return $lanes
            ->sortByDesc(function (Lane $lane) {
                return ($lane->race?->rennDatum ?? '0000-00-00') . ' ' . ($lane->race?->rennUhrzeit ?? '00:00:00');
            })
            ->filter(fn (Lane $lane) => $lane->race && $lane->race->event_id)
            ->groupBy(fn (Lane $lane) => (int) $lane->race->event_id)
            ->map(fn (Collection $lanesPerEvent) => $lanesPerEvent->first())
            ->values();
    }

    /**
     * Ermittelt für mehrere Teams (Mapping team_id=>teamlink) die letzten Ergebnisse je Event
     * und liefert einen Text (mehrzeilig) pro teamlink.
     *
     * @param Collection<int,int> $teamIdToTeamlink Mapping team_id => teamlink
     */
    public function getLastResultsTextByTeamIdToTeamlink(Collection $teamIdToTeamlink, int $excludeEventId, bool $finaleOnly = true): Collection
    {
        if ($teamIdToTeamlink->isEmpty()) {
            return collect();
        }

        $teamIds = $teamIdToTeamlink->keys();
        $lanes = $this->loadRelevantLanes($teamIds, $excludeEventId, $finaleOnly);

        return $lanes
            ->filter(fn (Lane $lane) => isset($teamIdToTeamlink[(int) $lane->mannschaft_id]))
            ->sortByDesc(function (Lane $lane) {
                return ($lane->race?->rennDatum ?? '0000-00-00') . ' ' . ($lane->race?->rennUhrzeit ?? '00:00:00');
            })
            ->filter(fn (Lane $lane) => $lane->race && $lane->race->event_id)
            ->groupBy(fn (Lane $lane) => (int) $teamIdToTeamlink[(int) $lane->mannschaft_id])
            ->map(function (Collection $lanesPerTeamlink) {
                $perEvent = $lanesPerTeamlink
                    ->groupBy(fn (Lane $lane) => (int) $lane->race->event_id)
                    ->map(fn (Collection $lanesPerEvent) => $lanesPerEvent->first())
                    ->values();

                return $perEvent
                    ->map(function (Lane $res) {
                        $platz = $res->platz ?? '-';
                        $rennen = $res->race->rennBezeichnung ?? 'Rennen';
                        $datum = $res->race->rennDatum ? Carbon::parse($res->race->rennDatum)->format('d.m.Y') : '-';
                        return "Platz {$platz} – {$rennen} – {$datum}";
                    })
                    ->implode("\n");
            });
    }

    /**
     * Lädt alle relevanten Lanes inkl. Race für die Ergebnislogik.
     */
    private function loadRelevantLanes(Collection $teamIds, int $excludeEventId, bool $finaleOnly): Collection
    {
        return Lane::query()
            ->whereIn('mannschaft_id', $teamIds)
            ->whereHas('race', function ($q) use ($excludeEventId, $finaleOnly) {
                $q->where('status', 4)
                    ->where('visible', 1)
                    ->where('event_id', '!=', (int) $excludeEventId);

                if ($finaleOnly) {
                    $q->whereHas('raceTabele', function ($q2) {
                        $q2->where('finale', 1);
                    });
                }

                // nur bereits veröffentlichte Ergebnisse
                $q->where(function ($query) {
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
            ->get();
    }
}

