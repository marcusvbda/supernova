<?php

namespace marcusvbda\supernova;

class Panel
{
    public $fields = [];
    public $label;
    public $type = "fields";
    public $visible = true;

    public static function make($label, $type = "fields"): Panel
    {
        return new static($label, $type);
    }

    public function __construct($label, $type)
    {
        $this->label = $label;
        $this->type = $type;
    }

    public function fields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function canSee($val): Panel
    {
        $this->visible = $val;
        return $this;
    }
}
