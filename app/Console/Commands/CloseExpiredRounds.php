<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApplicationRound;
use App\Enums\RoundStatus;
use Illuminate\Support\Facades\Cache;

class CloseExpiredRounds extends Command
{
    // ตั้งชื่อคำสั่งสำหรับรันใน Terminal
    protected $signature = 'app:close-expired-rounds';

    // คำอธิบายคำสั่ง
    protected $description = 'Check and close application rounds that have passed their end time';

    public function handle()
    {
        // 1. หารอบที่ยัง OPEN อยู่ แต่เวลาปัจจุบ้นเลย end_time ไปแล้ว
        $expiredRounds = ApplicationRound::where('status', RoundStatus::OPEN->value)
            ->where('end_time', '<', now())
            ->get();

        if ($expiredRounds->isEmpty()) {
            $this->info('No expired rounds to close.');
            return;
        }

        // 2. วนลูปเปลี่ยนสถานะและเคลียร์ Cache
        foreach ($expiredRounds as $round) {
            $round->update(['status' => RoundStatus::CLOSED->value]);

            // 🌟 เคลียร์ Cache โดยใช้ domain ของรอบนั้นๆ โดยตรง (เพราะไม่มี auth()->user())
            $domainValue = is_object($round->domain) ? $round->domain->value : $round->domain;
            Cache::forget("application_round.active.domain.{$domainValue}");

            $this->info("Closed round ID: {$round->id} for domain: {$domainValue}");
        }

        $this->info("Successfully closed {$expiredRounds->count()} rounds.");
    }
}
