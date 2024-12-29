<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Instruction;

class DatenschutzerklärungController extends Controller

{
    public function getDatenschutzerklärungDaten()
    {
        $instructionSearch="Datenschutzerklärung";
        $search = str_replace('_' , ' ' , $instructionSearch);
        $instructions = Instruction::where('ueberschrift' , $search)->get();

        return view('pages.frontend.datenschutzerkärung')->with([
            'instructions' => $instructions
        ]);
    }
}
