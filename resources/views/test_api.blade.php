<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test Suite - Academic Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #0f172a; color: #f8fafc; font-family: 'Inter', system-ui, sans-serif; }
        .gold-accent { color: #fbbf24; }
        .bg-gold { background-color: #fbbf24; }
        .glass-panel { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(251, 191, 36, 0.2); }
        pre { background: #020617; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; overflow-y: auto; max-height: 300px; font-size: 0.875rem; color: #a5b4fc; border: 1px solid #1e293b; }
        .success { color: #4ade80; }
        .error { color: #f87171; }
        
        /* Custom Scrollbar for pre */
        pre::-webkit-scrollbar { width: 8px; height: 8px; }
        pre::-webkit-scrollbar-track { background: #0f172a; }
        pre::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        pre::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="p-4 md:p-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <header class="mb-10 text-center">
            <div class="inline-block p-4 rounded-full glass-panel mb-4">
                <svg class="w-10 h-10 gold-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
            </div>
            <h1 class="text-3xl md:text-5xl font-bold tracking-tight mb-3"><span class="gold-accent">REST API</span> Verification Suite</h1>
            <p class="text-slate-400 max-w-2xl mx-auto">Automated verification of secure academic endpoints. This suite acts as a real SPA client interacting with Laravel Sanctum.</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" id="test-container">
            <!-- Tests will be injected here -->
        </div>
        
        <footer class="mt-12 text-center text-slate-500 text-sm">
            <p>Module API REST (3.6) - Examen Final</p>
        </footer>
    </div>

    <script>
        const baseUrl = '/api';
        const tests = [
            { id: 'login-student', name: '1. Authenticate (Student)', endpoint: '/login', method: 'POST', body: { email: 'student@university.com', password: 'password' } },
            { id: 'get-modules', name: '2. Fetch Modules', endpoint: '/modules', method: 'GET', requiresAuth: true },
            { id: 'get-grades', name: '3. Fetch Grades (Student View)', endpoint: '/grades', method: 'GET', requiresAuth: true },
            { id: 'get-schedule', name: '4. Fetch Schedule (Student View)', endpoint: '/schedule', method: 'GET', requiresAuth: true },
            { id: 'get-absences', name: '5. Fetch Absences', endpoint: '/absences', method: 'GET', requiresAuth: true },
            { id: 'login-prof', name: '6. Authenticate (Professor)', endpoint: '/login', method: 'POST', body: { email: 'prof@university.com', password: 'password' } },
            { id: 'get-schedule-prof', name: '7. Fetch Schedule (Prof View)', endpoint: '/schedule', method: 'GET', requiresAuth: true },
            { id: 'get-grades-prof-fail', name: '8. Fetch Grades (Expect 403)', endpoint: '/grades', method: 'GET', requiresAuth: true }
        ];

        let currentToken = null;

        async function runTests() {
            const container = document.getElementById('test-container');

            for (const test of tests) {
                // Render UI box
                const card = document.createElement('div');
                card.className = 'glass-panel rounded-xl p-6 shadow-2xl transition-all duration-300 transform translate-y-4 opacity-0';
                card.innerHTML = `
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-white">${test.name}</h2>
                        <span class="px-3 py-1 rounded-full text-xs font-bold tracking-widest ${test.method === 'GET' ? 'bg-blue-500/20 text-blue-400' : 'bg-green-500/20 text-green-400'} border border-current">${test.method}</span>
                    </div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="text-xs px-2 py-1 bg-slate-800 rounded text-slate-300 font-mono flex-1 truncate">${baseUrl}${test.endpoint}</div>
                    </div>
                    <div id="status-${test.id}" class="text-sm font-medium animate-pulse text-yellow-400 mb-2 flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Executing Request...
                    </div>
                    <pre id="result-${test.id}">Waiting...</pre>
                `;
                container.appendChild(card);
                
                // Animate entry
                setTimeout(() => {
                    card.classList.remove('translate-y-4', 'opacity-0');
                }, 50);

                try {
                    const headers = {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    };
                    if (test.requiresAuth && currentToken) {
                        headers['Authorization'] = `Bearer ${currentToken}`;
                    }

                    // Artificial delay for visual effect
                    await new Promise(r => setTimeout(r, 600));

                    const start = performance.now();
                    const response = await fetch(`${baseUrl}${test.endpoint}`, {
                        method: test.method,
                        headers: headers,
                        body: test.body ? JSON.stringify(test.body) : null
                    });
                    const time = (performance.now() - start).toFixed(0);
                    
                    const data = await response.json();
                    
                    // If login test, save token
                    if (test.endpoint === '/login' && data.access_token) {
                        currentToken = data.access_token;
                    }

                    const statusEl = document.getElementById(`status-${test.id}`);
                    statusEl.classList.remove('animate-pulse', 'text-yellow-400');
                    if (response.ok) {
                        statusEl.classList.add('success');
                        statusEl.innerHTML = `<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Status: ${response.status} OK <span class="text-slate-500 ml-2 font-mono">(${time}ms)</span>`;
                    } else {
                        statusEl.classList.add('error');
                        statusEl.innerHTML = `<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Status: ${response.status} Error <span class="text-slate-500 ml-2 font-mono">(${time}ms)</span>`;
                    }

                    const preEl = document.getElementById(`result-${test.id}`);
                    preEl.textContent = JSON.stringify(data, null, 2);

                } catch (error) {
                    const statusEl = document.getElementById(`status-${test.id}`);
                    statusEl.className = 'text-sm font-medium error mb-2';
                    statusEl.innerHTML = `<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Fetch Error`;
                    
                    const preEl = document.getElementById(`result-${test.id}`);
                    preEl.textContent = error.toString();
                }
            }
        }

        // Start sequence
        setTimeout(runTests, 800);
    </script>
</body>
</html>
