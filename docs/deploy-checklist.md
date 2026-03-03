# Deploy Checklist

## Manual Checklist (to review before deploy/demo)
1. PDF laporan bulanan bisa dibuka dan tidak korup.
2. Login semua role: Super Admin, Admin, Koordinator.
3. Dashboard real-time tampil (data masuk).
4. Threshold di UI dan backend konsisten (Warning 25 / Critical 28).
5. API menerima data sensor dan menyimpan ke DB.
6. Proses analisis berjalan (status berubah Normal/Warning/Critical).
7. Alert management muncul dan bisa di-acknowledge/resolve.
8. Export CSV & PDF berhasil.
9. Tampilan responsif di HP (tidak pecah).
10. Tidak ada error di `storage/logs/laravel.log`.

## Automated Checklist
Jalankan:

```bash
php scripts/deploy_checklist.php
```

Yang dicek otomatis:
- .env, APP_KEY, APP_ENV, APP_DEBUG
- storage/ dan bootstrap/cache writable
- Koneksi DB + tabel utama ada
- Data mesin / analysis / raw samples ada
- Roles sudah terisi
- Laraval log tidak penuh error
- Dompdf terpasang
- Template laporan bulanan ada

