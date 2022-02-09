<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function index()
    {
        Validator::make(request()->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
        ])->validate();
        $user = User::where('email', 'LIKE', request()->input('email'))->first();
        if (!$user || !Hash::check(request()->input('password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credentials do not match our records.',
            ], 404);
        }
        $token = $user->createToken('my-app-token')->plainTextToken;
        return response([
            'success' => true,
            'message' => 'User Logged In Successfully.',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response([
            'success' => true,
            'message' => 'User Logged Out Successfully.',
        ], 201);
    }

    public function revokeTokensExceptCurrent(Request $request)
    {
        $currentToken = $request->user()->currentAccessToken();
        $request->user()->tokens()->where('id', '<>', $currentToken->id)->delete();
        return response([
            'success' => true,
            'message' => 'User Logged Out Successfully from all other devices.',
        ], 201);
    }

    public function revokeAllTokens(Request $request)
    {
        $request->user()->tokens()->delete();
        return response([
            'success' => true,
            'message' => 'User Logged Out Successfully from all devices.',
        ], 201);
    }
}
