<?php

namespace App\Notifications;

use App\Models\Indicator;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IndicatorCorrectionRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(public Indicator $indicator, public ?string $requestedBy = null, public ?string $note = null)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $indicator = $this->indicator;
        $title = sprintf('[KPI] ร้องขอแก้ไข: %s %s', (string) ($indicator->code ?? ''), (string) ($indicator->name ?? ''));
        $url = route('dashboardkpi.admin.show', ['id' => $indicator->id]);

        $mail = (new MailMessage)
            ->subject($title)
            ->greeting('แจ้งเตือน เจ้าหน้าที่')
            ->line('มีการร้องขอการแก้ไขจากผู้ใช้ในตัวชี้วัดต่อไปนี้')
            ->line(sprintf('ตัวชี้วัด: %s (%s)', (string) ($indicator->name ?? '-'), (string) ($indicator->code ?? '-')))
            ->action('เปิดดูตัวชี้วัด', $url);

        if ($this->requestedBy) {
            $mail->line('ผู้ร้องขอ: ' . $this->requestedBy);
        }
        if ($this->note) {
            $mail->line('หมายเหตุ: ' . $this->note);
        }

        return $mail->line('ขอบคุณค่ะ/ครับ')->salutation(' ');
    }
}

