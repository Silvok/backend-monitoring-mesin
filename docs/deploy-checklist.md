# Deploy Checklist

## 1) Pre-handover (Developer / Pemilik Repo)
Pastikan ini sudah beres sebelum repo dikirim ke tim perusahaan:

1. `php artisan test` lulus tanpa gagal.
2. Tidak ada migrasi pending (`php artisan migrate:status`).
3. Migrasi aman dijalankan di DB baru maupun DB existing.
4. Role default terisi (Super Admin, Admin, Koordinator).
5. Fitur utama lolos uji manual (dashboard, analisis, alert, export PDF/CSV).
6. Konfigurasi contoh production sudah jelas di `.env.example`.
7. Dokumen setup realtime/queue tersedia (`WEBSOCKET_SETUP.md`, checklist ini).

## 2) Handover Ke Tim Perusahaan (Infra/Deploy)
Bagian ini biasanya dikerjakan tim perusahaan di server mereka:

1. Isi `.env` production (`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` domain produksi).
2. Setup DB production + jalankan migrasi/seed.
3. Setup process manager untuk queue worker dan websocket server (jika realtime aktif).
4. Setup web server (Nginx/Apache), SSL, firewall, backup, dan monitoring.
5. Buat symlink storage (`php artisan storage:link`) di server target.
6. Jalankan cache optimasi production (`config:cache`, `route:cache`, `view:cache`).

## 3) Manual Checklist (Before Go-Live)
1. PDF laporan bulanan bisa dibuka dan tidak korup.
2. Login semua role: Super Admin, Admin, Koordinator.
3. Dashboard real-time tampil (data masuk).
4. Threshold di UI dan backend konsisten (Warning 25 / Critical 28).
5. API menerima data sensor dan menyimpan ke DB.
6. Proses analisis berjalan (status berubah Normal/Warning/Critical).
7. Alert management muncul dan bisa di-acknowledge/resolve.
8. Export CSV & PDF berhasil.
9. Tampilan responsif di HP (tidak pecah).
10. Tidak ada error baru di `storage/logs/laravel.log`.

## 4) Automated Checklist
Jalankan:

```bash
php scripts/deploy_checklist.php
php scripts/validate_system.php
```

Jika belum ada data sensor live (untuk kebutuhan demo/checklist), bisa isi data dummy 24 jam:

```bash
php scripts/seed_dummy_recent_raw_samples.php --samples=72 --hours=24
```

Yang dicek otomatis:
- `.env`, `APP_KEY`, `APP_ENV`, `APP_DEBUG`, `APP_URL`
- `BROADCAST_CONNECTION` untuk kebutuhan realtime
- writable path (`storage`, `bootstrap/cache`) dan link `public/storage`
- koneksi DB + tabel utama
- data dasar mesin/analysis/raw samples + role
- pending migration
- scan log error
- dependency/export view laporan
