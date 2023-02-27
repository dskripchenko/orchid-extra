<?php

namespace Dskripchenko\OrchidExtra\Components;

use Dskripchenko\OrchidExtra\Filters\BaseDateFilter;
use Dskripchenko\OrchidExtra\Filters\BaseInputFilter;
use Dskripchenko\OrchidExtra\Filters\BaseSelectFromModelFilter;
use Dskripchenko\OrchidExtra\Filters\BaseSelectFromOptionsFilter;
use Dskripchenko\OrchidExtra\Filters\BaseSelectFromQueryFilter;
use Dskripchenko\OrchidExtra\Filters\BaseSwitcherFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filter;

class SelectionFactory
{
    /**
     * @param string $name
     * @param string $field
     * @param string $format
     * @param bool $range
     * @param string|null $parameter
     * @return Filter
     */
    public function date(
        string $name,
        string $field,
        string $format = 'Y-m-d',
        bool $range = true,
        string $parameter = null
    ): Filter {
        return new BaseDateFilter($name, $field, $format, $range, $parameter);
    }

    /**
     * @param string $name
     * @param string $field
     * @param string $operator
     * @param string|null $parameter
     * @return Filter
     */
    public function input(
        string $name,
        string $field,
        string $operator = 'like',
        string $parameter = null
    ): Filter {
        return new BaseInputFilter($name, $field, $operator, $parameter);
    }

    /**
     * @param string $name
     * @param string $field
     * @param string|null $parameter
     * @return Filter
     */
    public function switcher(
        string $name,
        string $field,
        string $parameter = null
    ): Filter {
        return new BaseSwitcherFilter($name, $field, $parameter);
    }

    /**
     * @param string $name
     * @param string $field
     * @param Model $entity
     * @param string $entityName
     * @param string $entityKey
     * @param string|null $parameter
     * @return Filter
     */
    public function selectFromModel(
        string $name,
        string $field,
        Model $entity,
        string $entityName = 'name',
        string $entityKey = 'id',
        string $parameter = null
    ): Filter {
        return new BaseSelectFromModelFilter($name, $field, $entity, $entityName, $entityKey, $parameter);
    }

    /**
     * @param string $name
     * @param string $field
     * @param Builder $query
     * @param string $entityName
     * @param string $entityKey
     * @param string|null $parameter
     * @return Filter
     */
    public function selectFromQuery(
        string $name,
        string $field,
        Builder $query,
        string $entityName = 'name',
        string $entityKey = 'id',
        string $parameter = null
    ): Filter {
        return new BaseSelectFromQueryFilter($name, $field, $query, $entityName, $entityKey, $parameter);
    }

    /**
     * @param string $name
     * @param string $field
     * @param array $options
     * @param string|null $parameter
     * @return Filter
     */
    public function selectFromOptions(
        string $name,
        string $field,
        array $options,
        string $parameter = null
    ): Filter {
        return new BaseSelectFromOptionsFilter($name, $field, $options, $parameter);
    }
}