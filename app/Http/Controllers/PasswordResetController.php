<?php
namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

class PasswordResetController extends Controller
{
    public function showLinkRequestForm() : View
    {
        return view('password/forgot-password');
    }

    public function sendResetLinkEmail(request $request) : JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => __($status)], 200)
        : response()->json(['message' => __($status)], 400);
    }

    public function showResetForm(Request $request, string $token) : View
    {   

        return view('password.reset', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request) : JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $this->resetPassword($user, $password);
            }
        );
        $message = $status === Password::PASSWORD_RESET ? 200 : 400;
        return response()->json([
            'message' => $message
        ]);

    }

    protected function resetPassword(User $user, string $password)
    {
        $user->forceFill([
            'password' => Hash::make($password)
        ])->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));
    }
}
