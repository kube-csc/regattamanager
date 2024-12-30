<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class ImpressumController extends Controller
{
    public function getImpressumDaten()
    {
        return view('pages.frontend.impressum');
    }
}
