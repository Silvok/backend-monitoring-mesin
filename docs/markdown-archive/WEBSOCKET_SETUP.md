# Laravel WebSocket (Reverb) Setup - Monitoring Mesin

## ✅ Instalasi Selesai

Laravel Reverb telah berhasil diinstal dan dikonfigurasi untuk real-time monitoring mesin.

## 📦 Package yang Terinstal

### Backend:
- `laravel/reverb` (v1.6.3) - Laravel official WebSocket server
- `pusher/pusher-php-server` (7.2.7) - Pusher protocol support

### Frontend:
- `laravel-echo` - Client WebSocket library
- `pusher-js` - Pusher JavaScript client

## 🔧 Konfigurasi

### 1. Environment Variables (.env)
```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=123456
REVERB_APP_KEY=laravel-monitoring-key
REVERB_APP_SECRET=laravel-monitoring-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 2. Files yang Dibuat/Diupdate

- ✅ `config/reverb.php` - Reverb configuration
- ✅ `config/broadcasting.php` - Broadcasting channels configuration
- ✅ `routes/channels.php` - Channel authorization routes
- ✅ `resources/js/echo.js` - Echo client configuration
- ✅ `app/Events/MachineStatusUpdated.php` - Event untuk broadcast status mesin
- ✅ `resources/views/dashboard.blade.php` - Dashboard dengan WebSocket listener

## 🚀 Cara Menjalankan

### 1. Start WebSocket Server (Reverb)
```bash
php artisan reverb:start
```

atau dalam mode debug:
```bash
php artisan reverb:start --debug
```

### 2. Start Laravel Development Server
```bash
php artisan serve
```

### 3. Build Frontend Assets (jika belum)
```bash
npm run dev
```
atau untuk production:
```bash
npm run build
```

## 📡 Cara Menggunakan Broadcasting

### Trigger Event dari Controller/Service:

```php
use App\Events\MachineStatusUpdated;
use App\Models\Machine;

// Contoh: Broadcast saat deteksi anomali
$machine = Machine::find(1);
broadcast(new MachineStatusUpdated($machine, 'anomaly', 'Getaran tinggi terdeteksi'));

// Contoh: Broadcast saat kondisi normal
broadcast(new MachineStatusUpdated($machine, 'normal'));
```

## 🎯 Fitur Real-time yang Tersedia

1. **Live Status Updates** - Dashboard otomatis update saat ada perubahan status mesin
2. **Real-time Notifications** - Notifikasi popup muncul saat anomali terdeteksi
3. **Auto Refresh Metrics** - Metrics dashboard diupdate secara real-time
4. **Visual Indicators** - Live indicator berubah warna berdasarkan status

## 📋 Event Structure

Event `MachineStatusUpdated` akan broadcast data berikut:
```javascript
{
    machine_id: 1,
    machine_name: "Mesin A",
    location: "Lantai 1",
    status: "anomaly" | "normal",
    prediction: "Getaran tinggi terdeteksi",
    timestamp: "2025-01-03 14:30:45"
}
```

## 🔍 Testing WebSocket

### 1. Buka Browser Console
Akses dashboard dan buka Developer Tools (F12) > Console

### 2. Test Manual Broadcast
Dari Tinker:
```bash
php artisan tinker
```

```php
$machine = App\Models\Machine::first();
broadcast(new App\Events\MachineStatusUpdated($machine, 'anomaly', 'Test anomaly'));
```

### 3. Cek Log
Dashboard akan menampilkan log di console:
- "Initializing WebSocket connection..."
- "WebSocket listeners registered"
- "Machine status update received: {...}"

## 🛠️ Troubleshooting

### WebSocket tidak connect:
1. Pastikan Reverb server berjalan: `php artisan reverb:start --debug`
2. Cek port 8080 tidak digunakan aplikasi lain
3. Periksa `.env` untuk REVERB_* variables

### Notifikasi tidak muncul:
1. Buka browser console dan cek error
2. Pastikan `npm run build` atau `npm run dev` sudah dijalankan
3. Clear cache browser (Ctrl+Shift+R)

### Event tidak terbroadcast:
1. Pastikan BROADCAST_CONNECTION=reverb di .env
2. Cek queue jika menggunakan `ShouldBroadcastNow`
3. Verifikasi Event implements `ShouldBroadcast`

## 📚 Dokumentasi

- [Laravel Broadcasting](https://laravel.com/docs/11.x/broadcasting)
- [Laravel Reverb](https://laravel.com/docs/11.x/reverb)
- [Laravel Echo](https://github.com/laravel/echo)

## 🎉 Next Steps

Untuk mengintegrasikan dengan sistem monitoring:
1. Tambahkan trigger broadcast di logic analisis data sensor
2. Broadcast saat threshold getaran terlampaui
3. Broadcast saat prediksi anomali dari model ML
4. Tambahkan channel untuk setiap mesin individual
5. Implementasi private channels untuk user-specific notifications
