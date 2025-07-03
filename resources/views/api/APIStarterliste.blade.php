Nr;Teamname;Verein / Firma;Wertung<br>
@foreach($regattateams as $regattateam)
    {{ $regattateam->laufende_nummer }};{{ $regattateam->teamname }};{{ $regattateam->verein }};{{ $regattateam->getRaceType->typ }}<br>
@endforeach
