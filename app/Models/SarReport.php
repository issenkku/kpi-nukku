<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SarReport extends Model
{
    protected $fillable = [
        'year',
        'title',
        'section1', 'section2', 'section4',
        'standard_id', 'indicator_id', 'criteria_id',
        'created_by', 'updated_by'
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }
    public function indicators()
    {
        return $this->belongsToMany(Indicator::class, 'sar_report_indicator');
    }
    public function criteria()
    {
        return $this->belongsTo(Criteria::class);
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function evidences()
    {
        return $this->belongsToMany(Evidence::class, 'sar_report_evidence');
    }
}
