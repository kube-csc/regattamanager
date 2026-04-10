<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lane extends Model
{
    use SoftDeletes; // Soft-Delete-Unterstützung aktivieren

    public function regattaTeam(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RegattaTeam::class, 'mannschaft_id');
    }

    public function getTableLane(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tabele::class, 'tabele_id');
    }
    public function race(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Race::class, 'rennen_id');
    }
}
