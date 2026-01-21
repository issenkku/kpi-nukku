<?php

namespace App\Notifications;

use App\Models\Indicator;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IndicatorAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(public Indicator $indicator, public array $missingRequirements = [])
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $indicator = $this->indicator;
        $title = sprintf('[KPI] มอบหมายงานใหม่: %s %s', (string) ($indicator->code ?? ''), (string) ($indicator->name ?? ''));
        $url = route('dashboardkpi.user.show', ['id' => $indicator->id]);

        $mail = (new MailMessage)
            ->subject($title)
            ->greeting('สวัสดีค่ะ/ครับ')
            ->line(!empty($this->missingRequirements)
                ? 'มีการติดตามเอกสารตัวชี้วัดในระบบ KPI'
                : 'คุณได้รับมอบหมายงานตัวชี้วัดใหม่ในระบบ KPI')
            ->line(sprintf('ตัวชี้วัด: %s (%s)', (string) ($indicator->name ?? '-'), (string) ($indicator->code ?? '-')));

        if (!empty($this->missingRequirements)) {
            $mail->line('รายการหลักฐานที่ยังไม่ครบ:');
            foreach ($this->missingRequirements as $name) {
                $mail->line('- ' . $name);
            }
        }

        return $mail
            ->action('เปิดดูตัวชี้วัด', $url)
            ->line('ขอบคุณที่ใช้งานระบบ')
            ->salutation(' ');
    }
}
