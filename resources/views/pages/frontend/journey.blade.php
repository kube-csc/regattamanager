@section('title' ,$event->ueberschrift.' - Anfahrt')

<x-frontend.layout>

    <main id="main">
        <div style="text-align: justify">
            @include('components.frontend.journey');
        </div>
    </main><!-- End #main -->

</x-frontend.layout>
