Nr;Teamname;Verein / Firma;Strasse;PLZ;Ort;Telefon;E-Mmail;Anzahl Training;Wertung<br>
@foreach($regattateams as $regattateam)
    {{ $regattateam->laufende_nummer }};{{ $regattateam->teamname }};{{ $regattateam->verein }};{{ $regattateam->strasse }};{{ $regattateam->plz }};{{ $regattateam->ort }};{{ $regattateam->telefon }};{{ $regattateam->email }};{{ $regattateam->training }};{{ $regattateam->getRaceType->typ }}<br>
@endforeach
