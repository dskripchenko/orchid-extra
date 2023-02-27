<?php

declare(strict_types=1);

namespace Dskripchenko\OrchidExtra\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\DateTimer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BaseDateFilter extends Filter
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
     * @var string Формат даты
     */
    protected string $dateFormat = 'Y-m-d';

    /**
     * @var bool Фильтровать по промежутку дат
     */
    protected bool $range = true;

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
     * @param string $format
     * @param bool $range
     * @param string|null $parameter
     */
    public function __construct(
        string $name,
        string $field,
        string $format = 'Y-m-d',
        bool $range = true,
        string $parameter = null
    ) {
        $this->name = $name;
        $this->filterField = $field;
        $this->dateFormat = $format;
        $this->range = $range;
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
        if ($this->range) {
            return [
                DateRange::make($this->parameter)
                    ->title($this->name())
                    ->value($this->request->get($this->parameter))
            ];
        }
        return [
            DateTimer::make($this->parameter)
                ->format($this->dateFormat)
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
        if (is_array($filterValue)) {
            return $builder->whereBetween($this->filterField, $filterValue);
        }
        return $builder
            ->where($this->filterField, $filterValue);
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function value(): string
    {
        $filterValue = $this->request->get($this->parameter);
        if (is_array($filterValue)) {
            $start = data_get($filterValue, 'start');
            $end = data_get($filterValue, 'end');
            return "{$start} <= {$this->name} <= {$end}";
        }
        return "{$this->name} : {$filterValue}";
    }
}
