<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class DeadlineReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title = 'แจ้งเตือนกำหนดส่งหลักฐาน',
        public ?string $message = null,
        public ?string $actionUrl = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('สวัสดีค่ะ/ครับ');

        if ($this->message) {
            // Render rich HTML content from the editor without escaping tags
            $mail->line(new HtmlString($this->message));
        }

        if ($this->actionUrl) {
            $mail->action('เปิดดูรายการตัวชี้วัดที่รับผิดชอบ', $this->actionUrl);
        }

        return $mail->line('ขอบคุณค่ะ/ครับ')->salutation(' ');
    }
}
