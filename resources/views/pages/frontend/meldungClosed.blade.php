@section('title' ,'Meldung')

<x-frontend.layout>

<main id="main">

    <div style="max-width: 900px; margin: 10px auto; padding: 10px; border: 1px solid #f0ad4e; background: #fcf8e3; color: #8a6d3b;">
        {{ $message ?? 'Die Meldung ist derzeit nicht möglich.' }}
    </div>

</main><!-- End #main -->

</x-frontend.layout>
