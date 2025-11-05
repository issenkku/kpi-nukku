<?php

namespace App\Mail\Transport;

use App\Services\KKUApiService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class KkuApiTransport extends AbstractTransport
{
    public function __construct(private KKUApiService $service)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $original = $message->getOriginalMessage();

        if (! $original instanceof Email) {
            // Fallback: send raw string as HTML body
            $payload = [
                'from' => config('mail.from.address'),
                'fromName' => config('mail.from.name'),
                'to' => config('mail.from.address'),
                'subject' => '(no subject)',
                'message' => '<pre>'.e((string) $original->toString()).'</pre>',
            ];
            $result = $this->service->sendMail($payload);
            if (!($result['ok'] ?? false)) {
                throw new TransportException('KKU API send failed (raw): '.json_encode($result));
            }
            return;
        }

        $from = $this->firstAddress($original->getFrom()) ?? config('mail.from.address');
        $fromName = $from?->getName() ?: (string) config('mail.from.name');

        $toList = $original->getTo();
        $ccList = $original->getCc();
        $bccList = $original->getBcc();

        $to = $this->implodeAddresses($toList);
        $cc = $this->implodeAddresses($ccList) ?: null;
        $bcc = $this->implodeAddresses($bccList) ?: null;

        $subject = (string) $original->getSubject();
        $html = $original->getHtmlBody();
        $text = $original->getTextBody();

        $body = $html !== null && $html !== ''
            ? $html
            : nl2br((string) $text);

        if (count($original->getAttachments()) > 0) {
            Log::warning('KKU transport: attachments present but not supported; ignoring');
        }

        $payload = [
            'from' => $from?->getAddress(),
            'fromName' => $fromName,
            'to' => $to,
            'subject' => $subject,
            'message' => $body,
            'cc' => $cc,
            'bcc' => $bcc,
        ];

        $result = $this->service->sendMail($payload);
        if (!($result['ok'] ?? false)) {
            throw new TransportException('KKU API send failed: '.json_encode($result));
        }
    }

    public function __toString(): string
    {
        return 'kku-api';
    }

    private function firstAddress(array $addresses): ?Address
    {
        return $addresses[0] ?? null;
    }

    private function implodeAddresses(?array $addresses): string
    {
        $addresses = $addresses ?? [];
        return implode(',', array_map(fn(Address $a) => $a->getAddress(), $addresses));
    }
}

