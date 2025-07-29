<x-mail::message>
# Anmeldung zum {{ $event->ueberschrift }}

Hallo {{ $regattaTeam->teamcaptain }},
<br>
{!! $mailtext !!}

<!--
<x-mail::button :url="''">
Button Text
</x-mail::button>
-->
<br>
{{ env('APP_URL') }}<br>
{{ env('VEREIN_NAME') }}<br>
{{ env('VEREIN_STRASSE') }}<br>
{{ env('VEREIN_PLZ') }} {{ env('VEREIN_ORT') }}<br>
{{ env('VEREIN_TELEFON') }}<br>
{{ env('VEREIN_EMAIL') }}<br>
@include('textimport.mailImpressum')

</x-mail::message>
