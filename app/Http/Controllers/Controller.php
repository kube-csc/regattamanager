<?php

namespace App\Http\Controllers;

use App\Models\Event;

abstract class Controller
{
    public function getEvent()
    {
        $currentDomain = parse_url(url('/'), PHP_URL_HOST);
        $currentDomain = str_replace('www.', '', $currentDomain);

        $event = Event::whereHas('eventGroup', function ($query) use ($currentDomain) {
            $query->where('domain', $currentDomain);
        })
            ->orderBy('datumvon', 'desc')
            ->first();

        if (!$event) {
            $event = new \stdClass();
            $event->ueberschrift = "Keine Veranstaltung gefunden";
        }

        return $event;
    }
}
