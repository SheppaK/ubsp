<?php

namespace App\Mail;

use App\Models\Business;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantAccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword,
        public Business $business,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your UBSP Tenant Account — '.$this->business->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-account-created-html',
            with: [
                'password' => $this->plainPassword,
                'loginUrl' => route('login'),
                'resetUrl' => route('password.request'),
            ],
        );
    }
}
