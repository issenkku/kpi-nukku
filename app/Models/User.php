<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;
    
    protected $fillable = [
        // ข้อมูลส่วนตัว
        'title',
        'first_name',
        'last_name',
        
        // ข้อมูลการทำงาน
        'positype',
        'workline',
        'posi',
        'level',
        
        // ข้อมูลติดต่อ
        'email',
        'phone',
        
        // ระบบ
        'password',
        'status',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }
    
    /**
     * Get the user's name (backward compatibility)
     * Alias for display_name
     */
    public function getNameAttribute()
    {
        return $this->display_name;
    }
    
    /**
     * Get the user's display name
     */
    public function getDisplayNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
    
    /**
     * Get the user's full name with title
     */
    public function getFullNameAttribute()
    {
        $parts = array_filter([$this->title, $this->first_name, $this->last_name]);
        return implode(' ', $parts);
    }

     public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }


    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'collector');
    }

    public function evidences()
    {
        return $this->hasMany(Evidence::class);
    }

}
