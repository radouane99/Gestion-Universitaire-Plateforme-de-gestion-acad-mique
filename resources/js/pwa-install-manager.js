// PWA App Install Manager
// Handles "Install App" button visibility and installation prompt

class PWAInstallManager {
    constructor() {
        this.deferredPrompt = null;
        this.installButton = null;
        this.init();
    }

    init() {
        // Listen for install prompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallButton();
            console.log('[PWA] Install prompt detected');
        });

        // Listen for successful app installation
        window.addEventListener('appinstalled', () => {
            console.log('[PWA] App successfully installed!');
            this.deferredPrompt = null;
            this.hideInstallButton();
            // Optionally track installation
            if (window.gtag) {
                gtag('event', 'app_installed');
            }
        });

        // Listen for app being added to home screen on iOS
        window.addEventListener('beforeinstallprompt', () => {
            if (window.navigator.standalone === true) {
                console.log('[PWA] App is running as PWA');
            }
        });
    }

    showInstallButton() {
        // Dispatch custom event for components to show install button
        const event = new CustomEvent('pwaShouldShowInstallButton', {
            detail: { manager: this }
        });
        window.dispatchEvent(event);
    }

    hideInstallButton() {
        const event = new CustomEvent('pwaShouldHideInstallButton');
        window.dispatchEvent(event);
    }

    async installApp() {
        if (!this.deferredPrompt) {
            console.warn('[PWA] Install prompt not available');
            return false;
        }

        try {
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('[PWA] User accepted installation');
                return true;
            } else {
                console.log('[PWA] User rejected installation');
                return false;
            }
        } catch (error) {
            console.error('[PWA] Installation error:', error);
            return false;
        } finally {
            this.deferredPrompt = null;
            this.hideInstallButton();
        }
    }

    isStandalone() {
        return window.navigator.standalone === true || 
               window.matchMedia('(display-mode: standalone)').matches;
    }

    isIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent);
    }

    showIOSInstallGuide() {
        const guide = `
📱 Pour installer UPF Portail sur votre iPhone:

1. Tapez sur Partager (icône carrée avec flèche)
2. Sélectionnez "Sur l'écran d'accueil"
3. Tapez "Ajouter"
4. L'app apparaîtra sur votre écran d'accueil

L'app fonctionnera hors-ligne et se synchronisera au redémarrage de la connexion.
        `.trim();
        
        alert(guide);
    }

    getAppStatus() {
        return {
            standalone: this.isStandalone(),
            supportsPWA: 'serviceWorker' in navigator && 'caches' in window,
            isIOS: this.isIOS(),
            userAgent: navigator.userAgent
        };
    }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.PWAManager = new PWAInstallManager();
    });
} else {
    window.PWAManager = new PWAInstallManager();
}

export default PWAInstallManager;
