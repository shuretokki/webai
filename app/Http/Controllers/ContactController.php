<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessContactForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $key = 'contact-form:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Too many contact attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        RateLimiter::hit($key, 3600);

        ProcessContactForm::dispatch(
            $validated['name'],
            $validated['email'],
            $validated['message'],
            $validated['company'] ?? null
        );

        return back()->with('success', 'Thank you! Your message has been sent successfully.');
    }
}
