<?php

namespace App\Notifications;

use App\Models\Indicator;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IndicatorFinalSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public Indicator $indicator, public ?string $submittedBy = null)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $indicator = $this->indicator;
        $title = sprintf('[KPI] ส่งฉบับจริง: %s %s', (string) ($indicator->code ?? ''), (string) ($indicator->name ?? ''));
        $by = $this->submittedBy ? ('โดย ' . $this->submittedBy) : null;
        $url = route('dashboardkpi.admin.show', ['id' => $indicator->id]);

        $mail = (new MailMessage)
            ->subject($title)
            ->greeting('แจ้งเตือน เจ้าหน้าที่')
            ->line('มีการบันทึกฉบับจริงของตัวชี้วัดในระบบ KPI')
            ->line(sprintf('ตัวชี้วัด: %s (%s)', (string) ($indicator->name ?? '-'), (string) ($indicator->code ?? '-')))
            ->action('ตรวจสอบตัวชี้วัด', $url);

        if ($by) {
            $mail->line($by);
        }

        return $mail->line('ขอบคุณค่ะ/ครับ')->salutation(' ');
    }
}

