<!-- Template Main CSS File abgeändert bei verschieden Ausgaben -->
<?php
$serverdomain = parse_url(url('/'), PHP_URL_HOST);
$serverdomain = str_replace('www.', '', $serverdomain);

$eventGroupHeader = DB::table('event_groups')
    ->where('domain', $serverdomain)
    ->where('visible', 1)
    ->orderby('id', 'desc')
    ->first();

$eventGroupHeaderBild = null;
if (!empty($eventGroupHeader?->headerBild)) {
    $eventGroupHeaderBild = str_replace('_', ' ', env('VEREIN_URL'))."/storage/groupEventHeader/".$eventGroupHeader->headerBild;
}

$eventGroupAccentColor = trim((string) ($eventGroupHeader?->accentColor ?? ''));
if ($eventGroupAccentColor === '') {
    $eventGroupAccentColor = null;
}
?>

<style>

    @if($eventGroupHeaderBild)
    #hero {
        width: 100%;
        height: 100vh;
        background: url("{{ $eventGroupHeaderBild }}") top center;
        background-size: cover;
        position: relative;
        margin-bottom: -90px;
    }
    @endif

@if($eventGroupAccentColor)

 #header {
        transition: all 0.5s;
        z-index: 997;
        transition: all 0.5s;
        padding: 15px 0;
        background: {{ $eventGroupAccentColor }};
    }

    #header.header-scrolled {
        background: {{ $eventGroupAccentColor }} !important;
        padding: 0px;
    }

    #footer .footer-top .footer-info {
        border-top: 4px solid {{ $eventGroupAccentColor }};
    }

    .about .content .about-btn {
        background: {{ $eventGroupAccentColor }};
    }

    .back-to-top {
        background: {{ $eventGroupAccentColor }};
    }

    /* @media (max-width: 768px) { */
    @media (max-width: 995px) {
        #header.header-scrolled {
            background: {{ $eventGroupAccentColor }} !important;
            padding: 15px 0;
        }

        #footer .footer-top .footer-info {
            border-top: 4px solid {{ $eventGroupAccentColor }};
        }

        .about .content .about-btn {
            background: {{ $eventGroupAccentColor }};
        }

        .back-to-top {
            background: {{ $eventGroupAccentColor }};
        }

    }
    @endif

</style>
