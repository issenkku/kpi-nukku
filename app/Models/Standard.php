<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Standard extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function indicators()
    {
        return $this->hasManyThrough(
            Indicator::class,
            Category::class,
            'standard_id',  // foreign key ใน categories ชี้ไป standards
            'categorie_id', // foreign key ใน indicators ชี้ไป categories (ชื่อคอลัมน์จริงใน DB)
            'id',           // primary key ใน standards (local key)
            'id'            // primary key ใน categories (local key)
        );
    }
}
