<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;


    protected $primaryKey = 'listing_id';
    protected $fillable = [
        'property_id',
        'agent_id', 
        'date_listed',
        'listing_price',
        'status',
        'description',
    ];
    public function property()
    {
        return $this->belongsto(Property::class, 'property_id');
    }

    public function agent()
    {
        return $this->belongsto(agent::class, 'agent_id');
    }
}
