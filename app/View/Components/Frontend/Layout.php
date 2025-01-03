<?php

namespace App\View\Components\Frontend;

use App\Models\Event;
use Illuminate\View\Component;

class Layout extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $currentDomain = parse_url(url('/'), PHP_URL_HOST);
        $currentDomain = str_replace('www.', '', $currentDomain);

        $event = Event::whereHas('eventGroup', function ($query) use ($currentDomain) {
            $query->where('domain', $currentDomain);
        })
            ->orderBy('datumvon', 'desc')
            ->first();

            return view('layouts.frontend' , compact('event'));
    }
}
