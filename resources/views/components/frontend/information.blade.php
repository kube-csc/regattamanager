
<!-- ======= Services Section ======= -->
<section id="services" class="services">
    <div class="container">
        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>Meldung {{ $event->ueberschrift }}</h2>
        </div>
        <div class="row">
            {!! $event->beschreibung !!}
        </div>
        <div>
            @if($event->datumvona && $event->datumbisa && $event->email && now()->between($event->datumvona, $event->datumbisa))
                <br>
                <a href="/Meldung" class="about-btn xx-large" style="font-size: xx-large;">Melden<i class="bx bx-chevron-right"></i></a>
            @endif
        </div>
    </div>
</section><!-- End Services Section -->

@if($event->email && $event->datumvona)
    @if($event->datumbisa && now()->between($event->datumvona, $event->datumbisa))
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
    @if($event->datumbisa && now() > $event->datumbisa)
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
