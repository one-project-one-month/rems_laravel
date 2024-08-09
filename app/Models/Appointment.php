<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        "agent_id",
        "client_id",
        "property_id",
        "appointment_date",
        "appointment_time",
        "status",
        "notes",
    ];
}
