<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Instruction;

class DatenschutzerklaerungController extends Controller

{
    public function getDatenschutzerklaerungDaten()
    {
        $instructionSearch="Datenschutzerklärung";
        $search = str_replace('_' , ' ' , $instructionSearch);
        $instructions = Instruction::where('ueberschrift' , $search)->get();

        return view('pages.frontend.datenschutzerkaerung')->with([
            'instructions' => $instructions
        ]);
    }
}
