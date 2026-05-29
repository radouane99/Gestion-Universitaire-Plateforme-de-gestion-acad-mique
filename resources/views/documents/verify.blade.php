<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de Document</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-3xl shadow-xl max-w-lg w-full text-center border-t-4 border-green-500">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 text-green-500">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h1 class="text-2xl font-black text-gray-800 mb-2">Document Authentique</h1>
        <p class="text-gray-600 mb-8">Ce document a été vérifié avec succès par notre système officiel.</p>

        <div class="bg-gray-50 rounded-2xl p-6 text-left space-y-4">
            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase">Titulaire du document</span>
                <span class="block font-medium text-gray-800">{{ $request->user->name }}</span>
            </div>
            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase">Type de Document</span>
                <span class="block font-medium text-gray-800">{{ $request->type }}</span>
            </div>
            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase">Référence</span>
                <span class="block font-medium text-gray-800">REQ-{{ $request->id }}</span>
            </div>
            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase">Date d'approbation</span>
                <span class="block font-medium text-gray-800">{{ $request->updated_at->format('d/m/Y à H:i') }}</span>
            </div>
        </div>
        
        <div class="mt-8">
            <p class="text-xs text-gray-400">Système de Vérification UPF</p>
        </div>
    </div>
</body>
</html>

