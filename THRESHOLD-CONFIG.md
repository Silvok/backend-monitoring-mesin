# Threshold Configuration - Backend System

## 📋 Overview

Dokumen ini menjelaskan konfigurasi threshold yang digunakan di sistem backend monitoring mesin untuk dashboard dan API endpoints.

---

## ⚙️ Current Threshold Configuration

### ISO 10816 Adapted Standard

| Status Level | RMS Range (g) | Visual Indicator | Dashboard Display |
|--------------|---------------|------------------|-------------------|
| **Good (Normal)** | 0.0 - 0.7 | ✅ Green | "Good: 0-0.7g" |
| **Acceptable (Warning)** | 0.7 - 1.8 | ⚠️ Yellow | "Acceptable: 0.7-1.8g" |
| **Unsatisfactory (Alert)** | > 1.8 | 🚨 Red | "Unsatisfactory: >1.8g" |

---

## 📁 Files Updated

### 1. Dashboard View
**File**: `backend/resources/views/dashboard.blade.php`

**Lines**: 
- Line 153: Anomali card threshold display
- Line 207-217: Chart threshold panel with color indicators
- Line 357-373: Chart.js threshold lines (0.7 and 1.8)

**Changes**:
```blade
<!-- Before -->
✅ Normal: 0-0.5 | ⚠️ Caution: 0.5-1.5 | 🚨 Alert: >1.5

<!-- After -->
✅ Good: 0-0.7g | ⚠️ Acceptable: 0.7-1.8g | 🚨 Unsatisfactory: >1.8g
```

### 2. API Controller
**File**: `backend/app/Http/Controllers/Api/DashboardApiController.php`

**Line**: ~103-115

**Changes**:
```php
// Before
if ($analysis->rms >= 2.0) {
    $severity = 'critical';
} elseif ($analysis->rms >= 1.5) {
    $severity = 'high';
} elseif ($analysis->rms >= 1.0) {
    $severity = 'medium';
}

// After (ISO 10816 Adapted)
if ($analysis->rms >= 1.8) {
    $severity = 'critical';  // Unsatisfactory
} elseif ($analysis->rms >= 0.7) {
    $severity = 'high';      // Acceptable
} else {
    $severity = 'low';       // Good
}
```

---

## 🔄 Synchronization dengan Predictive API

### Predictive API Threshold
**File**: `predictive-api/app/Jobs/AnalyzeBatchJob.php`

**Line**: ~153-165

```php
// ISO 10816 Adapted - Threshold RMS (g-force)
if ($rms >= 1.8) {
    $conditionStatus = 'ALERT';    // Unsatisfactory
} elseif ($rms >= 0.7) {
    $conditionStatus = 'WARNING';  // Acceptable
} else {
    $conditionStatus = 'NORMAL';   // Good
}
```

### Status Mapping

| Predictive API | Backend | Dashboard Display |
|----------------|---------|-------------------|
| `NORMAL` | `NORMAL` | ✅ Good (Green) |
| `WARNING` | `WARNING` / `HIGH` | ⚠️ Acceptable (Yellow) |
| `ALERT` | `ALERT` / `CRITICAL` | 🚨 Unsatisfactory (Red) |

---

## 📊 Chart.js Configuration

### Threshold Lines pada RMS Chart

```javascript
// Threshold Line 1: Good/Acceptable boundary (0.7g)
{
    label: 'Threshold: Good (0.7g)',
    data: Array(rmsData.length).fill(0.7),
    borderColor: 'rgba(234, 179, 8, 0.7)',
    borderDash: [5, 5],
}

// Threshold Line 2: Acceptable/Unsatisfactory boundary (1.8g)
{
    label: 'Threshold: Unsatisfactory (1.8g)',
    data: Array(rmsData.length).fill(1.8),
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
