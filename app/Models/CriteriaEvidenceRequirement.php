<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriteriaEvidenceRequirement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'criteria_id',
        'name',
        'sequence',
    ];

    protected $table = 'criteria_evidence_requirements';

    public function criteria()
    {
        return $this->belongsTo(Criteria::class);
    }
}
