<?php

namespace App\Http\Requests\Api\User;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthenticateRequest extends FormRequest
{
    public User $user;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $user = User::where('email', $this->get('email'))->first();
        $this->checkLoginLimit();

        if (! $user) {
            RateLimiter::hit($this->throttleKey());
            $this->checkLoginLimit();
            throw  ValidationException::withMessages([
                'error' => __('auth.wrong_email_password'),
            ]);
        }

        if (! Hash::check($this->get('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());
            $this->checkLoginLimit();

            throw ValidationException::withMessages([
                'error' => __('auth.wrong_email_password'),
            ]);
        }

        $this->user = $user;

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * @return void
     * @throws ValidationException
     */
    private function checkLoginLimit(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 60)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'error' => __('auth.authenticate_retries'),
            'access_after' => Carbon::now()->addSeconds($seconds),
        ]);
    }

    /**
     * @return string
     */
    private function throttleKey(): string
    {
        return 'user-login|'.$this->ip();
    }
}
