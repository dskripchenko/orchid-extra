<?php

namespace Dskripchenko\OrchidExtra\Screens;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen as BaseScreen;
use Throwable;

abstract class Screen extends BaseScreen
{
    /**
     * @param Repository $repository
     *
     * @return View
     */
    public function build(Repository $repository): View
    {
        /** @var Request $request */
        $request = request();
        $this->booting($request->all());
        return parent::build($repository);
    }


    /**
     * @param  string  $method
     * @param  string  $slug
     * @return View
     * @throws Throwable
     */
    public function asyncBuild(string $method, string $slug): View
    {
        /** @var Request $request */
        $request = request();
        $this->booting($request->all());
        return parent::asyncBuild($method, $slug);
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