<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseDate extends Model
{
    protected $table = 'coursedates';

    protected $casts = [
        'kursstarttermin' => 'datetime',
        'kursendtermin' => 'datetime',
        'kursstartvorschlag' => 'datetime',
        'kursendvorschlag' => 'datetime',
        'kursstartvorschlagkunde' => 'datetime',
        'kursendvorschlagkunde' => 'datetime',
        'kursNichtDurchfuerbar' => 'boolean',
    ];

    protected $fillable = [
        'course_id',
        'organiser_id',
        'kurslaenge',
        'kursstarttermin',
        'kursendtermin',
        'kursstartvorschlag',
        'kursendvorschlag',
        'kursstartvorschlagkunde',
        'kursendvorschlagkunde',
        'kursNichtDurchfuerbar',
        'kursKosten',
        'kursBezahlsystem',
        'sportgeraetanzahl',
        'kursFahrtenlaenge',
        'kursInformation',
        'bearbeiter_id',
        'autor_id',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function trainers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coursedate_user', 'coursedate_id', 'user_id');
    }
}
