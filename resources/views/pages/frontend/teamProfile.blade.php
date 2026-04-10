@section('title', 'Mannschaftssteckbrief')

@section('head')
    @if($team && isset($nextTeamUrl))
        @php
            $minTime = config('presentation.times.team_profile', 10);
            $charsPerSec = config('presentation.times.chars_per_sec', 40);
            $beschreibung = strip_tags($team->beschreibung ?? '');
            $extraTime = $beschreibung ? ceil(strlen($beschreibung) / $charsPerSec) : 0;
            $refreshTime = $minTime + $extraTime;
        @endphp
        <meta http-equiv="refresh" content="{{ $refreshTime }};url={{ $nextTeamUrl }}">
    @endif
@endsection

<x-frontend.layout>

<main id="main">
    @if($team)
        <x-frontend.presentation.regatta-team-steckbrief
            :team="$team"
            :team-index="$teamIndex"
            :team-count="$teamCount"
            :participation-count="$participationCount"
            :last-results="$lastResults"
            :fallback-year="$fallbackYear"
            :prev-team-url="$prevTeamUrl"
            :next-team-url="$nextTeamUrl"
        />
    @else
        <div class="alert alert-warning">Keine Mannschaften vorhanden.</div>
    @endif
</main><!-- End #main -->

</x-frontend.layout>

