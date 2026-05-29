# UPF Portail - Progressive Web App (PWA) Documentation

## 📱 Overview

UPF Portail is now a fully-featured Progressive Web App (PWA) that provides:

- **Installable App**: Users can install the application on their devices (mobile, tablet, desktop)
- **Offline Support**: Core functionality works offline with service worker caching
- **App-like Experience**: Standalone fullscreen mode without browser UI
- **Push Notifications**: Capability for real-time notifications
- **Responsive Design**: Works seamlessly on all device sizes

## 🚀 Installation Methods

### Android & Desktop (Chrome, Edge, etc.)
1. Open the application in a compatible browser
2. Look for the "Install" prompt or button (appears automatically)
3. Click "Install" and confirm
4. The app will appear on your home screen/desktop

### iOS (iPhone/iPad)
1. Open the app in Safari
2. Tap the **Share** button (square with arrow)
3. Select **"Add to Home Screen"**
4. Enter a name (defaults to "UPF Portail")
5. Tap **"Add"**

## 📋 PWA Features Implemented

### 1. **Service Worker** (`/public/sw.js`)
- **Install**: Caches essential static assets
- **Fetch**: Implements smart caching strategies:
  - **Network-first**: API calls and HTML pages (try network, fallback to cache)
  - **Cache-first**: Static assets (CSS, JS, images, fonts)
- **Activate**: Cleans up old cache versions
- **Offline Fallback**: Serves `/offline.html` when offline

### 2. **Web App Manifest** (`/public/manifest.json`)
- **App Metadata**: Name, description, theme colors
- **Icons**: Multiple sizes (192x192, 512x512) with maskable support
- **Display Mode**: Standalone (fullscreen without browser UI)
- **Shortcuts**: Quick access to key pages (Dashboard, Grades, Absences)
- **Screenshots**: For app store listings
- **Categories**: Marked as educational and productivity app

### 3. **Offline Page** (`/public/offline.html`)
- Beautiful, responsive offline experience
- Shows connection status
- One-tap retry functionality
- Auto-refresh when connection restored
- Cached by service worker for offline access

### 4. **PWA Meta Tags** (in `resources/views/layouts/app.blade.php`)
```html
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1f2937">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="UPF Portail">
<link rel="apple-touch-icon" href="/icons/icon-192x192.png">
```

### 5. **Service Worker Registration** (in `resources/views/layouts/app.blade.php`)
- Automatic registration on app load
- Periodic update checks (hourly)
- Handles service worker updates gracefully
- Fires custom events when updates available

### 6. **PWA Icon Generator** (`/scripts/generate_pwa_icons.php`)
- Generates required PNG icons without GD library dependency
- Creates icons at multiple sizes:
  - 192x192 (standard)
  - 192x192 maskable (for icon shapes)
  - 512x512 (standard)
  - 512x512 maskable (for icon shapes)
- Generates placeholder screenshots for app stores

### 7. **App Install Manager** (`/resources/js/pwa-install-manager.js`)
- Detects if PWA can be installed
- Handles installation prompts
- Provides iOS installation guide
- Tracks installation events

## 🔄 Caching Strategies

### Network-First (for API and HTML pages)
```
1. Try to fetch from network
2. If successful, cache response and return
3. If network fails, return from cache
4. If no cache, return offline page (for navigation)
```

### Cache-First (for static assets)
```
1. Check if resource is in cache
2. If found, return from cache
3. If not cached, fetch from network
4. Cache the network response
5. On network failure, return offline response
```

## 🛠️ Maintenance & Updates

### Updating the Service Worker
Service workers are versioned in the cache names:
- `upf-portail-v1` - Main app cache
- `upf-portail-api-v1` - API response cache
- `upf-portail-assets-v1` - Static assets cache

To deploy a new version:
1. Update cache version numbers in `/public/sw.js`
2. The service worker will automatically clean up old caches
3. Users will be notified of the update on next visit

### Testing Service Worker

**Check if registered:**
```javascript
navigator.serviceWorker.getRegistrations().then(regs => {
    console.log(regs);
});
```

**Clear all caches:**
```javascript
caches.keys().then(cacheNames => {
    cacheNames.forEach(cacheName => {
        caches.delete(cacheName);
    });
});
```

**Unregister service worker:**
```javascript
navigator.serviceWorker.getRegistrations().then(regs => {
    regs.forEach(reg => reg.unregister());
});
```

## 📊 PWA Audit

To verify PWA compliance, use:
- **Chrome DevTools**: Lighthouse (Ctrl+Shift+I → Lighthouse)
- **Online Tools**: https://pwabuilder.com or https://web.dev/measure/

Key metrics to achieve 100%:
- ✅ Web app manifest present and valid
- ✅ Service worker registered and working
- ✅ Offline page implemented
- ✅ HTTPS enabled (production)
- ✅ Icons at required sizes
- ✅ Theme color configured
- ✅ Viewport meta tag present

## 🔒 Security Considerations

1. **HTTPS Requirement**: Service workers only work over HTTPS (except localhost)
2. **Content Security Policy**: Ensure CSP doesn't block service worker
3. **Cache Versioning**: Always version caches for secure updates
4. **API Authentication**: Cached API responses don't require re-authentication
5. **Sensitive Data**: Don't cache sensitive information; use network-only strategy

## 📚 Browser Support

| Feature | Chrome | Firefox | Safari | Edge |
|---------|--------|---------|--------|------|
| Service Worker | ✅ | ✅ | ⚠️ | ✅ |
| Web App Manifest | ✅ | ✅ | ✅ | ✅ |
| Add to Home Screen | ✅ | ⚠️ | ✅ | ✅ |
| Offline Support | ✅ | ✅ | ⚠️ | ✅ |
| Push Notifications | ✅ | ✅ | ⚠️ | ✅ |

**Note**: iOS has limited PWA support but can still be added via "Add to Home Screen"

## 🎯 Next Steps

### Phase 2: Enhanced PWA Features
- [ ] Push notifications for convocations
- [ ] Background sync for grade submissions
- [ ] Periodic checks for schedule updates
- [ ] Camera access for student ID verification

### Phase 3: Native Features
- [ ] Geolocation for campus presence detection
- [ ] File upload optimization for large documents
- [ ] Calendar integration with device calendar
- [ ] Deep linking for direct navigation

### Phase 4: Advanced Caching
- [ ] IndexedDB for structured data storage
- [ ] Selective sync for user data
- [ ] Delta updates for large datasets
- [ ] Memory management optimizations

## 📞 Troubleshooting

### App won't install
- Ensure you're on HTTPS (or localhost)
- Check if manifest.json is valid: Browser DevTools → Application → Manifest
- Verify minimum requirements: manifest.json, icons, theme-color, description

### Service Worker not working
- Enable in DevTools: Application → Service Workers
- Check browser console for errors
- Ensure `.js` files don't have PHP syntax errors
- Clear cache and re-register: `chrome://serviceworker-internals/`

### Offline page shows on online
- Clear service worker cache
- Check network tab for failed requests
- Verify manifest.json paths are correct

### Icons not showing
- Run `php scripts/generate_pwa_icons.php` to regenerate
- Check if `/public/icons/` directory exists
- Verify manifest.json paths match icon file names

## 📄 Files Modified/Created

```
✅ public/manifest.json              - PWA metadata
✅ public/sw.js                      - Service worker with caching
✅ public/offline.html               - Offline fallback page
✅ public/icons/                     - Generated PNG icons
✅ resources/views/layouts/app.blade.php - PWA meta tags & SW registration
✅ resources/js/pwa-install-manager.js   - Installation handling
✅ scripts/generate_pwa_icons.php    - Icon generation utility
```

## 🎓 Learning Resources

- [Web.dev PWA Guide](https://web.dev/progressive-web-apps/)
- [MDN Service Workers](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [PWA Builder](https://www.pwabuilder.com/)
- [Can I use... Progressive Web Apps](https://caniuse.com/pwa)

---

**Last Updated**: 2024
**Status**: Production Ready ✅
**Support**: For issues, check browser console and DevTools Application tab
