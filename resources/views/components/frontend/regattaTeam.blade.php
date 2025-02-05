
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
                                    <a href="{{ $regattaTeam->homepage }}" target="_blank">{{ $regattaTeam->homepage }}</a>
                                </li>
                            @endif
                            <li>Werbung:<br>
                                {{ $regattaTeam->getRaceType->typ }}
                            </li>
                        </ul>
                        </p>
                    </div>
                </div>

            @endforeach

        </div>
    </div>
</section><!-- End Services Section -->
