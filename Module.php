<?php

namespace marcusvbda\supernova;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class Module
{
    public $defaultPermissions = [
        "view_index" => true,
        "view_details" => true,
        "create" => true,
        "edit" => true,
        "delete" => true
    ];

    public function permissions()
    {
        return $this->defaultPermissions;
    }

    public function model(): string
    {
        return "your model here ...";
    }

    public function getQty(): int
    {
        return $this->makeModel()->count();
    }

    public function title($page): string
    {
        $name = $this->name();
        return match ($page) {
            'index' =>  data_get($name, 1, $this->id()),
            'details' =>  'Detalhes de ' . strtolower(data_get($this->name(), 0)),
            'create' =>  'Cadastro de ' . strtolower(data_get($this->name(), 0)),
            'edit' =>  'Edição de ' . strtolower(data_get($this->name(), 0)),
            default => $this->id()
        };
    }

    public function id(): string
    {
        $name = class_basename(get_class($this));
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $name));
    }

    public function makeModel($init = null): mixed
    {
        if (!$init) {
            return app()->make($this->model());
        } else {
            $splitted = explode(".", $init);
            if (count($splitted) !== 3) return app()->make($this->model());
            $application = app()->make(config('supernova.application', Application::class));
            $module = $application->getModule($splitted[0]);
            $field = $module->getField($splitted[2]);
            if (!$field) return app()->make($this->model());
            $model = $module->makeModel()->findOrFail($splitted[1]);
            $queryAction = $field->query;
            return $queryAction($model);
        }
    }

    public function canViewIndex(): bool
    {
        $permissions = array_merge($this->defaultPermissions, $this->permissions());
        return $permissions["view_index"];
    }

    public function details($entity): View
    {
        $module = $this;
        return view("supernova::modules.details", compact("module", "entity"));
    }

    public function create(): View
    {
        $module = $this;
        return view("supernova::modules.create", compact("module"));
    }

    public function edit($entity): View
    {
        $module = $this;
        return view("supernova::modules.edit", compact("module", "entity"));
    }

    public function index(): View
    {
        $module = $this;
        return view("supernova::modules.index", compact("module"));
    }

    public function name(): array
    {
        $id = $this->id();
        $singular = ucfirst((substr($id, -1) === 's') ? substr($id, 0, -1) : $id);
        $plural = ucfirst((substr($id, -1) === 's') ? $id : $id . 's');
        return [$singular, $plural];
    }

    public function subMenu(): ?string
    {
        if (!$this->canViewIndex()) return null;
        return null;
    }

    public function menu(): ?string
    {
        if (!$this->canViewIndex()) return null;
        $sub = $this->subMenu();
        $menu = $this->name()[1];
        $url = "/" . $this->id();
        return $sub ? "$sub.$menu{href='$url'}" : "$menu{href='$url'}";
    }

    public function metrics(): array
    {
        $moduleId = $this->id();
        $cards[] = <<<BLADE
            @livewire('supernova::counter-card',['module' => '$moduleId'])
        BLADE;
        return $cards;
    }

    public function dashboardMetrics(): array
    {
        return $this->metrics();
    }

    public function canCreate(): bool
    {
        $permissions = array_merge($this->defaultPermissions, $this->permissions());
        return $permissions["create"];
    }

    public function dataTable(): array
    {
        $tableColumns = $this->getTableColumns();
        $columns = [];
        foreach ($tableColumns as $column) {
            $filterType = FILTER_TYPES::TEXT;
            if ($column === "id") $filterType = FILTER_TYPES::NUMBER_RANGE;
            if ($column === "created_at" || $column === "updated_at") $filterType = FILTER_TYPES::DATE_RANGE;
            $columns[] =  Column::make($column)->searchable()->sortable()->filterable($filterType);
        }
        return $columns;
    }

    public function getTableColumns(): array
    {
        $model = $this->makeModel();
        return $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());
    }

    public function perPage(): array
    {
        return [10, 25, 50, 100];
    }

    public function defaultSort(): string
    {
        return "id|desc";
    }

    public function canViewDetails(): bool
    {
        $permissions = array_merge($this->defaultPermissions, $this->permissions());
        return $permissions["view_details"];
    }

    public function canEdit(): bool
    {
        $permissions = array_merge($this->defaultPermissions, $this->permissions());
        return $permissions["edit"];
    }

    public function canEditRow($row): bool
    {
        return $this->canEdit();
    }

    public function canDelete(): bool
    {
        $permissions = array_merge($this->defaultPermissions, $this->permissions());
        return $permissions["delete"];
    }

    public function canDeleteRow($row): bool
    {
        return $this->canDelete();
    }

    public function getDataTableVisibleColumns(): array
    {
        $columns = $this->dataTable();
        $columns = collect($columns)->filter(fn ($column) => $column->visible)->toArray();
        return $columns;
    }

    public function applyFilters($model, $searchText, $filters, $sort): mixed
    {
        $columns = $this->getDataTableVisibleColumns();
        if ($searchText) {
            $model = $model->where(function ($query) use ($columns, $searchText) {
                foreach ($columns as $column) {
                    if ($column->searchable) {
                        $query->orWhere($column->name, "ILIKE", "%{$searchText}%");
                    }
                }
            });
        }

        $model = $model->where(function ($query) use ($columns, $filters) {
            foreach ($columns as $column) {
                if ($column->filterable) {
                    $callback = $column->filter_callback;
                    $val = data_get($filters, $column->name, '');
                    if (is_callable($callback)) {
                        if (in_array($column->filter_type, [FILTER_TYPES::NUMBER_RANGE->value, FILTER_TYPES::DATE_RANGE->value])) {
                            $min = data_get($filters, $column->name . "[0]");
                            $max = data_get($filters, $column->name . "[1]");
                            $val = $min || $max ? [$min, $max] : null;
                        }
                        $callback($query, $val);
                    }
                }
            }
        });

        return $model->orderBy($sort[0], $sort[1]);
    }

    protected function isApi(): bool
    {
        return request()->wantsJson();
    }

    public function createBtnText(): string
    {
        $name = $this->name();
        return "Criar " . strtolower($name[0]);
    }

    public function fields($row, $page): array
    {
        $tableColumns = $this->getTableColumns();
        $fields = [];
        foreach ($tableColumns as $column) {
            if (in_array($column, ["id", "created_at", "updated_at"])) continue;
            $fields[] =  Field::make($column)->type(FIELD_TYPES::TEXT);
        }
        return $fields;
    }

    public function getVisibleFieldPanels($panelFallback = "", $entity = null, $page = null): array
    {
        $fields = $this->fields($entity, $page);
        $fieldsWithoutPanel = collect($fields)->filter(function ($field) {
            return $field instanceof Field && $field->visible && $field->type !== FIELD_TYPES::MODULE->value;
        })->toArray();

        $panels = [];

        if (count($fieldsWithoutPanel)) {
            $title = strtolower($this->name()[0]);
            $panels[] = Panel::make($panelFallback ? ($panelFallback . ' ' . $title) : $title)->fields($fieldsWithoutPanel);
        }

        foreach ($fields as $panel) {
            $panelFields = [];
            if (data_get($panel, "visible")) {
                foreach (data_get($panel, 'fields', []) as $field) {
                    if (data_get($field, "visible") && data_get($field, "type") !== FIELD_TYPES::MODULE->value) {
                        $panelFields[] = $field;
                    }
                }
                if (count($panelFields)) {
                    $panels[] = $panel;
                }
            }
        }

        $fieldResources = collect($fields)->filter(function ($field) {
            return $field->visible && $field->type === FIELD_TYPES::MODULE->value;
        })->toArray();

        if (count($fieldResources)) {
            $panels[] = Panel::make("", "resources")->fields($fieldResources);
        }
        return $panels;
    }


    public function processFieldDetail($entity, $field): string
    {
        $detailCallback = $field->detailCallback;
        if ($detailCallback && is_callable($detailCallback)) {
            return $detailCallback($entity);
        }
        return config("supernova.placeholder_no_data", "<span>   -   </span>");
    }

    public function onDelete($entity): void
    {
        $entity->delete();
    }

    public function onSaved($id): int
    {
        return $id;
    }

    public function onPostSave($model, $values): void
    {
        foreach ($values as $field => $value) {
            $callback = $model->{$field}();
            $isCollection = $callback instanceof Collection;
            if ($callback && !$isCollection) {
                $callback->sync($value);
            }
        }
    }

    public function getField($field): ?Field
    {
        $panels = $this->fields(null, null);
        foreach ($panels as $panel) {
            if ($panel instanceof Panel) {
                foreach ($panel->fields as $f) {
                    if ($f->field === $field) {
                        return $f;
                    }
                }
            } elseif ($panel->field === $field) {
                return $panel;
            }
        }
        return null;
    }

    public function onSave($id, $values, $info = []): int
    {
        $parent_id = data_get($info, "parent_id");
        $parent_module = data_get($info, "parent_module");
        if ($parent_id && $parent_module) {
            $application = app()->make(config('supernova.application', Application::class));
            $parentModule = $application->getModule($parent_module, false);
            $relation = $this->id();
            $camelRelation = lcfirst(str_replace(" ", "", ucwords(str_replace("-", " ", $relation))));
            $parentModel = $parentModule->makeModel()->findOrFail($parent_id);
            $model = $id ? $parentModel->{$camelRelation}()->findOrFail($id) : $parentModel->{$camelRelation}()->make();
            $model->fill($values['save']);
            $model->save();

            return $this->onSaved($model->id);
        } else {
            $model = $id ? $this->makeModel()->findOrFail($id) : $this->makeModel();
            $model->fill($values['save']);
        }
        $model->save();

        $this->onPostSave($model, $values['post_save']);
        return $this->onSaved($model->id);
    }
}
