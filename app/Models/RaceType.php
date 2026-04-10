<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceType extends Model
{
    public function template()
    {
        // race_types.race_type_template_id -> race_type_templates.id
        return $this->belongsTo(RaceTypeTemplate::class, 'race_type_template_id');
    }
}
