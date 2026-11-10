<?php

namespace App\Services;

class SchoolMonitoringStatistics
{
    public function alumniAktifJepang(): int
    {
        return (int) config('school_monitoring.alumni_aktif_jepang');
    }
}
