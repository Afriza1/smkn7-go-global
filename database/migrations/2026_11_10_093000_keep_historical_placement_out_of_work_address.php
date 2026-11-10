<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tracer_studies')->where('record_type', 'historical')->update([
            'alamat_tempat_kerja' => null,
        ]);
    }

    public function down(): void
    {
        DB::table('tracer_studies')->where('record_type', 'historical')->update([
            'alamat_tempat_kerja' => DB::raw('penempatan_asli'),
        ]);
    }
};
