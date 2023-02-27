<?php

declare(strict_types=1);

namespace Dskripchenko\OrchidExtra\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Input;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BaseInputFilter extends Filter
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
     * @var string Оператор сравнения записей =|!=|>|<|like
     */
    protected string $filterOperator = 'like';

    /**
     * @param string $name
     * @param string $field
     * @param string $operator
     * @param string|null $parameter
     */
    public function __construct(
        string $name,
        string $field,
        string $operator = 'like',
        string $parameter = null
    ) {
        $this->name = $name;
        $this->filterField = $field;
        $this->filterOperator = $operator;
        $this->parameter = $parameter ?: $field;

        $this->parameters = $this->parameters();
        parent::__construct();
    }

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
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function display(): array
    {
        return [
            Input::make($this->parameter)
                ->title($this->name())
                ->value($this->request->get($this->parameter)),
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
        if ($this->filterOperator === 'like') {
            $filterValue = "%{$filterValue}%";
        }
        return $builder
            ->where($this->filterField, $this->filterOperator, $filterValue);
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function value(): string
    {
        $filterValue = $this->request->get($this->parameter);
        return "{$this->name} {$this->filterOperator} {$filterValue}";
    }
}
