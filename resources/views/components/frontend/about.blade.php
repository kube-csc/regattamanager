<!-- ======= About Section ======= -->
<section id="about" class="about">
    <div class="container">

        <div class="row no-gutters">
            <div class="content col-xl-5 d-flex align-items-stretch" data-aos="fade-up">
                <div class="content">
                    <h2>{{ $event->ueberschrift }}</h2>
                    <p>
                       @if($event->anmeldetext)
                         {!! $event->anmeldetext !!}
                       @else
                         {!! $event->beschreibung !!}
                       @endif
                    </p>

                    <a href="{{ route('pages.frontend.faq') }}" class="about-btn">FAQ<i class="bx bx-chevron-right"></i></a>

                    @if($event->email)
                       @if($event->anmeldetext)
                          <a href="/Ausschreibung" class="about-btn">Ausschreibung<i class="bx bx-chevron-right"></i></a>
                       @endif
                       @if($event->datumvona &&
                            $event->datumbisa &&
                            now()->toDateString() >= \Carbon\Carbon::parse($event->datumvona)->toDateString() &&
                            now()->toDateString() <= \Carbon\Carbon::parse($event->datumbisa)->toDateString())
                       <a href="/Meldung" class="about-btn">Melden<i class="bx bx-chevron-right"></i></a>
                       @endif
                    @endif
                </div>
            </div>
            <div class="col-xl-7 d-flex align-items-stretch">
                <div class="icon-boxes d-flex flex-column justify-content-center">
                    <div class="row">
                        <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="100">
                            <h4>Daten</h4>
                            <p>
                                @if($event->datumvon == $event->datumbis)
                                    Am:<br>
                                    {{ \Carbon\Carbon::parse($event->datumvon)->format('d.m.Y') }}
                                @else
                                    Wann:<br>
                                    {{ \Carbon\Carbon::parse($event->datumvon)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($event->datumbis)->format('d.m.Y') }}
                                @endif
                            </p>
                            @if($event->datumvona != null and $event->datumbisa != null)
                              <p>
                                Anmeldung:<br>
                                von: {{ \Carbon\Carbon::parse($event->datumvona)->format('d.m.Y') }} bis {{ \Carbon\Carbon::parse($event->datumbisa)->format('d.m.Y') }}
                              </p>
                            @endif
                            @if($event->datumvona == null and $event->datumbisa != null)
                              <p>
                                Anmeldung:<br>
                                bis {{ \Carbon\Carbon::parse($event->datumbisa)->format('d.m.Y') }}
                              </p>
                            @endif
                            <p>
                                Wo:<br>
                                {{ (string) config('verein.name', '') }}<br>
                                {{ (string) config('verein.strasse', '') }}<br>
                                {{ trim((string) config('verein.plz', '') . ' ' . (string) config('verein.ort', '')) }}
                            </p>
                            @if($event->telefon)
                              <p>
                                <i class="icofont-telephone"></i>
                                <a href="tel:{{ $event->telefon }}">{{ $event->telefon }}</a>
                              </p>
                            @endif
                            @if($event->email)
                              <p>
                                <i class="icofont-email"></i>
                                <a href="mailto:{{ $event->email }}">{{ $event->email }}</a>
                              </p>
                            @endif
                        </div>
                        @php
                            $delay=100;
                        @endphp
                        @if($raceTypes->count()>0)
                            @php
                                $delay=$delay+100;
                            @endphp
                            <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="{{$delay}}">
                                <h4>Klassen / Wertung</h4>
                                <ul>
                                  @foreach($raceTypes as $raceType)
                                   <li>{{ $raceType->typ }} {{ $raceType->distanz }}<br>
                                      {{ $raceType->beschreibung }}
                                   </li>
                                  @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($eventDokumentes->count()>0)
                            @php
                                $delay=$delay+100;
                                    $groupflak=0;
                                    $verwendung = [
                                         "2" => "Ausschreibung",
                                         "3" => "Programm",
                                         "4" => "Ergebnisse",
                                         "5" => "Plakat/Flyer",
                                     ];
                            @endphp

                            <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="{{$delay}}">
                                {{--<i class="bx bx-file"></i>--}}
                                <h4>Dokumente:</h4>
                                @foreach($eventDokumentes as $eventDokumente)
                                    @if($loop->first)
                                        @php
                                            $groupflak=$eventDokumente->verwendung;
                                        @endphp
                                        <ul>
                                            <li>{{ $verwendung[$groupflak] }}</li>
                                            <ul>
                                                @else
                                                    @if($eventDokumente->verwendung != $groupflak)
                                                        @php
                                                            $groupflak=$eventDokumente->verwendung;
                                                        @endphp
                                            </ul>
                                        </ul>
                                        <ul>
                                            <li>{{ $verwendung[$groupflak] }}</li>
                                            <ul>
                                                @endif
                                                @endif
                                                @if( $eventDokumente->bild != NULL)
                                                    <li><a href="{{ config('verein.canonical') }}/storage/eventDokumente/{{ $eventDokumente->bild }}" target="_blank">{{ $eventDokumente->titel }}</a></li>
                                                @else
                                                    <li><a href="{{ config('verein.canonical') }}/daten/text/{{ $eventDokumente->image }}" target="_blank">{{ $eventDokumente->titel }}</a></li>
                                                @endif
                                                @endforeach
                                            </ul>
                                        </ul>
                            </div>
                        @endif
                        @foreach($regattaInformations as $regattaInformation)
                                @php
                                   $delay=$delay+100;
                                @endphp
                                <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                                    <h4>{{ $regattaInformation->informationTittel }}</h4>
                                    <p>
                                        {!! $regattaInformation->informationBeschreibung !!}
                                    </p>
                                </div>
                        @endforeach
                    </div>
                </div><!-- End .content-->
            </div>
        </div>

    </div>
</section><!-- End About Section -->

{{-- FAQ wird nicht mehr direkt auf der About-Seite angezeigt. Nur Link/Button oben. --}}

@if($event->email && $event->datumvona && $event->datumbisa)
    @if(now()->toDateString() >= \Carbon\Carbon::parse($event->datumvona)->toDateString() &&
        now()->toDateString() <= \Carbon\Carbon::parse($event->datumbisa)->toDateString())
        <!-- ======= Cta Section ======= -->
        <section id="cta" class="cta">
            <div class="container" data-aos="zoom-in">

                <div class="text-center">
                    <h3>Eure Chance, dabei zu sein!</h3>
                    <p>Jetzt anmelden und Teil des {{ $event->ueberschrift }} werden – das ultimative Erlebnis wartet!</p>
                    <p>Es haben {{ $teamRaceCount  }} Teams gemeldet.</p>
                    <a class="cta-btn" href="/Meldung">Melden</a>
                </div>

            </div>
        </section><!-- End Cta Section -->
    @endif
    @if(now()->toDateString() > \Carbon\Carbon::parse($event->datumbisa)->toDateString())
        <!-- ======= Cta Section ======= -->
        <section id="cta" class="cta">
            <div class="container" data-aos="zoom-in">

                <div class="text-center">
                    <h3>Es haben viele die Chance genutzt, dabei zu sein!</h3>
                    <p>Insgesamt {{ $teamRaceCount }} Teams haben sich angemeldet.</p>
                </div>

            </div>
        </section><!-- End Cta Section -->
    @endif
@endif

