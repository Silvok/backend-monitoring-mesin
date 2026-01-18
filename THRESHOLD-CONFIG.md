# Threshold Configuration - Backend System

## 📋 Overview

Dokumen ini menjelaskan konfigurasi threshold yang digunakan di sistem backend monitoring mesin untuk dashboard dan API endpoints.

**Standar yang digunakan**: **ISO 10816-3** (Mechanical Vibration - Evaluation of Machine Vibration)

---

## ⚙️ Current Threshold Configuration

### ISO 10816-3 Standard (Velocity - mm/s)

**Kategori Mesin**: Class II (Medium Machines: 15-75 kW)

| Zone | RMS Range (mm/s) | Visual Indicator | Status | Tindakan |
|------|------------------|------------------|--------|----------|
| **Zone A** | 0.0 - 2.8 | ✅ Green | Normal | Operasi normal |
| **Zone B** | 2.8 - 7.1 | ⚠️ Yellow | Warning | Perlu monitoring |
| **Zone C** | 7.1 - 11.2 | 🟠 Orange | Unsatisfactory | Jadwalkan maintenance |
| **Zone D** | > 11.2 | 🚨 Red | Danger | Stop mesin segera |

**Referensi**: ISO 10816-3:2009 - Table B.1

---

## 📁 Files Updated

### 1. Dashboard View
**File**: `backend/resources/views/dashboard.blade.php`

**Threshold Display**:
```blade
✅ Normal: 0-2.8 mm/s | ⚠️ Waspada: 2.8-7.1 mm/s | 🚨 Bahaya: >7.1 mm/s
```

### 2. Dashboard Controller
**File**: `backend/app/Http/Controllers/DashboardController.php`

```php
// ISO 10816-3 Thresholds (mm/s) for Medium Machines (Class II)
$thresholds = ['warning' => 2.8, 'critical' => 7.1];
```

### 3. API Controller
**File**: `backend/app/Http/Controllers/Api/DashboardApiController.php`

```php
// ISO 10816-3 Thresholds (mm/s) for Medium Machines
if ($analysis->rms >= 7.1) {
    $severity = 'critical';  // Danger zone
} elseif ($analysis->rms >= 2.8) {
    $severity = 'high';      // Warning zone
} else {
    $severity = 'low';       // Good zone
}
```

### 4. Monitoring Mesin Page
**File**: `backend/resources/views/pages/monitoring-mesin.blade.php`

```javascript
// ISO 10816-3 Thresholds for Medium Machines
if (currentValue >= 7.1) {
    status = "DANGER";
} else if (currentValue >= 2.8) {
    status = "WARNING";
} else {
    status = "NORMAL";
}
```

---

## 🔄 Status Mapping

| RMS Value (mm/s) | Backend Status | Dashboard Display | Severity |
|------------------|----------------|-------------------|----------|
| 0 - 2.8 | `NORMAL` | ✅ Normal (Green) | `low` |
| 2.8 - 7.1 | `WARNING` | ⚠️ Waspada (Yellow) | `high` |
| > 7.1 | `CRITICAL` | 🚨 Bahaya (Red) | `critical` |

---

## 📊 Chart.js Configuration

### Threshold Lines pada RMS Chart

```javascript
// Threshold Line 1: Normal/Warning boundary (2.8 mm/s) - ISO 10816-3
{
    label: 'Threshold: Normal (2.8 mm/s)',
    data: Array(rmsData.length).fill(2.8),
    borderColor: 'rgba(234, 179, 8, 0.7)',
    borderDash: [5, 5],
}

// Threshold Line 2: Warning/Danger boundary (7.1 mm/s) - ISO 10816-3
{
    label: 'Threshold: Bahaya (7.1 mm/s)',
    data: Array(rmsData.length).fill(7.1),
    borderColor: 'rgba(239, 68, 68, 0.7)',
    borderDash: [5, 5],
}
```

---

## 🎨 Color Scheme

### Dashboard Visual Indicators

```css
/* Good (Normal) - Green */
--color-good: #10B981
--bg-good: #E0F5E8
--border-good: #118B50

/* Acceptable (Warning) - Yellow */
--color-warning: #EAB308
--bg-warning: #FEF3C7
--border-warning: #D97706

/* Unsatisfactory (Alert) - Red */
--color-alert: #EF4444
--bg-alert: #FEE2E2
--border-alert: #DC2626
```

---

## 🔧 Customization Guide

### How to Change Thresholds

1. **Backend API** (`DashboardApiController.php`):
   ```php
   // Line ~103
   if ($analysis->rms >= YOUR_THRESHOLD_2) {
       $severity = 'critical';
   } elseif ($analysis->rms >= YOUR_THRESHOLD_1) {
       $severity = 'high';
   }
   ```

2. **Dashboard View** (`dashboard.blade.php`):
   ```blade
   <!-- Line ~153 - Card display -->
   ✅ Good: 0-YOUR_T1g | ⚠️ Acceptable: YOUR_T1-YOUR_T2g | 🚨 Unsatisfactory: >YOUR_T2g
   
   <!-- Line ~357 - Chart.js -->
   data: Array(rmsData.length).fill(YOUR_THRESHOLD_1),
   data: Array(rmsData.length).fill(YOUR_THRESHOLD_2),
   ```

3. **Predictive API** (`AnalyzeBatchJob.php`):
   ```php
   // Line ~153
   if ($rms >= YOUR_THRESHOLD_2) {
       $conditionStatus = 'ALERT';
   } elseif ($rms >= YOUR_THRESHOLD_1) {
       $conditionStatus = 'WARNING';
   }
   ```

**⚠️ Important**: Pastikan threshold di ketiga lokasi **SYNCHRONIZED** untuk konsistensi sistem!

---

## 📝 Testing Checklist

Setelah mengubah threshold, test:

- [ ] Dashboard card displays correct threshold values
- [ ] Chart shows threshold lines at correct positions
- [ ] Color indicators match threshold levels
- [ ] API returns correct severity levels
- [ ] Alert panel triggers at correct thresholds
- [ ] Machine status cards show correct classifications
- [ ] Top machines by risk sorting works correctly

---

## 📚 References

Lihat file `ISO-10816-THRESHOLD-REFERENCE.md` untuk:
- Justifikasi threshold berdasarkan ISO 10816
- Dokumentasi untuk skripsi
- Academic references
- Validation methodology

---

**Last Updated**: 5 Januari 2026  
**Version**: 1.0  
**Status**: Production
