<?php

namespace Dskripchenko\OrchidExtra\Providers;

use Dskripchenko\OrchidExtra\Components\SelectionFactory;
use Illuminate\Support\ServiceProvider;

class OrchidExtraServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(
            'orchid_extra_selection_factory',
            SelectionFactory::class
        );

        parent::register();
    }
}