<?php

namespace App\Livewire\Admin\Bilty;

use App\Livewire\Admin\Documents\DocumentListBase;

class BiltyList extends DocumentListBase
{
    protected function documentType(): string
    {
        return 'bilty';
    }

    protected function routeName(): string
    {
        return 'builty';
    }
}
