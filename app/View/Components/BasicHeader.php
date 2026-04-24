<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BasicHeader extends Component
{
    public $languages;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $languageSetting = \App\Models\Setting::where('name', 'language')
            ->with('options')
            ->first();

        $this->languages = $languageSetting ? $languageSetting->options : collect();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.basic-header');
    }
}
