<?php

namespace App\Http\Controllers;

use App\Services\DeploymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeploymentController extends Controller
{
    public function __construct(protected DeploymentService $deploy) {}

    public function index(): View|RedirectResponse
    {
        if (! $this->deploy->isEnabled()) {
            abort(404);
        }

        $gitStatus = $this->deploy->isGitRepository()
            ? $this->deploy->gitStatus()
            : null;

        return view('platform.deployment', [
            'commands' => config('deploy.artisan_labels', []),
            'gitStatus' => $gitStatus,
            'info' => $this->deploy->environmentInfo(),
            'gitEnabled' => $this->deploy->isGitEnabled(),
        ]);
    }

    public function artisan(Request $request): RedirectResponse
    {
        if (! $this->deploy->isEnabled()) {
            abort(404);
        }

        $validated = $request->validate([
            'command' => ['required', 'string', 'in:'.implode(',', array_keys(config('deploy.artisan_commands', [])))],
        ]);

        $result = $this->deploy->runArtisan($validated['command']);

        return back()->with('deploy_result', $result);
    }

    public function gitPull(): RedirectResponse
    {
        if (! $this->deploy->isEnabled()) {
            abort(404);
        }

        $result = $this->deploy->gitPull();

        return back()->with('deploy_result', $result);
    }

    public function linkStorage(): RedirectResponse
    {
        if (! $this->deploy->isEnabled()) {
            abort(404);
        }

        $result = $this->deploy->linkStorage();

        return back()->with('deploy_result', $result);
    }

    public function refreshGitStatus(): RedirectResponse
    {
        if (! $this->deploy->isEnabled()) {
            abort(404);
        }

        $result = $this->deploy->gitStatus();

        return back()->with('deploy_result', $result);
    }
}
