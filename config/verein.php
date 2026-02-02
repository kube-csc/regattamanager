<?php

return [
    'name' => str_replace('_', ' ', env('VEREIN_NAME', '')),
    'strasse' => str_replace('_', ' ', env('VEREIN_STRASSE', '')),
    'plz' => str_replace('_', ' ', env('VEREIN_PLZ', '')),
    'ort' => str_replace('_', ' ', env('VEREIN_ORT', '')),

    'canonical' => env('VEREIN_CANONICAL', ''),
];
