<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabele extends Model
{
    use HasFactory;

    public function tabeledataShows()
    {
        return $this->hasMany(Tabledata::class, 'tabele_id');
    }
}
