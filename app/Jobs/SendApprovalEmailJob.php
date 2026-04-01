<?php

namespace App\Jobs;

use App\Mail\ApplicationApprovedMail;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendApprovalEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // 🌟 รับข้อมูลใบสมัครเข้ามาเก็บไว้
    public function __construct(public Application $application)
    {}

    // 🌟 ฟังก์ชันนี้จะถูกรันเมื่อคิวเริ่มทำงาน
    public function handle(): void
    {
        // สั่งส่งอีเมลหานิสิต (ดึงอีเมลจาก relationship user)
        Mail::to($this->application->user->email)
            ->send(new ApplicationApprovedMail($this->application));
    }
}
