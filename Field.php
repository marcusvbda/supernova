<?php

namespace marcusvbda\supernova;

class Field
{
    public $field;
    public $label;
    public $resource;
    public $noData;
    public $uploadDisk;
    public $model;
    public $query;
    public $disabled = false;
    public $component;
    public $limit = 1;
    public $rows = 6;
    public $multiple = false;
    public $detailCallback;
    public $mask = "";
    public $uploadPath = "uploads";
    public $type = "text";
    public $rules = [];
    public $messages = [];
    public $option_keys = ['value' => 'id', 'label' => 'name'];
    public $options_callback;
    public $options = [];
    public $previewCallback = null;
    public $visible = true;

    public function isNamespace($val)
    {
        return strpos($val, "\\") !== false;
    }

    public static function make($field, $label = null): Field
    {
        return new static($field, $label);
    }

    public function component($component = null): Field
    {
        $this->component = $component;
        return $this;
    }

    public function __construct($field, $label = null)
    {
        $this->field = $field;
        if (!$this->isNamespace($field)) {
            $this->label = $label ? $label : $field;
            $this->noData = config("supernova.placeholder_no_data", "<span>   -   </span>");
            $this->detailCallback = fn ($entity) => @$entity?->{$this->field} ?? $this->noData;
        } else {
            $this->module = $field;
            $parentModule = app()->make($field);
            $this->field = $parentModule->id();
            $this->type = FIELD_TYPES::MODULE->value;
            $field = $this->field;
            $this->query = function ($row) use ($field) {
                $camelField = lcfirst(str_replace(" ", "", ucwords(str_replace("-", " ", $field))));
                return @$row->{$camelField} ? $row->{$camelField}() : $row;
            };
        }
    }

    public function mask($mask): Field
    {
        $this->mask = $mask;
        return $this;
    }

    public function type($type, $relation = null, $path = null): Field
    {
        $this->type = is_string($type) ? $type : @$type->value;

        if ($this->type === FIELD_TYPES::TEXT->value) {
            $this->detailCallback = fn ($entity) => @$entity?->{$this->field} ?? $this->noData;
        } elseif ($this->type === FIELD_TYPES::SELECT->value) {
            $this->detailCallback = function ($entity)  use ($relation) {
                if (!$this->model) {
                    if (!$this->multiple) {
                        $value = @$entity?->{$this->field} ?? null;
                        $option = collect($this->options)->first(fn ($row) => $row["value"] == $value);
                        return $option ? $option["label"] : $this->noData;
                    } else {
                        $value = @$entity?->{$this->field} ?? [];
                        $valueContent = collect($this->options)->filter(fn ($row) => in_array($row["value"], $value))->map(fn ($row) => $row["label"])->implode(", ");
                        return $valueContent ? $valueContent : $this->noData;
                    }
                } else {
                    if (!$this->multiple) {
                        $value = @$entity?->{$relation ? $relation : $this->field} ?? null;
                        if (!$value) return $this->noData;
                        $valueContent = @$value?->{data_get($this->option_keys, 'label')} ?? null;
                        return $valueContent ? $valueContent : $this->noData;
                    } else {
                        $value = @$entity?->{$relation ? $relation : $this->field} ?? [];
                        if (count($value) == 0) return $this->noData;
                        $valueContent = $value->map(fn ($row) => $row->{data_get($this->option_keys, 'label')})->implode(", ");
                        return $valueContent ? $valueContent : $this->noData;
                    }
                }
            };

            $this->options_callback = function () {
                return $this->model->orderBy(data_get($this->option_keys, 'label'), "asc")->get()->map(function ($row) {
                    return ["value" => $row->{data_get($this->option_keys, 'value')}, "label" => $row->{data_get($this->option_keys, 'label')}];
                })->toArray();
            };
        } elseif ($this->type === FIELD_TYPES::UPLOAD->value) {
            $this->uploadDisk = $relation ? $relation : config("filesystems.default");
            if ($path) {
                $this->uploadPath = $path;
            }
            $this->previewCallback = function ($file) {
                if (is_array($file)) {
                    $id = data_get($file, "id");
                    $path = data_get($file, "path");
                    $disk = data_get($file, "disk");
                    $name = data_get($file, "original_name");
                    $extension = data_get($file, "extension");
                    $path = str_replace("/", "-", $path);
                    $fileName = $path . "-" . $id . "." . $extension;
                    $url = route("supernova.modules.upload-download", ["disk" => $disk, "file" => $fileName]);
                } else {
                    $url = $file->temporaryUrl();
                    $name = $file->getClientOriginalName();
                }
                return <<<BLADE
                    <a href="$url" target="_BLANK" title="$name">
                        <img src="$url" alt="$name" class="w-40 h-40 rounded border border-gray-300"/>
                    </a>
                BLADE;
            };

            $this->detailCallback = function ($row) {
                $files = $row->{$this->field} ?? [];
                if (!count($files)) return $this->noData;
                $callback = $this->previewCallback;
                $rows = collect($files)->map(function ($file) use ($callback) {
                    return $callback($file);
                })->implode("");
                return <<<BLADE
                    <div class="flex flex-wrap gap-2">
                        $rows
                    </div>
                 BLADE;
            };
        }
        return $this;
    }

    public function preview($callback): Field
    {
        if (is_callable($callback)) {
            $this->previewCallback = $callback;
        } else {
            if ($callback === UPLOAD_PREVIEW::AVATAR) {
                $this->previewCallback = function ($file) {
                    if (is_array($file)) {
                        $id = data_get($file, "id");
                        $path = data_get($file, "path");
                        $disk = data_get($file, "disk");
                        $name = data_get($file, "original_name");
                        $extension = data_get($file, "extension");
                        $path = str_replace("/", "-", $path);
                        $fileName = $path . "-" . $id . "." . $extension;
                        $url = route("supernova.modules.upload-download", ["disk" => $disk, "file" => $fileName]);
                    } else {
                        $url = $file->temporaryUrl();
                        $name = $file->getClientOriginalName();
                    }
                    return <<<BLADE
                        <a href="$url" target="_BLANK" title="$name">
                            <img src="$url" alt="$name" class="w-40 h-40 rounded border border-gray-300"/>
                        </a>
                    BLADE;
                };
            }
        }
        return $this;
    }

    public function rules($val, $messages = []): Field
    {
        $this->rules = $val;
        $this->messages = $messages;
        return $this;
    }

    public function canSee($val): Field
    {
        $this->visible = $val;
        return $this;
    }

    public function detailCallback($callback): Field
    {
        $this->detailCallback = $callback;
        return $this;
    }

    public function optionKeys($keys): Field
    {
        $this->option_keys = $keys;
        return $this;
    }

    public function options($options): Field
    {
        if (is_array($options)) {
            $this->options = array_map(function ($row) {
                if (is_array($row) && array_key_exists("value", $row) && array_key_exists("label", $row)) {
                    return $row;
                }
                return ["value" => $row, "label" => $row];
            }, $options);
        } else {
            $this->model = app()->make($options);
            $this->options = $this->model->orderBy(data_get($this->option_keys, 'label'), "asc")->get()->map(function ($row) {
                return ["value" => $row->{data_get($this->option_keys, 'value')}, "label" => $row->{data_get($this->option_keys, 'label')}];
            })->toArray();
        }
        return $this;
    }

    public function multiple($limit = 9999999999): Field
    {
        $this->multiple = true;
        $this->limit = $limit;
        return $this;
    }

    public function rows($rows): Field
    {
        $this->rows = $rows;
        return $this;
    }

    public function disabled($val = true): Field
    {
        $this->disabled = $val;
        return $this;
    }
}
