<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAuthService
{
    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (strlen($digits) === 11 && str_starts_with($digits, '1')) {
            $digits = substr($digits, 1);
        }

        return $digits;
    }

    public function findByLogin(string $method, string $login): ?User
    {
        if ($method === 'email') {
            return User::where('email', strtolower(trim($login)))->first();
        }

        return User::where('phone', $this->normalizePhone($login))->first();
    }

    public function attemptLogin(string $method, string $login, string $password, bool $remember = false): User
    {
        $user = $this->findByLogin($method, $login);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        Auth::login($user, $remember);

        return $user;
    }

    public function register(array $data): User
    {
        $method = $data['login_method'];
        $login = trim($data['login']);

        $email = $method === 'email'
            ? strtolower($login)
            : (isset($data['email']) && $data['email'] ? strtolower(trim($data['email'])) : null);

        $phone = $method === 'phone'
            ? $this->normalizePhone($login)
            : (isset($data['phone']) && $data['phone'] ? $this->normalizePhone($data['phone']) : null);

        if ($email && User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                $method === 'email' ? 'login' : 'shipping_email' => 'This email is already registered.',
            ]);
        }

        if ($phone && User::where('phone', $phone)->exists()) {
            throw ValidationException::withMessages([
                $method === 'phone' ? 'login' : 'shipping_phone' => 'This phone number is already registered.',
            ]);
        }

        if (! $email && ! $phone) {
            throw ValidationException::withMessages([
                'login' => 'Email or phone is required to create an account.',
            ]);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'phone' => $phone,
            'address' => $data['address'] ?? null,
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return $user;
    }
}
