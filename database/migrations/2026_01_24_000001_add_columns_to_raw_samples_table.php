<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * File migration ini sebelumnya kosong dan menyebabkan error saat test.
     * Dipertahankan sebagai no-op agar urutan migration tetap konsisten.
     */
    public function up(): void
    {
        // no-op
    }

    public function down(): void
    {
        // no-op
    }
};

