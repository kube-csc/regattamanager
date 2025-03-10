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
                    @if($event->datumvona && $event->datumbis && $event->email && now()->between($event->datumvona, $event->datumbis))
                       @if($event->anmeldetext)
                          <a href="/Ausschreibung" class="about-btn">Ausschreibung<i class="bx bx-chevron-right"></i></a>
                       @endif
                       <a href="/Meldung" class="about-btn">Melden<i class="bx bx-chevron-right"></i></a>
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
                                {{ str_replace('_', ' ', env('VEREIN_NAME')) }}<br>
                                {{ str_replace('_', ' ', env('VEREIN_STRASSE')) }}<br>
                                {{ str_replace('_', ' ', env('VEREIN_PLZ')) }} {{ str_replace('_', ' ', env('VEREIN_ORT')) }}
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
                                                    <li><a href="{{env('VEREIN_CANONICAL')}}/storage/eventDokumente/{{ $eventDokumente->bild }}" target="_blank">{{ $eventDokumente->titel }}</a></li>
                                                @else
                                                    <li><a href="{{env('VEREIN_CANONICAL')}}/daten/text/{{ $eventDokumente->image }}" target="_blank">{{ $eventDokumente->titel }}</a></li>
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

@if($event->datumvona && $event->datumbis && $event->email && now()->between($event->datumvona, $event->datumbis))
<!-- ======= Cta Section ======= -->
<section id="cta" class="cta">
    <div class="container" data-aos="zoom-in">

        <div class="text-center">
            <h3>Eure Chance, dabei zu sein!</h3>
            <p>Jetzt anmelden und Teil des {{ $event->ueberschrift }} werden – das ultimative Erlebnis wartet!</p>
            <a class="cta-btn" href="/Meldung">Melden</a>
           </div>

    </div>
</section><!-- End Cta Section -->
@endif

