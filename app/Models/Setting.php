<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'title',
        'notify_date1',
        'notify_time1',
        'notify_date2',
        'notify_time2',
        'message',
        'remind_days',
        'remind_time',
        'remind_enabled',
    ];

    public $timestamps = false;

    protected $casts = [
        'notify_date1' => 'date',
        'notify_date2' => 'date',
        'remind_enabled' => 'boolean',
    ];

    public static function getSetting()
    {
        return self::first();
    }
}
