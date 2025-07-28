<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'privacy' => 'required|accepted',
        ]);

        try {
            // Send email to srinu.vitam@gmail.com
            Mail::to('srinu.vitam@gmail.com')->send(new ContactMail($validated));

            return response()->json([
                'status' => 'success',
                'message' => 'Thank you for your message! We will get back to you soon.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Contact form email failed: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Sorry, there was an error sending your message. Please try again or contact us directly.'
            ], 500);
        }
    }
} 