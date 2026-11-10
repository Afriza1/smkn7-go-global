<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumni_globals', function (Blueprint $table) {
            $table->timestamp('kontak_disetujui_pada')->nullable()->after('kontak_disetujui');
            $table->foreignId('kontak_disetujui_oleh')
                ->nullable()
                ->after('kontak_disetujui_pada')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('alumni_globals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kontak_disetujui_oleh');
            $table->dropColumn('kontak_disetujui_pada');
        });
    }
};
