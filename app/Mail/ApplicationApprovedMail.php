<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Application $application)
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ยินดีด้วย! ใบสมัครรับรางวัลนิสิตดีเด่นของคุณผ่านการอนุมัติแล้ว 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application_approved', // 🌟 ชี้ไปที่ไฟล์ Blade ที่เราจะสร้างทีหลัง
        );
    }
}
