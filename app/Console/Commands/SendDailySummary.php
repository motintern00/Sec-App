<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\DailySummaryNotification;
use App\Services\DashboardService;
use Illuminate\Console\Command;

class SendDailySummary extends Command
{
    protected $signature = 'attendance:daily-summary {hour : Jam pengiriman 8|15|22}';

    protected $description = 'Kirim ringkasan harian absensi ke admin';

    public function handle(DashboardService $dashboardService): int
    {
        $hour = (int) $this->argument('hour');
        $now = now();

        if ((int) $now->format('G') !== $hour) {
            return self::SUCCESS;
        }

        $stats = $dashboardService->getStats($now);
        $date = $now->translatedFormat('d F Y');

        User::query()->where('role', UserRole::Admin)->each(function (User $admin) use ($stats, $date, $now) {
            $alreadySent = $admin->notifications()
                ->where('created_at', '>=', $now->copy()->startOfHour())
                ->where('data->type', 'daily_summary')
                ->exists();

            if ($alreadySent) {
                return;
            }

            $admin->notify(new DailySummaryNotification($stats, $date));
        });

        return self::SUCCESS;
    }
}
