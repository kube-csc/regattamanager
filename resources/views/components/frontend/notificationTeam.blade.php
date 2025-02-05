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
        <li>Werbung: {{ $regattaTeam->getRaceType->typ }}</li>
    </ul>
    <p>Team Beschreibung:<br>
        {{ $regattaTeam->beschreibung }}
    </p>
    <p>Information an den Veranstalter:<br>
        {!! $regattaTeam->kommentar !!}
    </p>

    @if($regattaTeam->mailen == 'a')
        <p>
            Über zukünftige Events möchtest du per E-Mail informiert werden.
        </p>
    @endif

    @if($regattaTeam->einverstaendnis == 1)
        <p>
            Du hast den Teilnahmebedingungen / Einverständniserklärung zugestimmt.
        </p>
    @else
        <p>
            Du hast den Teilnahmebedingungen / Einverständniserklärung nicht zugestimmt.
        </p>
    @endif

    <b>Möchtest du eine weiteres Team melden?</b><br>
    <a href="{{ route('RegattaTeam.create') }}" class="btn btn-primary">Weiteres Team melden</a><br>
    <br>
</div>
