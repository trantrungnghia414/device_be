<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\Log;


class GoogleController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(\Illuminate\Session\Middleware\StartSession::class);
    // }
   public function redirectToGoogle()
   {
        return Socialite::driver('google')->redirect();
   }   
   
// existing code...
// existing code...
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->email)->first();
            
            if (!$user) {
                return redirect(env('FRONTEND_URL') . '/login?error=invalid_credentials');
            }

            // Lấy thông tin quyền từ bảng roles thông qua role_id
            $role = Role::where('id', $user->role_id)->first();


                     
            
            if (!$role) {
                return redirect(env('FRONTEND_URL') . '/login?error=no_role_assigned');
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            
            // Trả về thông tin cho React
            $responseData = [
                'token' => $token,
                'role_name' => $role->name, // Tên quyền từ bảng roles
                'email' => $user->email,
                'username' => $user->name
            ];
            
            return redirect(env('FRONTEND_URL') . '/auth/callback?' . http_build_query($responseData));
            
        } catch (Exception $e) {
            Log::error('Google Login Error: ' . $e->getMessage());
            return redirect(env('FRONTEND_URL') . '/login?error=google_auth_failed');
        }
    }
}
