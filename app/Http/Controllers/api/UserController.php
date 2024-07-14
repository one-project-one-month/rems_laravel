<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:agent,client',
        ]);

        $user = User::create($validated);

        if ($user->role == 'agent') {
            Agent::create([
                'user_id' => $user->id,
                'agency_name' => $request->input('agency_name'),
                'license_number' => $request->input('license_number'),
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $request->input('address'),
            ]);
        } elseif ($user->role == 'client') {
            Client::create([
                'user_id' => $user->id,
                'agent_id' => $request->input('agent_id'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $request->input('address'),
            ]);
        }

        return response()->json($user, 201);
    }
    public function index(){
        return User::all();
    }
}
