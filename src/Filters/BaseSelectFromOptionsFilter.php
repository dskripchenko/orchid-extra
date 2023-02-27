<?php

declare(strict_types=1);

namespace Dskripchenko\OrchidExtra\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BaseSelectFromOptionsFilter extends Filter
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
     * @var array Список доступных значений фильтра
     */
    protected array $options;

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

    /**
     * @param string $name
     * @param string $field
     * @param array $options
     * @param string|null $parameter
     */
    public function __construct(
        string $name,
        string $field,
        array $options,
        string $parameter = null
    ) {
        $this->name = $name;
        $this->filterField = $field;
        $this->options = $options;
        $this->parameter = $parameter ?: $field;

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
                ->options($this->options)
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
