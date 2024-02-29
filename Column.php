<?php

namespace marcusvbda\supernova;

use marcusvbda\supernova\FILTER_TYPES;

class Column
{
    public $name;
    public $label;
    public $searchable = false;
    public $align = "justify-start";
    public $filterable = false;
    public $model;
    public $filter_type;
    public $filter_options = [];
    public $option_keys = ['value' => 'id', 'label' => 'name'];
    public $filter_options_callback;
    public $filter_callback;
    public $sortable = false;
    public $width;
    public $minWidth;
    public $action;
    public $visible = true;
    public $filter_options_limit;

    public static function make($name, $label = null): Column
    {
        return new static($name, $label);
    }

    public function __construct($name, $label = null)
    {
        $this->name = $name;
        $this->label = $label ? $label : $name;
        $this->action = fn ($row) => $row->{$name};
        $this->filter_callback = fn ($query, $value) => $value ? $query->where($this->name, "like", "%{$value}%") : $value;
    }

    public function name($val): Column
    {
        $this->name = $val;
        return $this;
    }

    public function label($val): Column
    {
        $this->label = $val;
        return $this;
    }

    public function searchable($value = true): Column
    {
        $this->searchable = $value;
        return $this;
    }

    public function alignRight(): Column
    {
        $this->align = "justify-end";
        return $this;
    }

    public function alignCenter(): Column
    {
        $this->align = "justify-center";
        return $this;
    }

    public function alignLeft(): Column
    {
        $this->align = "justify-start";
        return $this;
    }

    public function filterable(FILTER_TYPES $type, $filter_options_limit = null): Column
    {
        $this->filterable = true;
        $this->filter_type = $type->value;
        if (!$this->minWidth) {
            $this->minWidth("200px");
        }
        if ($type->value === FILTER_TYPES::SELECT->value) {
            $this->filter_options_limit = $filter_options_limit;
            $this->filter_callback = function ($query, $value) {
                $values = array_map(function ($row) {
                    return $row;
                }, ($value ? $value : []));
                if (count($values) <= 0) return $query;
                if (!$this->model) return $query->whereIn($this->name, $values);
                $query->whereHas($this->name, function ($q) use ($values) {
                    $q->whereIn(data_get($this->option_keys, 'value'), $values);
                });
                return $query;
            };
        }
        if ($type->value === FILTER_TYPES::NUMBER_RANGE->value) {
            $this->filter_callback = function ($query, $value) {
                if (!$value) return $query;
                $query->where(function ($q) use ($value) {
                    if ($value[0]) $q->where($this->name, ">=", $value[0]);
                    if ($value[1]) $q->where($this->name, "<=", $value[1]);
                });
            };
        }
        if ($type->value === FILTER_TYPES::DATE_RANGE->value) {
            $this->filter_callback = function ($query, $value) {
                if (!$value) return $query;
                $query->where(function ($q) use ($value) {
                    if ($value[0]) $q->whereDate($this->name, ">=", $value[0]);
                    if ($value[1]) $q->whereDate($this->name, "<=", $value[1]);
                });
            };
        }
        if ($type->value === FILTER_TYPES::DATE->value) {
            $this->filter_callback = function ($query, $value) {
                if (!$value) return $query;
                $query->whereDate($this->name, $value);
            };
        }
        return $this;
    }

    public function optionKeys($keys): Column
    {
        $this->option_keys = $keys;
        return $this;
    }

    public function filterOptions($options): Column
    {
        if (is_array($options)) {
            $this->filter_options = array_map(function ($row) {
                if (is_array($row) && array_key_exists("value", $row) && array_key_exists("label", $row)) {
                    return $row;
                }
                return ["value" => $row, "label" => $row];
            }, $options);
        } else {
            $this->model = app()->make($options);
            $this->filter_options_callback = function () {
                return $this->model->orderBy(data_get($this->option_keys, 'label'), "asc")->get()->map(function ($row) {
                    return ["value" => $row->{data_get($this->option_keys, 'value')}, "label" => $row->{data_get($this->option_keys, 'label')}];
                })->toArray();
            };
            $this->action = fn ($row) => $row->{$this->name}->{$this->option_keys['label']};
        }
        return $this;
    }

    public function filterOptionsCallback($callback): Column
    {
        $this->filter_options_callback = $callback;
        return $this;
    }

    public function filterCallback($callback): Column
    {
        $this->filter_callback = $callback;
        return $this;
    }

    public function sortable($value = true): Column
    {
        $this->sortable = $value;
        return $this;
    }

    public function width($value): Column
    {
        $this->width = $value;
        return $this;
    }

    public function minWidth($value): Column
    {
        $this->minWidth = $value;
        return $this;
    }

    public function callback($value): Column
    {
        $this->action = $value;
        return $this;
    }

    public function canSee($value): Column
    {
        $this->visible = $value;
        return $this;
    }
}
