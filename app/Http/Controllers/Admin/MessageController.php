<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.messages.index', compact('messages'));
    }

    public function reply(ContactMessage $message)
    {
        return view('admin.messages.reply', compact('message'));
    }

    public function sendReply(Request $request, ContactMessage $message)
    {
        $request->validate([
            'reply_text' => 'required|string',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($message->email)->send(
                new \App\Mail\ReplyMessageMail($request->reply_text, $message)
            );
            
            $message->update(['status' => 'replied']);

            \App\Models\ActivityLog::log(
                'replied',
                'Message',
                "Réponse par email envoyée au visiteur '{$message->name}' (Sujet: {$message->subject})"
            );
            
            return back()->with('success', 'Votre réponse a été envoyée avec succès par email !');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur d'envoi de la réponse email : " . $e->getMessage());
            return back()->with('error', "Impossible d'envoyer la réponse par email : " . $e->getMessage() . ". Vérifiez vos paramètres SMTP (Gmail) dans le fichier .env.");
        }
    }

    public function destroy(ContactMessage $message)
    {
        $senderName = $message->name;
        $message->delete();

        \App\Models\ActivityLog::log(
            'deleted',
            'Message',
            "Message du visiteur '{$senderName}' supprimé."
        );

        return back()->with('success', 'Message supprimé avec succès.');
    }
}
