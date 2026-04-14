# Checklist Validasi Data Monitoring Mesin

Dokumen ini dipakai untuk memastikan data dari ESP -> backend -> dashboard benar, konsisten, dan sesuai kebutuhan.

## 1. Informasi Uji

- Tanggal uji:
- Penguji:
- Mesin (`machine_id`):
- Lokasi mesin:
- Kondisi mesin saat uji:
- Catatan lingkungan (suhu, beban, dll):

## 2. Patokan Kebutuhan (Isi Dulu)

- RPM referensi mesin: `_____ rpm`
- Frekuensi referensi (RPM/60): `_____ Hz`
- Toleransi frekuensi: `_____ Hz` atau `_____ %`
- Batas RMS NORMAL: `< _____`
- Batas RMS WARNING: `_____ - _____`
- Batas RMS CRITICAL: `> _____`

## 3. Checklist Alur Data End-to-End

Centang `Ya/Tidak`, lalu beri catatan jika ada masalah.

| No | Poin Validasi | Ya/Tidak | Catatan |
|---|---|---|---|
| 1 | ESP mengirim data terus-menerus (serial/log tidak putus) |  |  |
| 2 | Nilai mentah sensor berubah saat mesin/sensor digerakkan |  |  |
| 3 | Endpoint ingest menerima request tanpa error |  |  |
| 4 | Data `raw_samples` bertambah di database |  |  |
| 5 | Data `analysis_results` bertambah di database |  |  |
| 6 | Dashboard AX/AY/AZ update realtime |  |  |
| 7 | Dashboard RMS/Peak/Frequency update realtime |  |  |
| 8 | Status NORMAL/WARNING/CRITICAL sesuai kondisi |  |  |
| 9 | Tidak ada nilai aneh (`null`, `NaN`, `0 terus`) |  |  |
| 10 | Delay data masih wajar (mis. <= 5 detik) |  |  |

## 4. Uji Skenario

### Skenario A: Mesin Diam / Stabil
- Harapan: getaran rendah, RMS stabil, status NORMAL.

| Parameter | Hasil Aktual | Sesuai (Ya/Tidak) | Catatan |
|---|---|---|---|
| AX/AY/AZ |  |  |  |
| RMS |  |  |  |
| Peak |  |  |  |
| Frequency |  |  |  |
| Status |  |  |  |

### Skenario B: Getaran Normal Operasional
- Harapan: nilai naik wajar, status tetap NORMAL atau WARNING ringan (sesuai threshold).

| Parameter | Hasil Aktual | Sesuai (Ya/Tidak) | Catatan |
|---|---|---|---|
| AX/AY/AZ |  |  |  |
| RMS |  |  |  |
| Peak |  |  |  |
| Frequency |  |  |  |
| Status |  |  |  |

### Skenario C: Getaran Tinggi / Anomali
- Harapan: RMS/Peak naik signifikan, status WARNING/CRITICAL muncul.

| Parameter | Hasil Aktual | Sesuai (Ya/Tidak) | Catatan |
|---|---|---|---|
| AX/AY/AZ |  |  |  |
| RMS |  |  |  |
| Peak |  |  |  |
| Frequency |  |  |  |
| Status |  |  |  |

## 5. Validasi Angka (Sampling Manual)

Ambil minimal 5 titik data, lalu bandingkan:
- Dashboard
- API
- Database
- (Opsional) hitung ulang manual (Excel/Python)

| Waktu | Dashboard RMS | API RMS | DB RMS | Selisih | Lolos (Ya/Tidak) |
|---|---:|---:|---:|---:|---|
|  |  |  |  |  |  |
|  |  |  |  |  |  |
|  |  |  |  |  |  |
|  |  |  |  |  |  |
|  |  |  |  |  |  |

Kriteria lolos yang disarankan:
- Selisih RMS/Peak <= 5%
- Selisih Frequency <= 3 Hz (atau <= 5%)

## 6. Query Cek Cepat (Opsional)

Contoh SQL sederhana:

```sql
-- Data sensor terbaru
SELECT id, machine_id, ax_g, ay_g, az_g, created_at
FROM raw_samples
WHERE machine_id = 1
ORDER BY id DESC
LIMIT 10;

-- Hasil analisis terbaru
SELECT id, machine_id, rms, peak_amp, dominant_freq_hz, condition_status, created_at
FROM analysis_results
WHERE machine_id = 1
ORDER BY id DESC
LIMIT 10;
```

## 7. Kesimpulan Uji

- Status validasi akhir: `LULUS / PERLU PERBAIKAN`
- Ringkasan masalah utama (jika ada):
- Aksi perbaikan:
- PIC tindak lanjut:
- Target tanggal perbaikan:

