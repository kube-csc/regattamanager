<!-- ======= Counts Section ======= -->
<section id="counts" class="counts section-bg">
    <div class="container">
        <div class="row no-gutters">
            <div class="col-lg-4 col-md-6 d-md-flex align-items-md-stretch">
                <div class="count-box">
                    <i class="icofont-history"></i>
                    <span data-toggle="counter-up">{{ $pastCourseDatesCount }}</span>
                    <p><strong>Vergangene Kurstermine</strong></p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 d-md-flex align-items-md-stretch">
                <div class="count-box">
                    <a href="#freie-kurstermine" class="text-reset text-decoration-none">
                        <i class="icofont-calendar"></i>
                        <span data-toggle="counter-up">{{ $courseDates->count() }}</span>
                        <p><strong>Freie Kurstermine</strong></p>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 d-md-flex align-items-md-stretch">
                <div class="count-box">
                    <a href="#gebuchte-kurstermine" class="text-reset text-decoration-none">
                        <i class="icofont-check-circled"></i>
                        <span data-toggle="counter-up">{{ $bookedCourseDates->count() }}</span>
                        <p><strong>Gebuchte Kurstermine</strong></p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section><!-- End Counts Section -->

<!-- ======= Services Section ======= -->
<section id="services" class="services">
    <div class="container">
        <div id="freie-kurstermine" class="section-title" data-aos="fade-in" data-aos-delay="50">
            <h2>Trainingsplanung</h2>
            <p>Freie Kurstermine</p>
        </div>

        @if($courseDates->isEmpty())
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 d-flex align-items-stretch">
                    <div class="count-box d-flex flex-column justify-content-center align-items-center text-center mx-auto w-100">
                        <p class="mb-0"><strong>Keine freien Termine vorhanden.</strong></p>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                @php $delay = 50; @endphp
                @foreach($courseDates as $courseDate)
                    @php $delay += 25; @endphp
                    <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                        <div class="icon-box" data-aos="fade-in" data-aos-delay="{{ $delay }}">
                            <h4 class="title">{{ $courseDate->course?->kursName ?? '-' }}</h4>
                            <div class="description">
                                @php
                                    $courseStart = $courseDate->kursstarttermin ? \Carbon\Carbon::parse($courseDate->kursstarttermin) : null;
                                    $courseEnd = $courseDate->kursendtermin ? \Carbon\Carbon::parse($courseDate->kursendtermin) : null;
                                    $startDay = $courseStart ? $courseStart->locale('de')->isoFormat('ddd') : '-';
                                    $endDay = $courseEnd ? $courseEnd->locale('de')->isoFormat('ddd') : '-';
                                    $isSameDay = $courseStart && $courseEnd && $courseStart->toDateString() === $courseEnd->toDateString();
                                    $durationSeconds = $courseDate->kurslaenge ? strtotime($courseDate->kurslaenge) - strtotime('00:00:00') : 0;
                                    $calculatedEnd = $courseStart ? $courseStart->copy()->addSeconds($durationSeconds) : null;
                                    $durationLabel = $courseDate->kurslaenge ? \Carbon\Carbon::parse($courseDate->kurslaenge)->format('H:i') : '-';
                                    $trainerNames = $courseDate->trainers->map(function ($trainer) {
                                        $fullName = trim(($trainer->vorname ?? '').' '.($trainer->nachname ?? ''));
                                        return $fullName !== '' ? $fullName : ($trainer->name ?? '-');
                                    })->implode(', ');
                                    $bookingDomain = $event?->eventGroup?->trainingDomain;
                                    $bookingCourseId = $courseDate->id;
                                    $bookingUrl = $bookingDomain && $bookingCourseId ? 'https://' . $bookingDomain . '/Kurseangebot/' . $bookingCourseId : null;
                                @endphp
                                @if($courseStart && $courseEnd && $isSameDay && $calculatedEnd && $calculatedEnd->timestamp == $courseEnd->timestamp)
                                    <strong>Datum:</strong><br>{{ $startDay }} {{ $courseStart->format('d.m.Y') }}<br>
                                    <strong>Uhrzeit:</strong><br>von {{ $courseStart->format('H:i') }} Uhr bis {{ $courseEnd->format('H:i') }} Uhr<br>
                                @endif
                                @if($courseStart && $courseEnd && $isSameDay && $calculatedEnd && $calculatedEnd->timestamp != $courseEnd->timestamp)
                                    <strong>Datum:</strong><br>{{ $startDay }} {{ $courseStart->format('d.m.Y') }}<br>
                                    <strong>Uhrzeit:</strong><br>von {{ $courseStart->format('H:i') }} Uhr bis {{ $courseEnd->format('H:i') }} Uhr<br>
                                    <span class="text-info" style="font-size: 0.95em;">
                                        <i class="bx bx-info-circle"></i>
                                        Die Startuhrzeit kann beim Buchen individuell angepasst werden.
                                    </span>
                                    <br>
                                @endif
                                @if($courseStart && $courseEnd && !$isSameDay)
                                    <strong>Terminserie<br>
                                        Start Datum:</strong><br>
                                    von {{ $startDay }} {{ $courseStart->format('d.m.Y') }}<br>
                                    <strong>Uhrzeit:</strong><br>
                                    ab {{ $courseStart->format('H:i') }} Uhr<br>
                                    <strong>End Datum:</strong><br>
                                    bis {{ $endDay }} {{ $courseEnd->format('d.m.Y') }}<br>
                                @endif
                                <strong>Dauer:</strong><br>{{ $durationLabel }}  Stunde(n)<br>
                                <strong>Trainer:</strong><br>{{ $trainerNames !== '' ? $trainerNames : '-' }}
                                @if($bookingUrl)
                                    <br>
                                    <a href="{{ $bookingUrl }}" target="_blank" rel="noopener noreferrer">Training buchen</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div id="gebuchte-kurstermine" class="section-title mt-5" data-aos="fade-in" data-aos-delay="50">
            <h2>Gebuchte Kurstermine</h2>
            <p>Wer hat gebucht?</p>
        </div>

        @if($bookedCourseDates->isEmpty())
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 d-flex align-items-stretch">
                    <div class="count-box d-flex flex-column justify-content-center align-items-center text-center mx-auto w-100">
                        <p class="mb-0"><strong>Keine gebuchten Termine vorhanden.</strong></p>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                @php $delay = 50; @endphp
                @foreach($bookedCourseDates as $courseDate)
                    @php $delay += 25; @endphp
                    <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                        <div class="icon-box" data-aos="fade-in" data-aos-delay="{{ $delay }}">
                            <h4 class="title">{{ $courseDate->course?->kursName ?? '-' }}</h4>
                            <div class="description">
                                @php
                                    $courseStart = $courseDate->kursstarttermin ? \Carbon\Carbon::parse($courseDate->kursstarttermin) : null;
                                    $courseEnd = $courseDate->kursendtermin ? \Carbon\Carbon::parse($courseDate->kursendtermin) : null;
                                    $startDay = $courseStart ? $courseStart->locale('de')->isoFormat('ddd') : '-';
                                    $endDay = $courseEnd ? $courseEnd->locale('de')->isoFormat('ddd') : '-';
                                    $isSameDay = $courseStart && $courseEnd && $courseStart->toDateString() === $courseEnd->toDateString();
                                    $durationSeconds = $courseDate->kurslaenge ? strtotime($courseDate->kurslaenge) - strtotime('00:00:00') : 0;
                                    $calculatedEnd = $courseStart ? $courseStart->copy()->addSeconds($durationSeconds) : null;
                                    $durationLabel = $courseDate->kurslaenge ? \Carbon\Carbon::parse($courseDate->kurslaenge)->format('H:i') : '-';
                                    $bookedTrainerNames = $courseDate->trainers->map(function ($trainer) {
                                        $fullName = trim(($trainer->vorname ?? '').' '.($trainer->nachname ?? ''));
                                        return $fullName !== '' ? $fullName : ($trainer->name ?? '-');
                                    })->implode(', ');
                                @endphp
                                @if($courseStart && $courseEnd && $isSameDay && $calculatedEnd && $calculatedEnd->timestamp == $courseEnd->timestamp)
                                    <strong>Datum:</strong><br>{{ $startDay }} {{ $courseStart->format('d.m.Y') }}<br>
                                    <strong>Uhrzeit:</strong><br>von {{ $courseStart->format('H:i') }} Uhr bis {{ $courseEnd->format('H:i') }} Uhr<br>
                                @endif
                                @if($courseStart && $courseEnd && $isSameDay && $calculatedEnd && $calculatedEnd->timestamp != $courseEnd->timestamp)
                                    <strong>Datum:</strong><br>{{ $startDay }} {{ $courseStart->format('d.m.Y') }}<br>
                                    <strong>Uhrzeit:</strong><br>von {{ $courseStart->format('H:i') }} Uhr bis {{ $courseEnd->format('H:i') }} Uhr<br>
                                    <span class="text-info" style="font-size: 0.95em;">
                                        <i class="bx bx-info-circle"></i>
                                        Die Startuhrzeit kann beim Buchen individuell angepasst werden.
                                    </span>
                                @endif
                                @if($courseStart && $courseEnd && !$isSameDay)
                                    <strong>Terminserie<br>
                                        Start Datum:</strong><br>
                                    von {{ $startDay }} {{ $courseStart->format('d.m.Y') }}<br>
                                    <strong>Uhrzeit:</strong><br>
                                    ab {{ $courseStart->format('H:i') }} Uhr<br>
                                    <strong>End Datum:</strong><br>
                                    bis {{ $endDay }} {{ $courseEnd->format('d.m.Y') }}
                                @endif
                                <strong>Dauer:</strong><br>{{ $durationLabel }} Stunde(n)<br>
                                <strong>Trainer:</strong><br>{{ $bookedTrainerNames !== '' ? $bookedTrainerNames : '-' }}<br>
                                <strong>Gebucht von:</strong><br>{{ $courseDate->bookedBy ?: '-' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
