<x-mail::message>
# Anmeldung zum {{ $event->ueberschrift }}

Hallo {{ $regattaTeam->teamcaptain }},<br>

Du hast das Team {{ $regattaTeam->teamname }} zum {{ $event->ueberschrift }} gemeldet: <br> <br>


{!! $mailtext !!}

<!--
<x-mail::button :url="''">
Button Text
</x-mail::button>
-->

{{ env('APP_URL') }}<br>
{{ env('VEREIN_NAME') }}<br>
{{ env('VEREIN_STRASSE') }}<br>
{{ env('VEREIN_PLZ') }} {{ env('VEREIN_ORT') }}<br>
{{ env('VEREIN_TELEFON') }}<br>
{{ env('VEREIN_EMAIL') }}<br>
@include('textimport.mailImpressum')

</x-mail::message>
