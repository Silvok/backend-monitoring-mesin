<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\WhatsAppService;

Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('wa:test-alert', function () {
    if (!config('services.whatsapp.enabled')) {
        $this->error('WHATSAPP_ENABLED=false. Aktifkan dulu di .env');
        return 1;
    }

    $alert = DB::table('analysis_results as ar')
        ->leftJoin('machines as m', 'm.id', '=', 'ar.machine_id')
        ->whereIn(DB::raw('UPPER(ar.condition_status)'), ['WARNING', 'CRITICAL', 'ANOMALY', 'DANGER'])
        ->orderByDesc('ar.id')
        ->select([
            DB::raw('COALESCE(m.name, "Mesin") as machine_name'),
            'ar.condition_status',
            'ar.rms',
            'ar.created_at',
        ])
        ->first();

    if (!$alert) {
        $this->error('Tidak ada data alert untuk dikirim.');
        return 1;
    }

    $targets = User::query()
        ->select('id', 'name', 'phone')
        ->where('role', 'koordinator')
        ->where('status', 'active')
        ->where('wa_notification_enabled', true)
        ->whereNotNull('phone')
        ->get();

    if ($targets->isEmpty()) {
        $this->error('Koordinator target tidak ditemukan.');
        return 1;
    }

    $status = strtoupper((string) $alert->condition_status);
    $rms = $alert->rms !== null ? number_format((float) $alert->rms, 3) . ' mm/s' : '-';
    $statusLabel = match ($status) {
        'CRITICAL', 'ANOMALY', 'DANGER' => 'KRITIS',
        'WARNING' => 'PERINGATAN',
        default => $status,
    };
    $message = trim(implode("\n", [
        "Halo Koordinator,",
        "",
        "Sistem Monitoring Mesin mendeteksi kondisi {$statusLabel} pada unit berikut:",
        "Mesin: {$alert->machine_name}",
        "Status Alert: {$status}",
        "Nilai RMS saat ini: {$rms}",
        "Waktu deteksi: " . now()->format('d-m-Y H:i') . " WIB",
        "",
        "Mohon tindak lanjut:",
        "1. Cek kondisi mesin di area terkait.",
        "2. Koordinasikan teknisi untuk pemeriksaan awal.",
        "3. Update status penanganan melalui dashboard.",
        "",
        "Akses dashboard: " . rtrim((string) config('app.url'), '/') . '/alert-management',
        "",
        "Terima kasih.",
        "Pesan ini dikirim otomatis oleh Sistem Monitoring Mesin.",
    ]));

    $service = app(WhatsAppService::class);
    foreach ($targets as $target) {
        try {
            $service->sendMessage((string) $target->phone, $message);
            $this->info("OK -> {$target->name} ({$target->phone})");
        } catch (\Throwable $e) {
            $this->error("FAIL -> {$target->name} ({$target->phone}): {$e->getMessage()}");
        }
    }

    return 0;
})->purpose('Kirim test WhatsApp alert ke koordinator aktif');
