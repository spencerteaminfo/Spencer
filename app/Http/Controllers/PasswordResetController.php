<?php
namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Mail\ForgetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PasswordResetController extends Controller
{
    public function showLinkRequestForm() : View
    {
        return view('password.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => __('passwords.user')]);
        }

        $token = Password::broker()->createToken($user);
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        Mail::to($user->email)->send(new ForgetPassword($resetUrl, $user->email));

        return back()->with('status', __('passwords.sent'));
    }

    public function showResetForm(Request $request, string $token) : View
    {   

        return view('password.reset', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request) : RedirectResponse
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

        
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);

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
