<?php

namespace App\Http\Controllers;

use App\Models\EmailSetting;
use App\Services\PhpMailerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailSettingsController extends Controller
{
    public function __construct(protected PhpMailerService $mailer) {}

    public function edit(): View
    {
        $settings = EmailSetting::query()->latest()->first() ?? new EmailSetting([
            'mailer' => 'smtp',
            'port' => 587,
            'encryption' => 'tls',
        ]);

        return view('platform.email-settings', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = EmailSetting::query()->latest()->first() ?? new EmailSetting;

        $validated = $request->validate([
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => [$settings->exists ? 'nullable' : 'required', 'string', 'max:255'],
            'encryption' => ['nullable', 'string', 'in:tls,ssl,'],
            'from_address' => ['required', 'email', 'max:255'],
            'from_name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        if (empty($validated['password']) && $settings->exists) {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['mailer'] = 'smtp';

        if ($validated['is_active']) {
            EmailSetting::query()->update(['is_active' => false]);
        }

        $settings->fill($validated)->save();

        return back()->with('success', 'Email settings saved successfully.');
    }

    public function test(Request $request): RedirectResponse
    {
        $request->validate(['test_email' => ['required', 'email']]);

        $this->mailer->applyToLaravelConfig();

        $sent = $this->mailer->send(
            $request->test_email,
            'UBSP Test Email',
            '<p>This is a test email from '.config('app.name').'. Your PHPMailer settings are working.</p>',
        );

        return back()->with(
            $sent ? 'success' : 'error',
            $sent ? 'Test email sent successfully.' : 'Failed to send test email. Check settings and logs.'
        );
    }
}
