<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Mail\NewsletterConfirmation;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:newsletters,email',
        ]);

        Newsletter::create($validated);

        // Send confirmation email
        Mail::to($validated['email'])->send(new NewsletterConfirmation($validated['name'] ?? null));

        return response()->json([
            'status' => 'success',
            'message' => 'Thank you for subscribing!'
        ]);
    }
} 