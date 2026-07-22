<?php

namespace App\Services;

use App\Models\ThemeSetting;

class ThemeService
{
    public function current(): ThemeSetting
    {
        return ThemeSetting::current();
    }

    public function cssVariableString(): string
    {
        $vars = $this->current()->toCssVariables();
        $lines = [];

        foreach ($vars as $name => $value) {
            $lines[] = "{$name}: {$value};";
        }

        return implode("\n    ", $lines);
    }
}
