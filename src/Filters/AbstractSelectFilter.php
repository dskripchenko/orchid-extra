<?php

declare(strict_types=1);

namespace Dskripchenko\OrchidExtra\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filter;
use Orchid\Screen\Fields\Select;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class AbstractSelectFilter extends Filter
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
     * @var string Отображаемое значение фильтра
     */
    protected string $entityName = 'name';

    /**
     * @var string Фактическое значение фильтра
     */
    protected string $entityKey = 'id';

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
     * @return Model
     */
    abstract public function getEntity(): Model;

    /**
     * @return Builder
     */
    public function getEntityBuilder(): Builder
    {
        return $this->getEntity()->newQuery();
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return get_class($this->getEntity());
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
                ->fromModel($this->getEntityClass(), $this->entityName, $this->entityKey)
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
        return $builder
            ->where($this->filterField, $this->request->get($this->parameter));
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function value(): string
    {
        /**
         * @var Model $item
         */
        $item = $this->getEntityBuilder()
            ->where($this->entityKey, $this->request->get($this->parameter))
            ->first();

        return "{$this->name} : {$item->getAttributeValue($this->entityName)}";
    }
}
