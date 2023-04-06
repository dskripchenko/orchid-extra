<?php

namespace Dskripchenko\OrchidExtra\Screens;

use Illuminate\Http\Request;
use Orchid\Screen\Screen as BaseScreen;

abstract class Screen extends BaseScreen
{
    public function __construct()
    {
        /** @var Request $request */
        $request = request();
        $this->booting($request->all());
    }

    /**
     * @param $parameters
     */
    public function booting($parameters): void
    {
        if (method_exists($this, 'boot')) {
            $this->boot($parameters);
        }
    }
}