<?php

namespace Edwinekr\OtelElkLaravel\View\Components;

use Illuminate\View\Component;

class RumScript extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('otel-elk::components.rum-script');
    }
}
