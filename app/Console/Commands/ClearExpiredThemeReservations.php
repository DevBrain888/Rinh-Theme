<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Theme;
use Carbon\Carbon;

class ClearExpiredThemeReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'themes:clear-expired-reservations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очищает истекшие блокировки тем (30 минут)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // ВАЖНО: Для тестирования установлено 10 секунд. Для продакшена измените на 30 минут (1800 секунд)
        // ИЗМЕНИТЬ ДЛЯ ПРОДАКШЕНА: замените 10 на 1800 (30 минут * 60 секунд)
        // ТЕСТ: 10 секунд | ПРОДАКШЕН: 1800 секунд (30 минут)
        $reservationTimeoutSeconds = 10; // ИЗМЕНИТЬ ДЛЯ ПРОДАКШЕНА: 10 -> 1800
        
        $expiredTime = Carbon::now()->subSeconds($reservationTimeoutSeconds);
        
        // Очищаем блокировки, где:
        // 1. Время блокировки истекло
        // 2. Научный руководитель НЕ заполнен (если руководитель заполнен, блокировка не снимается)
        $cleared = Theme::whereNotNull('reserved_at')
            ->where('reserved_at', '<', $expiredTime)
            ->whereNull('supervisor_id')
            ->update([
                'reserved_by_group' => null,
                'reserved_at' => null,
            ]);
        
        $this->info("Очищено истекших блокировок: {$cleared}");
        
        return Command::SUCCESS;
    }
}
