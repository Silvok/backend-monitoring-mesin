# Deploy + Import Historical Sensor Data

Dokumen ini untuk memastikan grafik historis muncul setelah deploy (staging/production), termasuk import CSV historis ke `raw_samples`.

## 1) Deploy Aplikasi

Jalankan di server aplikasi:

```bash
cd /path/to/backend-monitoring-mesin
git pull
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika server tidak butuh cache command tertentu (mis. saat debug), minimal jalankan `php artisan optimize:clear`.

## 2) Atur Retensi Data (Penting)

Jika import data lama (mis. Februari 2026), jangan biarkan retention raw data terlalu pendek karena akan dihapus scheduler.

Isi `.env` di server:

```env
SENSOR_RETENTION_RAW_SAMPLES_DAYS=365
SENSOR_RETENTION_RAW_3AXIS_DAYS=365
SENSOR_RETENTION_RAW_BATCHES_DAYS=365
SENSOR_RETENTION_TEMPERATURE_DAYS=365
SENSOR_RETENTION_ANALYSIS_DAYS=365
```

Lalu apply config:

```bash
php artisan config:clear
php artisan config:cache
```

Catatan: scheduler cleanup ada di `routes/console.php` (`sensor:cleanup-old-data` daily 02:00).

## 3) Import CSV Historical

Script import yang dipakai:

```bash
php scripts/import_sensor_history_csv.php \
  --file="/absolute/path/sensor_history_decanter_01_20260226_24h.csv" \
  --machine=1 \
  --date=2026-02-26 \
  --delete-existing=1 \
  --batch-size=1000
```

Penjelasan parameter:
- `--file`: path CSV di server.
- `--machine`: `machine_id` target (contoh `1` untuk `decanter_01`).
- `--date`: filter tanggal yang diimport (`YYYY-MM-DD`).
- `--delete-existing=1`: hapus dulu data existing untuk machine+tanggal yang sama.
- `--batch-size`: ukuran insert per batch.

## 4) Verifikasi Setelah Import

### SQL cek jumlah data tanggal tertentu

```sql
SELECT COUNT(*) AS total
FROM raw_samples
WHERE machine_id = 1
  AND created_at >= '2026-02-26 00:00:00'
  AND created_at <  '2026-02-27 00:00:00';
```

### Cek endpoint yang dipakai halaman

```bash
curl -H "Accept: application/json" \
  "https://domain-kamu/api/machine/1/historical-data?date=2026-02-26&hours=24"
```

Pastikan response `success=true` dan `original_count > 0`.

## 5) Validasi di UI

1. Login user yang punya permission `realtime.view`.
2. Buka halaman `Real-time Sensor`.
3. Pilih mesin `decanter_01`.
4. Mode `Historis`.
5. Pilih tanggal `26/02/2026`, range `Full Day (24h)`.
6. Hard refresh browser (`Ctrl+F5`) bila grafik belum update.
