<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use App\Events\UserRegistered;

class AuthController extends Controller
{
    /**
     * Display registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth/register');
    }

    /**
     * Display login form.
     */
    public function showLoginForm(): View
    {
        return view('auth/login');
    }

    /**
     * Registers new user and logs them in.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => [
                'required',
                'email',
                'unique:users',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                ],
        ]);

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new UserRegistered($user, ['ip' => $request->ip()]));

        Auth::login($user);
        return response()->json([
            'message' => 'Account created successfully.',
            'data' => $user
        ], 201);
    }

    /**
     * Responds to login requests, if valid credentials are passed in $request, it logs the user in.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
            ],
        ]);

        $attempt = Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        if ($attempt) {
            $request->session()->regenerate();

            $user = auth()->user();

            return response()->json([
                'message' => 'Login was successful.',
                'data' => $user
            ], 200);
        }

        return response()->json([
            'message' => 'No account matches with the given credentials.'
        ], 401);
    }

    /**
     * Logs out the currently authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully.'
        ], 200);
    }
}
