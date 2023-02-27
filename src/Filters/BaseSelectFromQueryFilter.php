<?php

declare(strict_types=1);

namespace Dskripchenko\OrchidExtra\Filters;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BaseSelectFromQueryFilter extends Filter
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
     * @var Builder Запрос по которому будет производиться выборка значений фильтра
     */
    protected Builder $query;

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

    /**
     * @param string $name
     * @param string $field
     * @param Builder $query
     * @param string $entityName
     * @param string $entityKey
     * @param string|null $parameter
     */
    public function __construct(
        string $name,
        string $field,
        Builder $query,
        string $entityName = 'name',
        string $entityKey = 'id',
        string $parameter = null
    ) {
        $this->name = $name;
        $this->filterField = $field;
        $this->query = $query;
        $this->entityName = $entityName;
        $this->entityKey = $entityKey;
        $this->parameter = $parameter ?: $field;

        $this->parameters = $this->parameters();
        parent::__construct();
    }

    /**
     * @return Model
     * @throws Exception
     */
    public function getEntity(): Model
    {
        return $this->getEntityBuilder()->getModel();
    }

    /**
     * @return Builder
     * @throws Exception
     */
    public function getEntityBuilder(): Builder
    {
        return $this->query;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getEntityClass(): string
    {
        return get_class($this->getEntity());
    }

    /**
     * @return array|Field[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function display(): array
    {
        return [
            Select::make($this->parameter)
                ->fromQuery($this->getEntityBuilder(), $this->entityName, $this->entityKey)
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
     * @throws Exception
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
