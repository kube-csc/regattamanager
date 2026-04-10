@props(['team', 'teamIndex', 'teamCount', 'participationCount', 'lastResults', 'fallbackYear', 'prevTeamUrl' => null, 'nextTeamUrl' => null])

<name id="about">
    <!-- ======= Breadcrumbs Section ======= -->
    <section class="breadcrumbs">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center">
                <h2>Steckbrief</h2>
                <ol>
                    <li><a href="{{ route('pages.frontend.home') }}">Home</a></li>
                    <li>Steckbrief</li>
                </ol>
            </div>
        </div>
    </section><!-- End Breadcrumbs Section -->

    <!-- ======= Inner Page Section ======= -->
    <section id="about" class="about">
    <div class="container">
        <div class="row" data-aos="fade-up" data-aos-delay="100">
            <div class="col-lg-12">
                <div class="card mb-4 w-100 h-100 flex-grow-1">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h1 class="mb-0"><strong>{{ $team->teamname }}</strong></h1>
                        @if(($teamCount ?? 0) > 0)
                            <div class="mt-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <a
                                    href="{{ $prevTeamUrl ?? '#' }}"
                                    class="btn btn-sm btn-outline-light {{ $prevTeamUrl ? '' : 'disabled' }}"
                                    @if(!$prevTeamUrl) aria-disabled="true" tabindex="-1" @endif
                                >
                                    &laquo; Zurück
                                </a>

                                <span class="small">
                                    Team {{ (int) $teamIndex + 1 }} von {{ (int) $teamCount }}
                                </span>

                                <a href="{{ $nextTeamUrl ?? '#' }}" class="btn btn-sm btn-outline-light {{ $nextTeamUrl ? '' : 'disabled' }}"
                                   @if(!$nextTeamUrl) aria-disabled="true" tabindex="-1" @endif>
                                    Weiter &raquo;
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body bg-light overflow-auto">
                        <div class="row h-100">
                            <!-- Team Bild -->
                            <div class="col-md-5 text-center mb-4">
                                @if($team->bild)
                                    <div class="position-relative d-inline-block w-100">
                                        <img
                                            src="{{ config('app.regatta_url') . '/storage/teamImage/' . $team->bild }}"
                                            alt="Teamfoto"
                                            class="img-fluid rounded shadow-lg w-100 object-fit-cover"
                                            style="max-height: 55vh;"
                                            onerror="if (!this.dataset.fallback){ this.dataset.fallback='1'; this.src='{{ asset('assets/img/keinBild.png') }}'; }"
                                        >
                                        @if($fallbackYear)
                                            <div class="position-absolute bottom-0 end-0 bg-dark text-white p-2 small rounded-start opacity-75">
                                                Foto von {{ $fallbackYear }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="d-none d-md-flex align-items-center justify-content-center bg-white border rounded shadow-sm w-100"
                                         style="height: 55vh; min-height: 280px;">
                                        <span class="text-muted">Kein Bild vorhanden</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Team Details -->
                            <div class="col-md-7">
                                <div class="mb-4 text-center text-md-start">
                                    <div class="fs-4">
                                        <div class="d-flex align-items-baseline">
                                            <div class="fw-bold text-right" style="min-width: 12.5rem;">Verein / Firma / Institution:</div>
                                            <div class="text-primary ml-2">{{ $team->verein ?: '-' }}</div>
                                        </div>
                                    </div>
                                    <hr>
                                    @php
                                        $rennklasse = trim((string) ($team->getRaceType?->typ ?? '-'));
                                        $bootsklasse = trim((string) ($team->getRaceType?->template?->typ ?? '-'));
                                    @endphp
                                    <div class="fs-4">
                                        <div class="d-flex align-items-baseline">
                                            <div class="fw-bold text-right" style="min-width: 9.5rem;">Rennklasse:</div>
                                            <div class="text-secondary ml-2">{{ $rennklasse }}</div>
                                        </div>

                                        @if($rennklasse !== $bootsklasse)
                                            <div class="d-flex align-items-baseline">
                                                <div class="fw-bold text-right" style="min-width: 9.5rem;">Bootsklasse:</div>
                                                <div class="text-secondary ml-2">{{ $bootsklasse }}</div>
                                            </div>
                                        @endif

                                        @if($team->ort)
                                            <div class="d-flex align-items-baseline">
                                                <div class="fw-bold text-right" style="min-width: 9.5rem;">Ort:</div>
                                                <div class="text-secondary ml-2">{{ $team->ort }}</div>
                                            </div>
                                        @endif

                                        @if($participationCount > 0)
                                            <div class="d-flex align-items-baseline">
                                                <div class="fw-bold text-right" style="min-width: 9.5rem;">Teilnahmen:</div>
                                                <div class="text-primary ml-2">{{ $participationCount }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Beschreibung (temporär ausgeblendet)
                                @if($team->beschreibung)
                                    <div class="mb-4">
                                        <h4 class="border-bottom pb-2">Beschreibung</h4>
                                        <div class="fs-5">{!! $team->beschreibung !!}</div>
                                    </div>
                                @endif
                                --}}

                                @if($lastResults->count() > 0)
                                    <div class="mt-auto">
                                        <h4 class="border-bottom pb-2">Letzte Erfolge</h4>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered bg-white shadow-sm">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th class="text-center">Platz</th>
                                                        <th>Rennen</th>
                                                        <th>Datum</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="fs-5">
                                                    @foreach($lastResults as $res)
                                                        <tr>
                                                            <td class="text-end fw-bold text-primary" style="width: 20%">Platz {{ $res->platz ?? '-' }}</td>
                                                            <td>{{ $res->race->rennBezeichnung ?? 'Rennen' }}</td>
                                                            <td class="text-muted" style="width: 25%">{{ $res->race->rennDatum ? date('d.m.Y', strtotime($res->race->rennDatum)) : '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
</name>
