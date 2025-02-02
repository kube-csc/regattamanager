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


{{ config('app.name') }}<br>
{{ env('VEREIN_NAME') }}
@include('textimport.mailImpressum')

</x-mail::message>
