<?php

namespace App\View\Components\Frontend;

use App\Http\Controllers\Controller as BaseController;
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
        // Event-Ermittlung inkl. FAQ-Flags erfolgt zentral im Controller
        $event = new class extends BaseController {};
        $event = $event->getEvent();

        return view('layouts.frontend', compact('event'));
    }
}
