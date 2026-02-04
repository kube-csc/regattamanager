<!-- ======= Counts Section ======= -->
<section id="counts" class="counts  section-bg">
    <div class="container">
        <div class="row no-gutters">

            @foreach($regattaTeamCounts as $regattaTeamCount)
                @if($regattaTeamCount->total > 0)
                    <div class="col-lg-3 col-md-6 d-md-flex align-items-md-stretch">
                        <div class="count-box">
                            <i class="icofont-users-alt-5"></i>
                            <span data-toggle="counter-up">{{ $regattaTeamCount->total }}</span>
                            <p><strong>{{ $regattaTeamCount->getRaceType->typ }}</strong></p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section><!-- End Counts Section -->

<!-- ======= Services Section ======= -->
<section id="services" class="services">
    <div class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="50">
            <h2>Meldung {{ $event->ueberschrift }}</h2>
        </div>

        @if($regattaTeamCounts->count() === 0)
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 d-flex align-items-stretch">
                    <div class="count-box d-flex flex-column justify-content-center align-items-center text-center mx-auto w-100">
                        <p class="mb-0"><strong>Es haben noch keine Teams gemeldet.</strong></p>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            @php
                $delay=50;
            @endphp
            @foreach($regattaTeams as $regattaTeam)
                @php
                    $delay=$delay+25;
                @endphp
                <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                    <div class="icon-box" data-aos="fade-in" data-aos-delay="{{ $delay }}">
                        @if($regattaTeam->status == 'Warteliste')
                            <p class="text-danger font-weight-bold">aktuell auf der Warteliste</p>
                        @endif
                        <h4 class="title">{{ $regattaTeam->teamname }}</h4>

                        <ul class="description">
                            <li>Verein / Firma / Institution:<br>
                                {{ $regattaTeam->verein }}
                            </li>
                            @if($regattaTeam->homepage)
                                <li>Homepage:<br>
                                    <a href="{{ $regattaTeam->homepage }}" target="_blank">zur Webseite</a>
                                </li>
                            @endif
                            <li>Klasse / Wertung:<br>
                                {{ $regattaTeam->getRaceType->typ }}
                            </li>
                            <li>Anmeldedatum:<br>
                                {{ \Carbon\Carbon::parse($regattaTeam->datum)->format('d.m.Y') }}
                            </li>
                        </ul>

                        <div>
                            @if($regattaTeam->bild)
                                <img src="{{ asset('storage/teamImage/' . $regattaTeam->bild) }}" alt="{{ $regattaTeam->teamname }}" class="img-fluid">
                            @endif
                        </div>
                    </div>
                </div>

            @endforeach

        </div>
    </div>
</section><!-- End Services Section -->
