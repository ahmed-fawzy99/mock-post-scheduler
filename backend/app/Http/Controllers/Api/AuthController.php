<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
//use App\Http\Resources\Api\V1\UserResource;
use App\Http\Requests\Api\Auth\UpdateInfoRequest;
use App\Http\Requests\Api\Auth\UpdatePasswordRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
//use App\Permissions\Api\v1\Permissions;
use App\Traits\Api\V1\ApiResponse;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules;


class AuthController extends Controller
{
    use ApiResponse;
    public function login(LoginRequest $request)
    {
        $request->validated($request->all());

        $request->authenticate();

        $request->session()->regenerate();

        return $this->ok('Login successful', new UserResource(auth()->user()));
    }
    public function register(RegisterRequest $request)
    {
        $request->validated($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'accepts_terms' => $request->accepts_terms,
        ]);

        $user->assignRole('user');

        event(new Registered($user));

        Auth::login($user);

        $user->sendEmailVerificationNotification();

        return $this->ok('Registration successful', new UserResource($user));

    }

    public function forgotPassword(Request $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $this->ok('Password reset link sent', null);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->string('password')),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $this->ok('Password reset successful', null);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->ok('Logout successful', null);
    }

    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->ok('Email already verified', null);
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->ok('Verification email sent', null);
    }

    public function updateUserInfo(UpdateInfoRequest $request)
    {
        $request->validated($request->all());

        auth()->user()->update($request->all());

        if ($request->has('email')) {
            auth()->user()->email_verified_at = null;
            auth()->user()->save();
            auth()->user()->sendEmailVerificationNotification();
        }

        return $this->ok('User info updated', new UserResource(auth()->user()));
    }

    public function updateUserPassword(UpdatePasswordRequest $request)
    {
        $request->validated($request->all());

        if (!Hash::check($request->password, auth()->user()->password)) {
            return $this->error('Current password is incorrect', null, 403);
        }

        auth()->user()->update([
            'password' => bcrypt($request->new_password),
        ]);

        return $this->ok('User password updated', new UserResource(auth()->user()));
    }

    public function getAuthUser()
    {
        return $this->ok('User is authenticated', new UserResource(auth()->user()));
    }

}
