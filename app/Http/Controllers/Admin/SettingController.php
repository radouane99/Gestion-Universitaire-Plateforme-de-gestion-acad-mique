<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first() ?? new Setting();
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'institution_name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:50',
            'official_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'exam_rules' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'inscription_start_date' => 'nullable|date',
            'inscription_end_date' => 'nullable|date',
            'reinscription_start_date' => 'nullable|date',
            'reinscription_end_date' => 'nullable|date',
        ]);

        $setting = Setting::first() ?? new Setting();
        
        $setting->institution_name = $request->institution_name;
        $setting->academic_year = $request->academic_year;
        $setting->official_email = $request->official_email;
        $setting->phone = $request->phone;
        $setting->address = $request->address;
        $setting->exam_rules = $request->exam_rules;
        $setting->inscription_start_date = $request->inscription_start_date;
        $setting->inscription_end_date = $request->inscription_end_date;
        $setting->reinscription_start_date = $request->reinscription_start_date;
        $setting->reinscription_end_date = $request->reinscription_end_date;

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $setting->logo_path = $request->file('logo')->store('settings', 'public');
        }

        if ($request->hasFile('signature')) {
            if ($setting->signature_path) {
                Storage::disk('public')->delete($setting->signature_path);
            }
            $setting->signature_path = $request->file('signature')->store('settings', 'public');
        }

        $setting->save();

        return redirect()->route('admin.settings.index')->with('success', 'Paramètres mis à jour avec succès.');
    }

    /**
     * Exécute les migrations de base de données par l'administrateur sans terminal.
     */
    public function runMigrations()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            \App\Models\ActivityLog::log('updated', 'System', "Migrations de la base de données exécutées manuellement via l'interface d'administration.");
            
            return redirect()->route('admin.settings.index')->with('success', 'Base de données mise à jour avec succès !<br><pre class="text-xs text-left bg-gray-900 text-green-400 p-3 rounded-lg mt-2 overflow-auto" style="font-family: monospace; line-height: 1.4;">' . e($output) . '</pre>');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')->with('error', 'Erreur lors de la migration : ' . $e->getMessage());
        }
    }
}
