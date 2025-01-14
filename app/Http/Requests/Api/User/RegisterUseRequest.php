<?php

namespace App\Http\Requests\Api\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class   RegisterUseRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:80'],
            'email' => ['required', 'email', 'max:80', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:8', 'max:60'],
        ];
    }

    /**
     * @return void
     * @throws ValidationException
     */
    public function registerUser(): void
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $this->get('name'),
                'email' => $this->get('email'),
                'password' => Hash::make($this->get('password')),
            ]);

            DB::commit();
            $this->user = $user;
        } catch (\Exception $e) {
            \Log::info('sign-up', [$e->getMessage()]);
            DB::rollBack();

            throw ValidationException::withMessages([__('Something went wrong, please try again')]);
        }
    }
}
