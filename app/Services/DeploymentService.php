<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class DeploymentService
{
    public function isEnabled(): bool
    {
        return (bool) config('deploy.enabled', false);
    }

    public function isGitEnabled(): bool
    {
        return $this->isEnabled() && (bool) config('deploy.git.enabled', false);
    }

    public function projectRoot(): string
    {
        return base_path();
    }

    public function isGitRepository(): bool
    {
        return is_dir($this->projectRoot().'/.git');
    }

    /**
     * @return array{success: bool, output: string, command: string}
     */
    public function runArtisan(string $key): array
    {
        $commands = config('deploy.artisan_commands', []);
        $command = $commands[$key] ?? null;

        if ($command === null) {
            return [
                'success' => false,
                'output' => 'Unknown command.',
                'command' => $key,
            ];
        }

        try {
            $exitCode = Artisan::call($command);
            $output = trim(Artisan::output());

            Log::info('[Deploy] Artisan command finished', [
                'command' => $command,
                'exit_code' => $exitCode,
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => $exitCode === 0,
                'output' => $output !== '' ? $output : ($exitCode === 0 ? 'Command completed successfully.' : 'Command failed.'),
                'command' => $command,
            ];
        } catch (\Throwable $e) {
            Log::error('[Deploy] Artisan command failed', [
                'command' => $command,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return [
                'success' => false,
                'output' => $e->getMessage(),
                'command' => $command,
            ];
        }
    }

    /**
     * @return array{success: bool, output: string, command: string}
     */
    public function linkStorage(): array
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        if (! is_dir($target)) {
            File::ensureDirectoryExists($target);
        }

        if (is_link($link)) {
            @unlink($link);
        } elseif (is_dir($link) && ! is_link($link)) {
            return [
                'success' => false,
                'output' => 'public/storage exists as a directory. Remove it manually, then retry.',
                'command' => 'storage:link',
            ];
        }

        if (@symlink($target, $link)) {
            Log::info('[Deploy] Storage symlink created', ['user_id' => auth()->id()]);

            return [
                'success' => true,
                'output' => 'Symlink created: public/storage → storage/app/public',
                'command' => 'storage:link',
            ];
        }

        $shell = $this->runShell(
            'ln -sf ../storage/app/public public/storage',
            $this->projectRoot()
        );

        $shell['command'] = 'storage:link';

        return $shell;
    }

    /**
     * @return array{success: bool, output: string, command: string}
     */
    public function gitPull(): array
    {
        if (! $this->isGitEnabled()) {
            return [
                'success' => false,
                'output' => 'Git deploy is disabled. Set DEPLOY_GIT_ENABLED=true in .env.',
                'command' => 'git pull',
            ];
        }

        if (! $this->isGitRepository()) {
            return [
                'success' => false,
                'output' => 'This directory is not a git repository (.git folder missing).',
                'command' => 'git pull',
            ];
        }

        $remote = config('deploy.git.remote', 'origin');
        $branch = config('deploy.git.branch', 'main');
        $command = sprintf('git pull %s %s', escapeshellarg($remote), escapeshellarg($branch));

        $result = $this->runShell($command, $this->projectRoot());
        $result['command'] = trim($command, "'");

        if ($result['success']) {
            Log::info('[Deploy] Git pull succeeded', [
                'branch' => $branch,
                'user_id' => auth()->id(),
            ]);
        }

        return $result;
    }

    /**
     * @return array{success: bool, output: string, command: string}
     */
    public function gitStatus(): array
    {
        if (! $this->isGitRepository()) {
            return [
                'success' => false,
                'output' => 'Not a git repository.',
                'command' => 'git status',
            ];
        }

        $result = $this->runShell('git status -sb && echo --- && git log -1 --oneline', $this->projectRoot());
        $result['command'] = 'git status';

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function environmentInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => config('app.env'),
            'debug' => config('app.debug'),
            'git_repo' => $this->isGitRepository(),
            'git_branch' => config('deploy.git.branch'),
            'manifest_exists' => is_file(public_path('build/manifest.json')),
            'storage_linked' => is_link(public_path('storage')) || is_dir(public_path('storage')),
            'exec_available' => $this->isFunctionAvailable('exec'),
            'shell_exec_available' => $this->isFunctionAvailable('shell_exec'),
            'proc_open_available' => $this->isFunctionAvailable('proc_open'),
        ];
    }

    /**
     * @return array{success: bool, output: string, command: string}
     */
    protected function runShell(string $command, string $cwd): array
    {
        if (! $this->isFunctionAvailable('exec')
            && ! $this->isFunctionAvailable('shell_exec')
            && ! $this->isFunctionAvailable('proc_open')) {
            return [
                'success' => false,
                'output' => 'Shell functions (exec, shell_exec, proc_open) are disabled on this server. Use SSH for git commands.',
                'command' => $command,
            ];
        }

        try {
            $process = Process::fromShellCommandline($command, $cwd, null, null, 120);
            $process->run();

            $output = trim($process->getOutput()."\n".$process->getErrorOutput());

            return [
                'success' => $process->isSuccessful(),
                'output' => $output !== '' ? $output : ($process->isSuccessful() ? 'Completed successfully.' : 'Command failed with no output.'),
                'command' => $command,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'output' => $e->getMessage(),
                'command' => $command,
            ];
        }
    }

    protected function isFunctionAvailable(string $function): bool
    {
        if (! function_exists($function)) {
            return false;
        }

        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));

        return ! in_array($function, $disabled, true);
    }
}
