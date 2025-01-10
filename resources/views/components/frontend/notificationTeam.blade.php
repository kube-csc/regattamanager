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
        <li>Bemerkung: {{ $regattaTeam->kommentar }}</li>
    </ul>

    @if($regattaTeam->mailen == 'a')
        <p>
            Über zukünftige Meldungen werden Sie per Email informiert.
        </p>
    @endif


    <h3>Möchten Sie eine weitere Meldung hinzufügen?</h3>
    <a href="{{ route('RegattaTeam.create') }}" class="btn btn-primary">Weitere Meldung</a>
    <br><br>

</div>
