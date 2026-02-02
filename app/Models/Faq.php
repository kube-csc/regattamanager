<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'category',
        'question',
        'answer_html',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'int',
        'is_active' => 'bool',
    ];
}
