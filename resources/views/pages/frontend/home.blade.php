@section('title' ,$event->ueberschrift)

<x-frontend.layout>

<x-frontend.hero></x-frontend.hero>

<main id="main">

    @include('components.frontend.about');

</main><!-- End #main -->

</x-frontend.layout>
