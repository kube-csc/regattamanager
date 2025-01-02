@section('title' ,$event->ueberschrift)

<x-frontend.layout>

<x-frontend.hero></x-frontend.hero>

<main id="main">

   <p style="text-align: center;">
         Unter der folgenden Domain {{parse_url(url('/'), PHP_URL_HOST)}} sind derzeit keine Veranstaltungen geplant.
   </p>
    <p style="height: 10px;"></p>

</main><!-- End #main -->

</x-frontend.layout>
