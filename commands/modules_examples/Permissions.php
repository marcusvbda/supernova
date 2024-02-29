<?php

namespace App\Http\Supernova\Modules;

use App\Models\Permission;
use App\Models\PermissionType;
use Illuminate\Support\Facades\Auth;
use marcusvbda\supernova\Column;
use marcusvbda\supernova\Field;
use marcusvbda\supernova\FIELD_TYPES;
use marcusvbda\supernova\FILTER_TYPES;
use marcusvbda\supernova\Module;

class Permissions extends Module
{
    public function subMenu(): string
    {
        return "Configurações";
    }

    public function name(): array
    {
        return ['Permissão', 'Permissões'];
    }

    public function model(): string
    {
        return Permission::class;
    }

    public function dataTable(): array
    {
        $columns[] = Column::make("id", "Id")->width("200px")
            ->searchable()->sortable()
            ->filterable(FILTER_TYPES::NUMBER_RANGE);
        $columns[] = Column::make("name", "Nome")
            ->searchable()->sortable()
            ->filterable(FILTER_TYPES::TEXT);
        $columns[] = Column::make("key", "Chave")
            ->searchable()->sortable()
            ->filterable(FILTER_TYPES::TEXT);
        $columns[] = Column::make("type", "Tipo")
            ->filterable(FILTER_TYPES::SELECT, 3)
            ->filterOptions(PermissionType::class);
        return $columns;
    }

    public function fields($row, $page): array
    {
        $id = data_get(request()->values, "id");
        return [
            Field::make("name", "Nome")->rules(["required"], ["required" => "O campo nome é obrigatório"]),
            Field::make("key", "Chave")->rules(["required", "unique:permissions,key,$id,id"], [
                "required" => "O campo chave é obrigatório",
                "unique" => "A chave informada já está em uso"
            ]),
            Field::make("type_id", "Tipo")->type(FIELD_TYPES::SELECT, 'type')
                ->options(PermissionType::class)
                ->rules(["required"], ["required" => "O campo tipo é obrigatório"])
        ];
    }

    public function canDelete(): bool
    {
        return Auth::user()->role === "root";
    }

    public function canEdit(): bool
    {
        return Auth::user()->role === "root";
    }

    public function canCreate(): bool
    {
        return Auth::user()->role === "root";
    }
}
