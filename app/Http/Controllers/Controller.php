<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Faq;

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
            $event->id = null;

            // keine EventGroup/Event -> es kann keine passende FAQ geben
            $event->has_faqs = false;
            $event->has_faqs_event_id_null = false;
            $event->has_faqs_event_id_not_null = false;

            return $event;
        }

        // FAQ-Ermittlung (ausschließlich hier):
        // - nur aktuelle EventGroup
        // - event_id ist NULL (global) ODER = aktuelles Event (spezifisch)
        $eventGroupId = $event->eventGroup_id;
        $eventId = $event->id;

        $event->has_faqs_event_id_null = Faq::query()
            ->where('is_active', true)
            ->where('eventGroup_id', $eventGroupId)
            ->whereNull('event_id')
            ->exists();

        $event->has_faqs_event_id_not_null = Faq::query()
            ->where('is_active', true)
            ->where('eventGroup_id', $eventGroupId)
            ->whereNotNull('event_id')
            ->where('event_id', $eventId)
            ->exists();

        $event->has_faqs = ($event->has_faqs_event_id_null || $event->has_faqs_event_id_not_null);

        return $event;
    }
}
