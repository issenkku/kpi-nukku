<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variable_formula extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'variable_id',
        'formula_id',
    ];

    public function variable()
    {
        return $this->belongsTo(Variable::class);
    }

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }
}
