<?php

namespace App\Mail;

use AllowDynamicProperties;
use App\Models\RaceType;
use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

#[AllowDynamicProperties] class TeamsRegistrationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($regattaTeam, $event)
    {
       $this->regattateam= $regattaTeam;
       $this->event      = $event;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->regattateam->teamname.' wurde für den '.$this->event->ueberschrift.' angemeldet',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
//        return new Content(
//            view: 'view.name',
//        );
        $raceType = RaceType::find($this->regattateam->gruppe_id);
        $mailtext = "";
        $mailtext = $mailtext . '<h2>Ihre Anmeldedaten:</h2>';
        $mailtext = $mailtext . '<ul>';
        $mailtext = $mailtext . '<li>Teamname: '.$this->regattateam->teamname.'</li>';
        $mailtext = $mailtext . '<li>Verein: '.$this->regattateam->verein.'</li>';
        $mailtext = $mailtext . '<li>Teamcaptain: '.$this->regattateam->teamcaptain.'</li>';
        $mailtext = $mailtext . '<li>Strasse: '.$this->regattateam->strasse.'</li>';
        $mailtext = $mailtext . '<li>PLZ: '.$this->regattateam->plz.'</li>';
        $mailtext = $mailtext . '<li>Ort: '.$this->regattateam->ort.'</li>';
        $mailtext = $mailtext . '<li>Telefon: '.$this->regattateam->telefon.'</li>';
        $mailtext = $mailtext . '<li>Email: '.$this->regattateam->email.'</li>';
        $mailtext = $mailtext . '<li>Homepage: '.$this->regattateam->homepage.'</li>';
        $mailtext = $mailtext . '<li>Wertung: '.$raceType->typ.'</li><br>';
        $mailtext = $mailtext . '<li>Beschreibung des Teams: '.$this->regattateam->beschreibung.'</li><br>';
        $mailtext = $mailtext . '<li>Information an den Veranstalter: '.$this->regattateam->kommentar.'</li>';
        $mailtext = $mailtext . '</ul><br>';
        $mailtext = $mailtext . $this->event->einverstaendnis.'<br><br>';

        if ($this->regattateam->einverstaendnis == 1) {
                $mailtext = $mailtext . "Sie haben den Teilnahmebedingungen / Einverständniserklärung zugestimmt.<br><br>";
        }
        else {
                $mailtext = $mailtext . "Sie haben den Teilnahmebedingungen / Einverständniserklärung nicht zugestimmt.<br><br>";
        }

        if($this->regattateam->mailen == 'a') {
                $mailtext = $mailtext . "Sie haben zugestimmt, dass wir Ihnen Informationen zu weiteren Events per Email zusenden dürfen.<br>";
        }
        else {
                $mailtext = $mailtext . "Sie haben nicht zugestimmt, dass wir Ihnen Informationen zu weiteren Events per Email zusenden dürfen.<br>";
        }

        return new Content(
            markdown: 'emails.teams.teamsRegistrationConfirmation',
            with: [
                'mailtext' => $mailtext,
                'regattaTeam' => $this->regattateam,
                'event' => $this->event,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
