<?php

namespace App\Models\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\Events\Registration;

class Event extends Model
{
    use HasFactory, HasUuids;


    protected $keyType = 'string'; 
    public $incrementing = false; 


    protected $fillable = [
        'id',
        'name',
        'start_time',
        'end_time',
        'max_participants',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
    

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'registrations', 'event_id', 'user_id')
                    ->withTimestamps();
    }
}
