<?php

namespace App\Http\ViewComposers;

use App\Models\Cadre;

class MainLayoutViewComposer
{
    private $allCadre;
    public function compose($view)
    {

        if (!$this->allCadre) {
            // Log::info("inside layout composer");
            $this->allCadre = Cadre::get(['id', 'title'])->toArray();
        }

        $view->with(
            'all_cadres',
            $this->allCadre
        );
    }
}
