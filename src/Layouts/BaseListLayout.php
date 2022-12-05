<?php

namespace Dskripchenko\OrchidExtra\Layouts;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

abstract class BaseListLayout extends Table
{
    /**
     * @param  Model  $model
     * @return string|null
     */
    abstract public function getEditRoute(Model $model): ?string;

    /**
     * @param  Model  $model
     * @return string|null
     */
    abstract public function getDetailRoute(Model $model): ?string;

    /**
     * @return TD[]
     */
    abstract public function getColumns(): array;

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        $columns = array_values($this->getColumns());
        return [
            TD::make('Действия')
                ->render(function (Model $model) {
                    $actions = [];
                    $editRoute = $this->getEditRoute($model);
                    if ($editRoute) {
                        $actions[] = Link::make('Изменить')->icon('pencil')->href($editRoute);
                    }

                    $detailRoute = $this->getDetailRoute($model);
                    if ($detailRoute) {
                        $actions[] = Link::make('Детальный просмотр')->icon('eye')->href($detailRoute);
                    }

                    $enableFields = ['is_active', 'enable', 'enabled'];
                    $attributes = $model->getAttributes();
                    foreach ($enableFields as $activityKey) {
                        if (array_key_exists($activityKey, $attributes)) {
                            $actions[] = Button::make(!$model->getAttribute($activityKey) ? 'Опубликовать' : 'Скрыть')
                                ->icon(!$model->getAttribute($activityKey) ? 'check' : 'close')
                                ->method('publishing', [
                                    'field' => $activityKey,
                                    'id' => $model->id
                                ]);
                            break;
                        }
                    }


                    return DropDown::make()
                        ->icon('settings')
                        ->list([
                            ...$actions,

                            Button::make('Удалить')
                                ->confirm('Вы действительно хотите удалить запись?')
                                ->icon('trash')
                                ->method('deleting', [
                                    'id' => $model->id
                                ])
                        ]);
                })
                ->cantHide(),

            ...$columns,

            TD::make('created_at', 'Дата создания')
                ->sort()
                ->defaultHidden()
                ->render(function (Model $model) {
                    $attributes = $model->getAttributes();
                    return
                        (array_key_exists('created_at', $attributes) && $model->created_at)
                        ? $model->created_at->toDateTimeString()
                        : 'Не определена';
                }),

            TD::make('updated_at', 'Дата изменения')
                ->sort()
                ->render(function (Model $model) {
                    $attributes = $model->getAttributes();
                    return
                        (array_key_exists('updated_at', $attributes) && $model->updated_at)
                        ? $model->updated_at->toDateTimeString()
                        : 'Не определена';
                }),
        ];
    }
}
