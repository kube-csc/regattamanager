
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
            @if($event->datumvona && $event->datumbis && $event->email && now()->between($event->datumvona, $event->datumbis))
                <br>
                <a href="/Meldung" class="about-btn xx-large" style="font-size: xx-large;">Melden<i class="bx bx-chevron-right"></i></a>
            @endif
        </div>
    </div>
</section><!-- End Services Section -->

@if($event->datumvona && $event->datumbis && $event->email && now()->between($event->datumvona, $event->datumbis))
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
