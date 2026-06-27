<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserAuthService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function __construct(protected UserAuthService $authService) {}

    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'login_method' => ['required', Rule::in(['email', 'phone'])],
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'email' => ['nullable', 'email', 'max:255'],
        ];

        if ($request->login_method === 'email') {
            $rules['login'][] = 'email';
        } else {
            $rules['login'][] = 'min:7';
        }

        $validated = $request->validate($rules);

        $user = $this->authService->register([
            'login_method' => $validated['login_method'],
            'login' => $validated['login'],
            'password' => $validated['password'],
            'name' => $validated['name'],
            'email' => $validated['login_method'] === 'email' ? $validated['login'] : ($validated['email'] ?? null),
            'phone' => $validated['login_method'] === 'phone' ? $validated['login'] : null,
        ]);

        event(new Registered($user));

        return redirect(route('home', absolute: false));
    }
}
