<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['role'])->get();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = new User();
        $user->role_id = $request->role_id;
        //$user->repair_team_id = $request->repair_team_id;
       // $user->username = $request->username;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->gender = $request->gender;
        $user->avatar = $request->avatar;
        $user->status = 0;
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['role'])->find($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $user->role_id = $request->role_id;
       // $user->repair_team_id = $request->repair_team_id;
        $user->username = $request->username;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->gender = $request->gender;
        $user->avatar = $request->avatar;
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json($user);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json($user);
    }

    /**
 * Toggle user status between active (1) and inactive (0)
 */
public function toggleStatus(string $id)
{
    $user = User::findOrFail($id);
    $user->status = $user->status === 1 ? 0 : 1;
    $user->save();
    
    return response()->json([
        'status' => $user->status,
        'message' => 'User status updated successfully'
    ]);
}


public function userProfile(Request $request)
{
    $user = User::where('email', $request->email)
        ->with('role')
        ->first();
    
    if ($user) {
        $userData = $user->toArray();
        $userData['avatar_url'] = $user->avatar ? asset('storage/' . $user->avatar) : null;
        return response()->json($userData);
    }

    return response()->json([
        'message' => 'User not found'
    ], 404);
}



public function updateProfile(Request $request)
{
    try {
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Check current password
        if ($request->has('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password does not match'
                ], 400);
            }
            $user->password = Hash::make($request->password);
        }

        // Update basic info
        $user->name = $request->name;
        $user->phone = $request->phone;
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Generate unique filename
            $fileName = time() . '_' . $request->file('avatar')->getClientOriginalName();
            
            // Store new avatar in public/avatars directory
            $request->file('avatar')->storeAs('avatars', $fileName, 'public');
            
            // Save path to database
            $user->avatar = 'avatars/' . $fileName;
        }

        $user->save();

        // Return full URL for avatar
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                ...$user->toArray(),
                'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error updating profile',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function updateProfile1(Request $request)
{
    try {
      

        // Find user by email
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password does not match'
            ], 400);
        }

        // Update user data
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->role_id = $request->role_id;
        $user->repair_team_id = $request->repair_team_id;
       
       
        $user->address = $request->address;
        $user->gender = $request->gender;
        if($request->avatar!=null){
            $user->avatar = $request->avatar;
        }
        $user->save();
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error updating profile',
            'error' => $e->getMessage()
        ], 500);
    }
}
}