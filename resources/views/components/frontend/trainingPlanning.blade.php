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
                    <th>Kurslänge</th>
                    <th>Starttermin</th>
                    <th>Endtermin</th>
                    <th>Startvorschlag</th>
                    <th>Endvorschlag</th>
                    <th>Startvorschlag Kunde</th>
                    <th>Endvorschlag Kunde</th>
                    <th>Trainer</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($courseDates as $courseDate)
                    <tr>
                        <td>{{ $courseDate->course?->kursName ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $courseDate->kurslaenge)->format('H:i') }}</td>
                        <td>{{ optional($courseDate->kursstarttermin)->format('d.m.Y H:i') ?? '-' }}</td>
                        <td>{{ optional($courseDate->kursendtermin)->format('d.m.Y H:i') ?? '-' }}</td>
                        <td>{{ optional($courseDate->kursstartvorschlag)->format('d.m.Y H:i') ?? '-' }}</td>
                        <td>{{ optional($courseDate->kursendvorschlag)->format('d.m.Y H:i') ?? '-' }}</td>
                        <td>{{ optional($courseDate->kursstartvorschlagkunde)->format('d.m.Y H:i') ?? '-' }}</td>
                        <td>{{ optional($courseDate->kursendvorschlagkunde)->format('d.m.Y H:i') ?? '-' }}</td>
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
                        <td colspan="9">Keine freien Termine vorhanden.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
