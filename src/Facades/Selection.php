<?php

namespace Dskripchenko\OrchidExtra\Facades;

use Dskripchenko\OrchidExtra\Components\SelectionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Orchid\Filters\Filter;

/**
 * @method static Filter date(string $name, string $field, string $format = 'Y-m-d', bool $range = true, string $parameter = null)
 * @method static Filter input(string $name, string $field, string $operator = 'like', string $parameter = null)
 * @method static Filter switcher(string $name, string $field, string $parameter = null)
 * @method static Filter selectFromModel(string $name, string $field, Model $entity, string $entityName = 'name', string $entityKey = 'id', string $parameter = null)
 * @method static Filter selectFromQuery(string $name, string $field, Builder $query, string $entityName = 'name', string $entityKey = 'id', string $parameter = null)
 * @method static Filter selectFromOptions(string $name, string $field, array $options, string $parameter = null)
 */
class Selection extends Facade
{
    /**
     * @see SelectionFactory
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'orchid_extra_selection_factory';
    }
}