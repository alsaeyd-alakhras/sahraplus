<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FrontLayout extends Component
{
    public $title;
    public $lang;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = null, $lang = null)
    {
        $this->title = $title ?? config('settings.app_name');
        $this->lang = $lang ?? app()->getLocale();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.front-layout', [
            'title' => $this->title,
            'lang' => $this->lang,
        ]);
    }
}
