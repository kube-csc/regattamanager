<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;

class ImpressumController extends Controller
{
    public function getImpressumDaten()
    {
        return view('pages.frontend.impressum');
    }
}
