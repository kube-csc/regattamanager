<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegattaTeam extends Model
{
    protected $fillable = [
        'datum',
        'teamname',
        'verein',
        'teamcaptain',
        'strasse',
        'plz',
        'ort',
        'telefon',
        'email',
        'homepage',
        'status',
        'training',
        'regatta_id',
        'gruppe_id',
        'passwort',
        'bild',
        'pixx',
        'pixy',
        'beschreibung',
        'teamlink',
        'kommentar',
        'mannschaftsmail',
        'mailen',
        'mailendatum',
        'werbung',
        'einverstaendnis'
    ];

}
