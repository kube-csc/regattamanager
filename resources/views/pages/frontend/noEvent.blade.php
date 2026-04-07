@section('title' ,$event->ueberschrift)

<x-frontend.layout>

<main id="main">

   <p style="text-align: center;">
         Unter der Domain {{ parse_url(url('/'), PHP_URL_HOST) }} sind aktuell keine Veranstaltungen geplant.<br>
         Prüfe bitte, ob die Domain in der Eventgruppe korrekt hinterlegt ist.
   </p>
    <p style="height: 10px;"></p>

</main><!-- End #main -->

</x-frontend.layout>
