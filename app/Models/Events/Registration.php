<?php

namespace App\Models\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;
use App\Models\Events\Event;

class Registration extends Model
{
    
    use HasUuids;

    protected $keyType = 'string'; 
    public $incrementing = false; 

    protected $fillable = [
        'event_id',
        'user_id',
    ];


    public function event()
    {
        return $this->belongsTo(Event::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
