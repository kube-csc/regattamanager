<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public function eventGroup()
    {
        return $this->belongsTo(EventGroup::class, 'eventGroup_id');
    }
}
