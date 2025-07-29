<?php

namespace App\Mail;

use AllowDynamicProperties;
use App\Models\RaceType;
use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
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
        $raceType = RaceType::find($this->regattateam->gruppe_id);

        if($this->regattateam->status == 'Warteliste'){
            $mailtext = 'ihr Team ' . $this->regattateam->teamname . ' wurde auf die Warteliste für ' . $this->event->ueberschrift . ' gesetzt.';
        }
        else{
            $mailtext = 'du hast das Team ' . $this->regattateam->teamname . ' für ' . $this->event->ueberschrift . ' gemeldet.';
        }

        $mailtext = $mailtext . '<br>';
        $mailtext = $mailtext . '<h2>Ihre Anmeldedaten:</h2>';
        $mailtext = $mailtext . '<ul>';
        $mailtext = $mailtext . '<li>Teamname: ' . $this->regattateam->teamname . '</li>';
        $mailtext = $mailtext . '<li>Verein: ' . $this->regattateam->verein . '</li>';
        $mailtext = $mailtext . '<li>Teamcaptain: ' . $this->regattateam->teamcaptain . '</li>';
        $mailtext = $mailtext . '<li>Strasse: ' . $this->regattateam->strasse . '</li>';
        $mailtext = $mailtext . '<li>PLZ: ' . $this->regattateam->plz . '</li>';
        $mailtext = $mailtext . '<li>Ort: ' . $this->regattateam->ort . '</li>';
        $mailtext = $mailtext . '<li>Telefon: ' . $this->regattateam->telefon . '</li>';
        $mailtext = $mailtext . '<li>Email: ' . $this->regattateam->email . '</li>';
        $mailtext = $mailtext . '<li>Homepage: ' . $this->regattateam->homepage . '</li>';
        $mailtext = $mailtext . '<li>Wertung: ' . $raceType->typ . '</li>';
        $mailtext = $mailtext . '<li>Beschreibung des Teams: ' . $this->regattateam->beschreibung . '</li>';
        $mailtext = $mailtext . '<li>Information an den Veranstalter: ' . $this->regattateam->kommentar . '</li>';
        $mailtext = $mailtext . '</ul>';

        if($this->event->emailAntwort <> '') {
            $mailtext = $mailtext . $this->event->emailAntwort . '<br>';
        }

        if ($this->event->einverstaendnis <> ''){
            $mailtext = $mailtext . $this->event->einverstaendnis . '<br>';

            if ($this->regattateam->einverstaendnis == 1) {
                $mailtext = $mailtext . "Du hast den Teilnahmebedingungen / Einverständniserklärung zugestimmt.<br><br>";
            } else {
                $mailtext = $mailtext . "Du hast den Teilnahmebedingungen / Einverständniserklärung nicht zugestimmt.<br><br>";
            }
        }

        if($this->regattateam->mailen == 'a') {
                $mailtext = $mailtext . "Du hast zugestimmt, dass wir Ihnen Informationen zu weiteren Events per Email zusenden dürfen.<br><br>";
        }
        else {
                $mailtext = $mailtext . "Du hast nicht zugestimmt, dass wir Ihnen Informationen zu weiteren Events per Email zusenden dürfen.<br><br>";
        }

        return new Content(
            markdown: 'emails.teams.teamsRegistrationConfirmation',
            with: [
                'mailtext'    => $mailtext,
                'regattaTeam' => $this->regattateam,
                'event'       => $this->event,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
