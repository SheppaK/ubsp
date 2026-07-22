<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();

        if ($user->two_factor_secret) {
            return view('auth.two-factor.manage', ['user' => $user]);
        }

        $google2fa = new Google2FA;
        $secret = $google2fa->generateSecretKey();

        session(['two_factor_secret' => $secret]);

        $qrCodeUrl = $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secret);

        $writer = new Writer(new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd));
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return view('auth.two-factor.setup', [
            'secret' => $secret,
            'qrCodeSvg' => $qrCodeSvg,
        ]);
    }

    public function enable(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $secret = session('two_factor_secret');
        abort_unless($secret, 422);

        $google2fa = new Google2FA;

        if (! $google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        $request->user()->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_enabled' => true,
        ]);

        session()->forget('two_factor_secret');

        return redirect()->route('profile.edit')->with('success', 'Two-factor authentication enabled.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|current_password']);

        $request->user()->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Two-factor authentication disabled.');
    }

    public function challenge(): View
    {
        return view('auth.two-factor.challenge');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $userId = session('login.id');
        abort_unless($userId, 403);

        $user = \App\Models\User::findOrFail($userId);
        $google2fa = new Google2FA;

        if (! $google2fa->verifyKey(decrypt($user->two_factor_secret), $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        auth()->login($user, session('login.remember', false));
        session()->forget(['login.id', 'login.remember']);

        return redirect()->intended(route('platform.dashboard'));
    }
}
