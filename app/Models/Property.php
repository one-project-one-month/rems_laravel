<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $fillable = [
        "address",
        "city",
        "state",
        "zip_code",
        "property_type",
        "price",
        "size",
        "number_of_bedrooms",
        "number_of_bathrooms",
        "year_built",
        "description",
        "status",
        "date_listed",
    ];
}