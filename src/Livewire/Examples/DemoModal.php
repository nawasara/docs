<?php

namespace Nawasara\Docs\Livewire\Examples;

use Livewire\Component;

class DemoModal extends Component
{
    public $message = 'livewire:nawasara-docs.examples.demo-modal';

    public function render()
    {
        return view('nawasara-docs::livewire.pages.examples.demo-modal');
    }
}
