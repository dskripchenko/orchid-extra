<?php

namespace Dskripchenko\OrchidExtra\Screens;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Orchid\Support\Facades\Alert;
use Throwable;

abstract class BaseListScreen extends Screen
{
    /**
     * @return Model
     */
    abstract protected function entity(): Model;

    /**
     * @return string
     */
    abstract protected function getListLayoutClass(): string;

    /**
     * @return array
     */
    protected function getSelections(): array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getQueryWithParameters(): array
    {
        return [];
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    protected function prepareQuery(Builder $query): Builder
    {
        return $query;
    }

    /**
     * @return array
     */
    public function query(): array
    {
        $entity = $this->entity();

        $query = $entity->newQuery()
            ->filters()
            ->defaultSort('id', 'desc');

        foreach ($this->getSelections() as $selection) {
            $query->filtersApplySelection($selection);
        }

        $query->with($this->getQueryWithParameters());

        $query = $this->prepareQuery($query);

        $paginator = $query->paginate();

        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        if ($currentPage > $lastPage) {
            /** @var Request $request */
            $request = request();
            /** @var Route $route */
            $route = $request->route();
            $routeName = $route->getName();
            $routeParameters = $route->parameters;

            redirect()->route($routeName, array_merge($routeParameters, [
                'page' => $lastPage
            ]))->send();

            return [];
        }

        return [
            $entity->getTable() => $paginator,
        ];
    }

    /**
     * @return string[]
     */
    public function layout(): array
    {
        return [
            ...$this->getSelections(),
            $this->getListLayoutClass(),
        ];
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function publishing(Request $request): RedirectResponse
    {
        $id = $request->get('id');
        $entity = $this->entity()
            ->newQuery()
            ->findOrFail($id);
        $field = $request->get('field');
        $oldValue = $entity->getAttribute($field);
        $entity->setAttribute($field, !$oldValue);
        $entity->saveOrFail();

        Alert::success(
            !$oldValue
                ? 'Запись успешно опубликована'
                : 'Запись успешно скрыта'
        );

        return redirect()->back();
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function deleting(Request $request): RedirectResponse
    {
        $id = $request->get('id');
        $entity = $this->entity()
            ->newQuery()
            ->findOrFail($id);

        $entity->delete();

        Alert::success('Запись успешно удалена');

        return redirect()->back();
    }

    /**
     * @return RedirectResponse
     */
    public function truncate(): RedirectResponse
    {
        $table = $this->entity()->getTable();
        $sql = "TRUNCATE TABLE {$table};";
        DB::statement($sql);
        Alert::success("Все данные раздела успешно удалены");

        return redirect()->back();
    }
}
