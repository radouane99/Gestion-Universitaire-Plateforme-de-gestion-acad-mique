<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\TotpHelper;
use App\Models\ActivityLog;

class AdminTwoFactorController extends Controller
{
    /**
     * Show the 2FA challenge screen.
     */
    public function showChallenge()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin() || !$user->google2fa_enabled) {
            return redirect()->route('dashboard');
        }

        if (session()->get('admin_2fa_verified')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the submitted 2FA challenge code.
     */
    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        if (!$user || !$user->isAdmin() || !$user->google2fa_enabled) {
            return redirect()->route('dashboard');
        }

        if (TotpHelper::verifyCode($user->google2fa_secret, $request->code)) {
            session(['admin_2fa_verified' => true]);
            
            ActivityLog::log('login', 'User', "Double authentification 2FA réussie pour l'administrateur #{$user->id}.");
            
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['code' => 'Le code de sécurité saisi est incorrect ou a expiré.']);
    }

    /**
     * Initialize 2FA activation in the profile.
     */
    public function initSetup(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        // Generate temporary secret and store it in session
        $secret = TotpHelper::generateSecret();
        session(['temp_2fa_secret' => $secret]);

        $qrCodeUrl = TotpHelper::getQrCodeUrl($user->email, $secret);

        return response()->json([
            'status' => 'success',
            'secret' => $secret,
            'qr_code_url' => 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($qrCodeUrl)
        ]);
    }

    /**
     * Confirm 2FA activation in the profile.
     */
    public function confirmSetup(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $secret = session()->get('temp_2fa_secret');
        if (!$secret) {
            return back()->with('error', 'Session expirée. Veuillez réinitialiser l\'activation du 2FA.');
        }

        if (TotpHelper::verifyCode($secret, $request->code)) {
            $user->update([
                'google2fa_secret' => $secret,
                'google2fa_enabled' => true,
            ]);

            session()->forget('temp_2fa_secret');
            session(['admin_2fa_verified' => true]);

            ActivityLog::log('updated', 'User', "Double authentification 2FA activée sur le compte administrateur #{$user->id}.");

            return back()->with('success', 'Félicitations ! La double authentification a été activée et validée avec succès sur votre compte.');
        }

        return back()->with('error', 'Le code de validation saisi est incorrect. Veuillez vérifier votre application d\'authentification.');
    }

    /**
     * Disable 2FA on profile.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        // Confirm identity via password before disabling security
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Le mot de passe saisi est incorrect.']);
        }

        $user->update([
            'google2fa_secret' => null,
            'google2fa_enabled' => false,
        ]);

        session()->forget('admin_2fa_verified');

        ActivityLog::log('updated', 'User', "Double authentification 2FA désactivée sur le compte administrateur #{$user->id}.");

        return back()->with('success', 'La double authentification a été désactivée avec succès.');
    }
}
