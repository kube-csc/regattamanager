<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'organiser_id',
        'kursName',
        'kursBeschreibung',
        'kursKosten',
        'kursBezahlsystem',
        'visible',
        'trainer',
        'autor_id',
        'bearbeiter_id',
        'freigeber_id',
        'letzteFreigabe',
    ];

    public function courseDates(): HasMany
    {
        return $this->hasMany(CourseDate::class);
    }
}
