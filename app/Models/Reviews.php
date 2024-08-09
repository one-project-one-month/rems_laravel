<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Property;
use App\Models\User;

class Reviews extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function properties()
    {
        return $this->hasOne(Property::class, 'id','property_id');
    }

    public function users()
    {
        return $this->hasOne(User::class, 'id','user_id');
    }

}
