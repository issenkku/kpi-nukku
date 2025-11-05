<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    protected $fillable = [
        'variable_name',
        'label_name',
        'type',        // <-- matches migration
        'value',
        'indicator_id',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function formulas()
    {
        return $this->belongsToMany(Formula::class, 'variable_formulas');
    }
}
