<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist_item extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'required_items',
        'score',
        'description',
        'indicator_id',
    ];

    protected $casts = [
        'required_items' => 'array',
        'score'          => 'float',
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }
}
