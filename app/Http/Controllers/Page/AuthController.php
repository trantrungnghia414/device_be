<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();
        $role = $user->role_id;
        $role_name = Role::where('id', $role)->first()->name;
       
        if (Auth::attempt($credentials)) {
            // Generate token
            $token = $request->user()->createToken('auth_token')->plainTextToken;
           
            return response()->json([
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role_name' => $role_name,
                'token' => $token], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
