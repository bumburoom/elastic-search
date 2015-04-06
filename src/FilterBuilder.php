<?php

namespace Cake\ElasticSearch;

use Elastica\Filter;
use Elastica\Filter\AbstractFilter;
use Elastica\Query\AbstractQuery;

class FilterBuilder
{

    public function between($field, $from, $to)
    {
        return $this->range($field, [
            'gte' => $from,
            'lte' => $to
        ]);
    }

    public function bool()
    {
        return new Filter\Bool();
    }

    public function exists($field)
    {
        return new Filter\Exists($field);
    }

    public function geoBoundingBox($field, array $coordinates)
    {
        return new Filter\GeoBoundingBox($field, $coordinates);
    }

    public function geoDistance($field, $location, $distance)
    {
        return new Filter\GeoDistance($field, $location, $distance);
    }

    public function geoDistanceRange($field, $location, array $ranges)
    {
        return new Filter\GeoDistanceRange($field, $location, $ranges);
    }

    public function geoPolygon($field, array $geoPoints)
    {
        return new Filter\GeoPolygon($field, $geoPoints);
    }

    public function geoShape($field, array $geoPoints)
    {
        return new Filter\GeoShape($field, $geoPoints);
    }

    public function geoHashCell($field, $location, $precision = -1, $neighbors = false)
    {
        return new Filter\GeohashCell($field, $location, $precision, $neighbors);
    }

    public function gt($field, $value)
    {
        return $this->range($field, ['gt' => $value]);
    }

    public function gte($field, $value)
    {
        return $this->range($field, ['gte' => $value]);
    }

    public function hasChild($query, $type = null)
    {
        return new Filter\HasChild($query, $type);
    }

    public function hasParent()
    {
        return new Filter\HasParent($query, $type);
    }

    public function ids(array $ids = [], $type = null)
    {
        return new Filter\Ids($type, $ids);
    }

    public function indices(AbstractFilter $filter, array $indices)
    {
        return new Filter\Indices($filter, $indices);
    }

    public function limit($limit)
    {
        return new Filter\Limit((int)$limit);
    }

    public function matchAll()
    {
        return new Filter\MatchAll();
    }

    public function lt($field, $value)
    {
        return $this->range($field, ['lt' => $value]);
    }

    public function lte($field, $value)
    {
        return $this->range($field, ['lte' => $value]);
    }

    public function missing($field = '')
    {
        return new Filter\Missing($field);
    }

    public function nested($path, $filter)
    {
        $nested = new Filter\Nested();
        $nested->setPath($path);

        if ($filter instanceof $filter) {
            $nested->setFilter($filter);
        }

        if ($filter instanceof AbstractQuery) {
            $nested->setQuery($filter);
        }
        return $nested;
    }

    public function not($filter)
    {
        return new Filter\BoolNot($filter);
    }

    public function prefix($field, $prefix)
    {
        return new Filter\Prefix($field, $prefix);
    }

    public function query($query)
    {
        return new Filter\Query($query);
    }

    public function range($field, array $args)
    {
        return new Filter\Range($field, $args);
    }

    public function regexp($field, $regexp, array $options = [])
    {
        return new Filter\Regexp($field, $args);
    }

    public function script($script)
    {
        return new Filter\Script($script);
    }

    public function term($field, $value)
    {
        return new Filter\Term([$field => $value]);
    }

    public function terms($field, $values)
    {
        return new Filter\Terms($field, $values);
    }

    public function type($type)
    {
        return new Filter\Type($type);
    }

    public function and_()
    {
        $filters = func_get_args();
        $boolFilter = $this->bool();

        foreach ($filters as $k => $filter) {
            if ($filter instanceof Filter\Bool) {
                $bool = $filter;
                unset($filters[$k]);
                break;
            }
        }

        foreach ($filters as $filter) {
            $bool->addMust($filter);
        }

        return $bool;
    }

    public function or_()
    {
        $filters = func_get_args();
        $or = new Filter\BoolOr();

        foreach ($filters as $filter) {
            $or->addFilter($filter);
        }

        return $or;
    }

    public function __call($method, $args)
    {
        if (in_array($method, ['and', 'or'])) {
            return call_user_func_array([$this, $method . '_'], $args);
        }
        throw new \BadMethodCallException('Cannot build filter ' . $method);
    }
}
