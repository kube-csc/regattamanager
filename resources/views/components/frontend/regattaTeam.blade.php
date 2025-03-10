
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

        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>Meldung {{ $event->ueberschrift }}</h2>
        </div>

        <div class="row">

            @foreach($regattaTeams as $regattaTeam)

                <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                    <div class="icon-box" data-aos="fade-up">
                        <h4 class="title">{{ $regattaTeam->teamname }}</h4>
                        <p class="description">
                        <ul>
                            <li>Verein:<br>
                                {{ $regattaTeam->verein }}
                            </li>
                            @if($regattaTeam->homepage)
                                <li>Homepage:<br>
                                    <a href="{{ $regattaTeam->homepage }}" target="_blank">zur Webseite</a>
                                </li>
                            @endif
                            <li>Klassen:<br>
                                {{ $regattaTeam->getRaceType->typ }}
                            </li>
                            <li>Anmeldedatum:<br>
                                {{ \Carbon\Carbon::parse($regattaTeam->datum)->format('d.m.Y') }}
                            </li>
                        </ul>
                        </p>
                    </div>
                </div>

            @endforeach

        </div>
    </div>
</section><!-- End Services Section -->
