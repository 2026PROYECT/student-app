<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        $accessToken = $user->createToken('app');
        return response()->json([
            'user' => $user->name,
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
        ]);
    }
    public function login(LoginRequest $request)
{
    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json([
            'errors' => ['email' => ['Las credenciales son incorrectas.']]
        ], 422);
    }

    // IMPORTANTE: Asegúrate de que el objeto $user que se devuelve 
    // es el que acabamos de traer de la base de datos.
    return response()->json([
        'user' => $user, 
        'token' => $user->createToken('auth_token')->plainTextToken,
    ]);
}
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        $result = $request->user()->token()->revoke();
        if ($result) {
            return response()->json(['code' => 200, 'message' => 'Logout success']);
        } else {
            return response()->json(['code' => 400, 'message' => 'Logout Failed']);
        }
    }
}
