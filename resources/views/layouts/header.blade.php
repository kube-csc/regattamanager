<!-- Template Main CSS File abgeändert bei verschiedene Ausgaben -->
@php
    $serverdomain   = parse_url(url('/'), PHP_URL_HOST);
    $abteilungStyl  = DB::table('event_groups')->where('domain' , $serverdomain)->first();
@endphp

<style>

@include('textimport.cssColor')

    @if ($abteilungStyl)
         @if($abteilungStyl->headerBild<>'')
            @php
             $bild = env('VEREIN_CANONICAL')."/storage/header/".$abteilungStyl->headerBild;
             //$bild = "/storage/header/".$abteilungStyl->headerBild;
            @endphp
            #hero {
                    width: 100%;
                    height: 100vh;
                    background: url("{{$bild}}") top center;
                    background-size: cover;
                    position: relative;
                    margin-bottom: -90px;
            }
         @endif
    @endif

</style>
