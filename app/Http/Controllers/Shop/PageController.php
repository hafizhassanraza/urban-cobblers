<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function about(): Response
    {
        return Inertia::render('Shop/About');
    }

    public function contact(): Response
    {
        return Inertia::render('Shop/Contact');
    }

    public function sizeGuide(): Response
    {
        return Inertia::render('Shop/SizeGuide');
    }

    public function journal(): Response
    {
        return Inertia::render('Shop/Journal');
    }

    public function storeContact(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ContactMessage::create($request->only(['name', 'email', 'subject', 'message']));

        return back()->with('success', 'Thank you! We\'ll get back to you within 24 hours.');
    }
}
