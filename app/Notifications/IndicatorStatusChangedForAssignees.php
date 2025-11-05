<?php

namespace App\Notifications;

use App\Models\Indicator;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IndicatorStatusChangedForAssignees extends Notification
{
    use Queueable;

    public function __construct(
        public Indicator $indicator,
        public int|string $newStatus,
        public int|string|null $prevStatus = null,
        public ?string $changedBy = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    private function statusLabel(int|string|null $status): string
    {
        $map = [
            0 => 'ยังไม่กำหนด',
            1 => 'บันทึกเป็นฉบับร่าง',
            2 => 'ส่งตรวจ (รอ QA ตรวจ)',
            3 => 'ผ่านการตรวจ (อนุมัติ)',
            4 => 'ไม่ผ่านการตรวจ (ต้องแก้ไข)',
        ];
        if (is_numeric($status)) {
            $key = (int) $status;
            return $map[$key] ?? (string) $status;
        }
        return (string) ($status ?? '-');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $indicator = $this->indicator;
        $newLabel = $this->statusLabel($this->newStatus);
        $prevLabel = $this->prevStatus !== null ? $this->statusLabel($this->prevStatus) : null;
        $title = sprintf('[KPI] เปลี่ยนสถานะ: %s %s → %s', (string) ($indicator->code ?? ''), (string) ($indicator->name ?? ''), $newLabel);
        $url = route('dashboardkpi.user.show', ['id' => $indicator->id]);

        $mail = (new MailMessage)
            ->subject($title)
            ->greeting('สวัสดี')
            ->line('มีการเปลี่ยนสถานะตัวชี้วัดของคุณในระบบ KPI')
            ->line(sprintf('ตัวชี้วัด: %s (%s)', (string) ($indicator->name ?? '-'), (string) ($indicator->code ?? '-')))
            ->line('สถานะใหม่: ' . $newLabel)
            ->action('เปิดดูตัวชี้วัด', $url);

        if ($prevLabel) {
            $mail->line('สถานะเดิม: ' . $prevLabel);
        }
        if ($this->changedBy) {
            $mail->line('เปลี่ยนโดย: ' . $this->changedBy);
        }

        if ((int) $this->newStatus === 1) {
            $mail->line('หมายเหตุ: ขณะนี้คุณสามารถแก้ไขตัวชี้วัดนี้ได้แล้ว');
        }

        return $mail->salutation(' ');
    }
}

