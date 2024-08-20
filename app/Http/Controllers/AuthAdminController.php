<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthAdminController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'email|required|string|max:255|unique:admins',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
 $abilities = ['admin'];
        $token = $admin->createToken('auth_token', $abilities)->plainTextToken;

        return response()->json([
            'data' => $admin,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
    
   

    public function login(Request $request)
    {
         // Validate the request data
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find the admin by email
        $admin = Admin::where('email', $credentials['email'])->first();

        // Check if admin exists and the password is correct
        if (! $admin || ! Hash::check($credentials['password'], $admin->password)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
        $admin = Admin::where('email', $request->email)->firstOrFail();
          $abilities = ['admin'];

        $token = $admin->createToken('auth_token',$abilities)->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

      public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'logout success'
        ]);
    }
}
