<section id="services" class="services">
    <div class="container">
        <div class="section-title" data-aos="fade-in" data-aos-delay="50">
            <h2>Trainingsplanung</h2>
            <p>Freie Kurstermine</p>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Kurs</th>
                    <th>Starttermin</th>
                    <th>Endtermin</th>
                    <th>Trainer</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($courseDates as $courseDate)
                    <tr>
                        <td>{{ $courseDate->course?->kursName ?? '-' }}</td>
                        <td>{{ optional($courseDate->kursstarttermin)->format('d.m.Y H:i') ?? '-' }}</td>
                        <td>{{ optional($courseDate->kursendtermin)->format('d.m.Y H:i') ?? '-' }}</td>
                        <td>
                            @if ($courseDate->trainers->isEmpty())
                                -
                            @else
                                {{ $courseDate->trainers->map(function ($trainer) {
                                    $fullName = trim(($trainer->vorname ?? '').' '.($trainer->nachname ?? ''));
                                    return $fullName !== '' ? $fullName : ($trainer->name ?? '-');
                                })->implode(', ') }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Keine freien Termine vorhanden.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="section-title mt-5" data-aos="fade-in" data-aos-delay="50">
            <h2>Gebuchte Kurstermine</h2>
            <p>Wer hat gebucht?</p>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Kurs</th>
                    <th>Starttermin</th>
                    <th>Endtermin</th>
                    <th>Gebucht von</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($bookedCourseDates as $courseDate)
                    <tr>
                        <td>{{ $courseDate->course?->kursName ?? '-' }}</td>
                        <td>{{ optional($courseDate->kursstarttermin)->format('d.m.Y H:i') ?? '-' }}</td>
                        <td>{{ optional($courseDate->kursendtermin)->format('d.m.Y H:i') ?? '-' }}</td>
                        <td>{{ $courseDate->bookedBy ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Keine gebuchten Termine vorhanden.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
