<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    protected array $keys = [
        'store_name',
        'store_email',
        'store_phone',
        'shipping_rate',
        'low_stock_threshold',
        'currency_symbol',
    ];

    public function index(): Response
    {
        $settings = [];
        foreach ($this->keys as $key) {
            $settings[$key] = Setting::get($key, '');
        }

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'store_email' => ['required', 'email', 'max:255'],
            'store_phone' => ['nullable', 'string', 'max:50'],
            'shipping_rate' => ['required', 'numeric', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'currency_symbol' => ['required', 'string', 'max:5'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Settings saved.');
    }
}
