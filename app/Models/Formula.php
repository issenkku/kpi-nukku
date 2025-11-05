<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    protected $fillable = [
        'condition',
        'indicator_id',
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function variables()
    {
        return $this->belongsToMany(Variable::class, 'variable_formulas', 'formula_id', 'variable_id');
    }
}
