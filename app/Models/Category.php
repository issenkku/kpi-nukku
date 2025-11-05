<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'max_score',
        'standard_id',
    ];

    protected $casts = [
        'max_score' => 'decimal:2',
    ];

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function indicators()
    {
        return $this->hasMany(Indicator::class, 'categorie_id');
    }
    
}
