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
            ->map(function (Collection $lanesPerEvent) {
                $lane = $lanesPerEvent->first();
                $lane->display_platz = $this->resolveDisplayPlace($lane, $lanesPerEvent);

                return $lane;
            })
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
                    ->map(function (Collection $lanesPerEvent) {
                        $lane = $lanesPerEvent->first();
                        $lane->display_platz = $this->resolveDisplayPlace($lane, $lanesPerEvent);

                        return $lane;
                    })
                    ->values();

                return $perEvent
                    ->map(function (Lane $res) {
                        $platz = $res->display_platz ?? $res->platz ?? '-';
                        $rennen = $res->race->rennBezeichnung ?? 'Rennen';
                        $datum = $res->race->rennDatum ? Carbon::parse($res->race->rennDatum)->format('d.m.Y') : '-';
                        return "Platz {$platz} – {$rennen} – {$datum}";
                    })
                    ->implode("\n");
            });
    }

    /**
     * Ermittelt den anzuzeigenden Platz.
     * Wenn der gespeicherte Platz 0 ist, wird innerhalb des gleichen Rennens
     * anhand von Wertungsart und Ergebnisdaten neu gerankt.
     */
    private function resolveDisplayPlace(Lane $lane, Collection $lanesInSameEvent): int
    {
        $storedPlace = (int) ($lane->platz ?? 0);
        if ($storedPlace > 0) {
            return $storedPlace;
        }

        $raceId = (int) ($lane->race?->id ?? $lane->rennen_id ?? 0);
        if ($raceId <= 0) {
            return 0;
        }

        $tableId = (int) ($lane->tabele_id ?? $lane->race?->tabele_id ?? 0);
        if ($tableId <= 0) {
            return 0;
        }

        $sameRaceLanes = $lanesInSameEvent
            ->filter(fn (Lane $candidate) =>
                (int) ($candidate->race?->id ?? $candidate->rennen_id ?? 0) === $raceId
                && (int) ($candidate->tabele_id ?? $candidate->race?->tabele_id ?? 0) === $tableId
            )
            ->values();

        if ($sameRaceLanes->isEmpty()) {
            return 0;
        }

        $valuationType = (int) ($lane->race?->raceTabele?->wertungsart ?? 0);

        $rankedLanes = $sameRaceLanes
            ->sort(function (Lane $a, Lane $b) use ($valuationType, $tableId) {
                return $this->compareLanesForDisplayPlace($a, $b, $valuationType, $tableId);
            })
            ->values();

        $index = $rankedLanes->search(fn (Lane $candidate) => (int) $candidate->id === (int) $lane->id);

        return $index === false ? 0 : $index + 1;
    }

    /**
     * Vergleichslogik für die Platzermittlung.
     * Punktwertungen: mehr Punkte zuerst.
     * Zeit-/Laufwertungen: schnellere Zeit, dann Hundertstelsekunden.
     */
    private function compareLanesForDisplayPlace(Lane $a, Lane $b, int $valuationType, int $tableId): int
    {
        $tableA = (int) ($a->tabele_id ?? $a->race?->tabele_id ?? 0);
        $tableB = (int) ($b->tabele_id ?? $b->race?->tabele_id ?? 0);

        if ($tableA !== $tableId || $tableB !== $tableId) {
            return $tableA <=> $tableB;
        }

        if ($valuationType === 1) {
            $pointsA = (int) ($a->punkte ?? 0);
            $pointsB = (int) ($b->punkte ?? 0);

            if ($pointsA !== $pointsB) {
                return $pointsB <=> $pointsA;
            }
        }

        $timeA = (string) ($a->zeit ?? '');
        $timeB = (string) ($b->zeit ?? '');

        if ($timeA !== $timeB) {
            return $timeA <=> $timeB;
        }

        $hundredA = (int) ($a->hundert ?? 0);
        $hundredB = (int) ($b->hundert ?? 0);

        if ($hundredA !== $hundredB) {
            return $hundredA <=> $hundredB;
        }

        return (int) $a->id <=> (int) $b->id;
    }

    /**
     * Lädt alle relevanten Lanes inkl. Race für die Ergebnislogik.
     */
    private function loadRelevantLanes(Collection $teamIds, int $excludeEventId, bool $finaleOnly): Collection
    {
        return Lane::query()
            ->whereIn('mannschaft_id', $teamIds)
            ->where('platz', '!=', 99999)
            ->whereHas('race', function ($q) use ($excludeEventId, $finaleOnly) {
                $q->where('status', '>', 3)
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

