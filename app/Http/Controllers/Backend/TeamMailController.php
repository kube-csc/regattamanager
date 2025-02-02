<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\RegattaTeam;
use Illuminate\Support\Facades\Mail;

class TeamMailController extends Controller
{
    public function TeamMeldungMail()
    {
        $regattateams = Regattateam::where('mannschaftsmail', 'M')->get();
        foreach ($regattateams as $regattateam) {
            $event = Event::find($regattateam->regatta_id);
            $eventEmail= $event->email;
            Mail::to($eventEmail)->send(new \App\Mail\TeamsRegistrationConfirmationMail($regattateam, $event));
            $teamEmail = $regattateam->email;
            Mail::to($teamEmail)->send(new \App\Mail\TeamsRegistrationConfirmationMail($regattateam, $event));
        }

        Regattateam::where('mannschaftsmail', 'M')
            ->update(['mannschaftsmail' => '']);

         return ;
    }
}
