<?php

namespace App\Services;

use App\Models\EmailSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

class PhpMailerService
{
    public function isConfigured(): bool
    {
        $settings = EmailSetting::current();

        return $settings
            && $settings->host
            && $settings->from_address
            && $settings->getDecryptedPassword() !== null;
    }

    public function send(string $to, string $subject, string $htmlBody, ?string $toName = null): bool
    {
        $settings = EmailSetting::current();

        if (! $settings || ! $settings->is_active) {
            Log::info('[PHPMailer mock] Email not sent — no active DB mail settings.', [
                'to' => $to,
                'subject' => $subject,
            ]);

            return false;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $settings->host;
            $mail->SMTPAuth = true;
            $mail->Username = $settings->username;
            $mail->Password = $settings->getDecryptedPassword();
            $mail->SMTPSecure = $settings->encryption ?: PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $settings->port ?: 587;
            $mail->CharSet = PHPMailer::CHARSET_UTF8;

            $mail->setFrom(
                $settings->from_address,
                $settings->from_name ?: config('app.name')
            );
            $mail->addAddress($to, $toName ?? '');

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();

            return true;
        } catch (MailerException $e) {
            Log::error('[PHPMailer] Send failed: '.$e->getMessage(), ['to' => $to]);

            return false;
        }
    }

    public function applyToLaravelConfig(): void
    {
        $settings = EmailSetting::current();

        if (! $settings || ! $settings->is_active) {
            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $settings->host);
        Config::set('mail.mailers.smtp.port', $settings->port);
        Config::set('mail.mailers.smtp.encryption', $settings->encryption);
        Config::set('mail.mailers.smtp.username', $settings->username);
        Config::set('mail.mailers.smtp.password', $settings->getDecryptedPassword());
        Config::set('mail.from.address', $settings->from_address);
        Config::set('mail.from.name', $settings->from_name ?: config('app.name'));
    }
}
