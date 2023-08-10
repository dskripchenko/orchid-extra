<?php

namespace Dskripchenko\OrchidExtra\Screens;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

abstract class BaseEditScreen extends Screen
{
    /**
     * @return Model
     */
    abstract protected function entity(): Model;

    /**
     * @return string|\Orchid\Screen\Layout
     */
    abstract protected function getEditLayoutClass();

    /**
     * @param Model $entity
     * @param Request $request
     * @return array
     */
    abstract protected function getSaveEntityRules(Model $entity, Request $request): array;


    protected function getSaveEntityRuleMessages(Model $entity, Request $request): array
    {
        return [];
    }


    /**
     * @var bool
     */
    protected bool $exist = false;

    /**
     * @var string Роут, на который случится переход после удаления записи
     */
    protected string $redirectAfterDeletingRouteName = 'platform.index';

    /**
     * @var string Роут, на который случится переход после сохранения записи
     */
    protected string $redirectAfterSavingRouteName = 'platform.index';

    /**
     * @return Model
     */
    protected function getCurrentEntity(): Model
    {
        $routeValue = $this->getRouteParameterValue();

        $entity = $this->entity();
        return $entity
            ->newQuery()
            ->where($entity->getRouteKeyName(), $routeValue)
            ->firstOrNew();
    }

    /**
     * @return string|null
     */
    protected function getRouteParameterKey(): ?string
    {
        $entity = $this->entity();
        return Str::snake(class_basename($entity));
    }

    /**
     * @return string|null
     */
    protected function getRouteParameterValue(): ?string
    {
        $request = \request();
        $route = $request->route();
        $routeParameterKey = $this->getRouteParameterKey();
        return $route->parameter($routeParameterKey);
    }

    /**
     * @return array|\Orchid\Screen\Layout[]
     */
    public function layout(): array
    {
        $block = Layout::block([$this->getEditLayoutClass()]);

        if ($this->name) {
            $block->title($this->name);
        }

        if ($this->description) {
            $block->description($this->description);
        }

        return [ $block ];
    }

    /**
     * @return Model[]
     */
    public function query(): array
    {
        $routeKey = $this->getRouteParameterKey();

        $entity = $this->getCurrentEntity();

        $this->exist = $entity->exists;

        return [
            $routeKey => $entity,
        ];
    }

    /**
     * @return array|Action[]
     */
    public function commandBar(): array
    {
        return [
            Button::make('Сохранить')
                ->icon('bs.check')
                ->method('save'),

            Button::make('Удалить')
                ->icon('bs.trash')
                ->confirm('Вы действительно хотите удалить запись?')
                ->method('remove')
                ->canSee($this->exist),
        ];
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function remove(Request $request): RedirectResponse
    {
        $routeParameters = $request->route()->parameters;
        $routeValue = Arr::first(array_values($routeParameters));

        $entity = $this->entity();
        $entity = $entity
            ->newQuery()
            ->where($entity->getRouteKeyName(), $routeValue)
            ->firstOrFail();

        $entity->delete();

        Alert::success('Запись удалена');

        return redirect()->route($this->redirectAfterDeletingRouteName);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function save(Request $request): RedirectResponse
    {
        $entity = $this->getCurrentEntity();
        $rules = $this->getSaveEntityRules($entity, $request);
        $messages = $this->getSaveEntityRuleMessages($entity, $request);
        $input = $request->input();

        Validator::validate($input, $rules, $messages);

        $routeKey = $this->getRouteParameterKey();
        $data = $request->get($routeKey);
        $entity = $this->saving($entity, $data);

        Alert::success('Данные успешно обновлены');

        return redirect()->route($this->redirectAfterSavingRouteName, [
            $routeKey => $entity
        ]);
    }

    /**
     * @param Model $entity
     * @param array $data
     * @return Model
     */
    public function saving(Model $entity, array $data): Model
    {
        $entity->fill($data);
        $entity->save();

        return $entity;
    }
}
