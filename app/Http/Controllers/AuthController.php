<?php

namespace App\Http\Controllers;

use App\Core\UseCases\Users\CreateMemberUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private CreateMemberUser $createMemberUser;
    public function __construct(CreateMemberUser $createMemberUser){
        $this->createMemberUser = $createMemberUser;
    }

    public function register(Request $request)
    {
            $request->validate([
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|confirmed|min:8',
            ]);
    
     
        $user = $this->createMemberUser->execute($request->all());
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'auth_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'data' => $user,
            'auth_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    public function logout()
    {
        Auth::user()->token->delete();

        return response()->json([
            'message'=> 'logout'
            ],200);
    }
}
