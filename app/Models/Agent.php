<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Agent extends Model
{
    use HasApiTokens,HasFactory,Notifiable;
    protected $fillable = [
        'user_id',
        'agency_name',
        'license_number',
        'email',
        'phone',
        'address'
        
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
