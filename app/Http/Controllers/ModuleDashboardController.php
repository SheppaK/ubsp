<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ModuleDashboardController extends Controller
{
    public function show(string $module): View
    {
        $config = config("ubsp.modules.{$module}");

        abort_unless($config, 404);

        $view = "modules.{$module}.dashboard";

        if (! view()->exists($view)) {
            $view = 'modules.generic.dashboard';
        }

        return view($view, [
            'module' => $module,
            'config' => $config,
        ]);
    }
}
