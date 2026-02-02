<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $event = $this->getEvent();

        if ($event->id == null) {
            return view('pages.frontend.noEvent', compact('event'));
        }

        $faqs = Faq::query()
            ->where('is_active', true)
            // Nur FAQs der aktuellen EventGroup
            ->where('eventGroup_id', $event->eventGroup_id)
            // Zusätzlich: global (event_id NULL) oder spezifisch für das aktuelle Event
            ->where(function ($q) use ($event) {
                $q->whereNull('event_id')
                    ->orWhere('event_id', $event->id);
            })
            ->orderBy('category_sort_order')
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // Struktur passend zur bestehenden View: [ ['title' => ..., 'items' => [...]], ... ]
        $groups = $faqs
            ->groupBy('category')
            ->map(function ($items, $category) {
                return [
                    'title' => $category,
                    'items' => $items->map(function (Faq $faq) {
                        return [
                            'question' => $faq->question,
                            'answer_html' => $faq->answer_html,
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();

        return view('pages.frontend.faq', compact('event', 'groups'));
    }
}
