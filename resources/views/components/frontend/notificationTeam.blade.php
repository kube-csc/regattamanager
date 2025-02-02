<div class="container">
    <h1>Meldung {{ $event->ueberschrift }}</h1>
    @if($success)
        <div class="alert alert-success">
            {{ $success }}
        </div>
    @endif

    <h2>Ihre Anmeldedaten:</h2>
    <ul>
        <li>Teamname: {{ $regattaTeam->teamname }}</li>
        <li>Verein: {{ $regattaTeam->verein }}</li>
        <li>Teamcaptain: {{ $regattaTeam->teamcaptain }}</li>
        <li>Strasse: {{ $regattaTeam->strasse }}</li>
        <li>PLZ: {{ $regattaTeam->plz }}</li>
        <li>Ort: {{ $regattaTeam->ort }}</li>
        <li>Telefon: {{ $regattaTeam->telefon }}</li>
        <li>Email: {{ $regattaTeam->email }}</li>
        <li>Homepage: {{ $regattaTeam->homepage }}</li>
        <li>Werbung: {{ $wertung->typ }}</li>
        <br>
        <li>Team Beschreibung: {{ $regattaTeam->beschreibung }}</li>
        <br>
        <li>Information an den Veranstalter: {!! $regattaTeam->kommentar !!}</li>
    </ul>

    @if($regattaTeam->mailen == 'a')
        <p>
            Über zukünftige Events werden Sie per Email informiert.
        </p>
    @endif

    @if($regattaTeam->einverstaendnis == 1)
        <p>
            Sie haben den Teilnahmebedingungen / Einverständniserklärung zugestimmt.
        </p>
    @else
        <p>
            Sie haben den Teilnahmebedingungen / Einverständniserklärung nicht zugestimmt.
        </p>
    @endif

    <h3>Möchten Sie eine weitere Team melden?</h3>
    <a href="{{ route('RegattaTeam.create') }}" class="btn btn-primary">Weiteres Team melden</a><br>
    <br>
</div>
