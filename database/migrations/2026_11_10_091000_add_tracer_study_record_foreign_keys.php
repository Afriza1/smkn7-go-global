<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['tracer_studies', 'users'] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException("Required table {$table} is missing.");
            }
        }
        foreach (['record_type', 'periode_id', 'dibuat_oleh'] as $column) {
            if (! Schema::hasColumn('tracer_studies', $column)) {
                throw new RuntimeException("tracer_studies.{$column} is missing; period references were not added.");
            }
        }

        $invalidPeriodReference = DB::table('tracer_studies as response')
            ->leftJoin('tracer_studies as period', 'period.id', '=', 'response.periode_id')
            ->where('response.record_type', 'website')
            ->where(function ($query): void {
                $query->whereNull('response.periode_id')
                    ->orWhereNull('period.record_type')
                    ->orWhere('period.record_type', '<>', 'period');
            })->exists();
        if ($invalidPeriodReference) {
            throw new RuntimeException('A website response does not point to a period record.');
        }

        Schema::table('tracer_studies', function (Blueprint $table): void {
            $table->foreign('periode_id', 'tracer_studies_period_record_fk')
                ->references('id')->on('tracer_studies')->restrictOnDelete();
            $table->foreign('dibuat_oleh', 'tracer_studies_period_creator_fk')
                ->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tracer_studies', function (Blueprint $table): void {
            $table->dropForeign('tracer_studies_period_record_fk');
            $table->dropForeign('tracer_studies_period_creator_fk');
        });
    }
};
