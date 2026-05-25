<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $message = ContactMessage::create($request->all());

        try {
            \Illuminate\Support\Facades\Mail::to('admin@upf.ac.ma')->send(new \App\Mail\ContactMessageMail($message));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur envoi email de contact : ' . $e->getMessage());
        }

        return back()->with('success', __('Your message has been sent successfully. We will get back to you soon.'));
    }
}
