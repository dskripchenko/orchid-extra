<?php

declare(strict_types=1);

namespace Dskripchenko\OrchidExtra\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class AbstractSwitcherFilter extends Filter
{
    /**
     * @var string Название фильтра
     */
    protected string $name;

    /**
     * @var string Параметр в адресе url
     */
    protected string $parameter;

    /**
     * @var string Поле по которому производится фильтрация
     * Используется в дефолтном методе run
     */
    protected string $filterField;

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string[]|null
     */
    public function parameters(): ?array
    {
        return [$this->parameter];
    }

    public function __construct()
    {
        $this->parameters = $this->parameters();
        parent::__construct();
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function display(): array
    {
        return [
            Select::make($this->parameter)
                ->options([
                    'yes' => 'Да',
                    'no' => 'Нет',
                ])
                ->empty()
                ->value($this->request->get($this->parameter))
                ->title($this->name()),
        ];
    }

    /**
     * @param Builder $builder
     * @return Builder
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(Builder $builder): Builder
    {
        $filterValue = $this->request->get($this->parameter);
        if (!$filterValue) {
            return $builder;
        }
        $filterValue = $filterValue === 'yes' ? 1 : 0;
        return $builder->where($this->filterField, $filterValue);
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function value(): string
    {
        $filterValue = $this->request->get($this->parameter);
        if (!$filterValue) {
            return '';
        }
        return "{$this->name} : {$filterValue}";
    }
}
