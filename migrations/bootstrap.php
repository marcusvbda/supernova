<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use marcusvbda\supernova\seeders\PermissionSeeder;

return new class extends Migration
{
    public function up(): void
    {
        $this->createAccessGroups();
        $this->createPermissions();
        $this->createUsers();
        $aclSeeder = new PermissionSeeder();
        $aclSeeder->makePermissions('Grupos de Acesso', 'access-groups');
        $aclSeeder->makePermissions('Usuários', 'users');
    }

    private function createPermissions()
    {
        Schema::create("permission_types", function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create("permissions", function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('key');
            $table->unsignedBigInteger("type_id");
            $table->foreign("type_id")
                ->references("id")
                ->on("permission_types")
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create("access_group_permissions", function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger("access_group_id");
            $table->foreign("access_group_id")
                ->references("id")
                ->on("access_groups")
                ->onDelete("cascade");
            $table->unsignedBigInteger("permission_id");
            $table->foreign("permission_id")
                ->references("id")
                ->on("permissions")
                ->onDelete("cascade");
            $table->primary(["access_group_id", "permission_id"]);
        });
    }

    private function createAccessGroups()
    {
        Schema::create("access_groups", function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    private function createUsers()
    {
        Schema::create("users", function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email');
            $table->boolean('dark_mode')->default(false);
            $table->string('role')->default("user");
            $table->jsonb('avatar')->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger("access_group_id")->nullable();
            $table->foreign("access_group_id")
                ->references("id")
                ->on("access_groups")
                ->onDelete("set null");
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("users");
        Schema::dropIfExists("access_groups");
        Schema::dropIfExists("permissions");
        Schema::dropIfExists("permission_types");
        Schema::dropIfExists("access_group_permissions");
        Schema::dropIfExists("teams");

        $aclSeeder = new PermissionSeeder();
        $aclSeeder->deletePermissionType('Grupos de Acesso');
        $aclSeeder->deletePermissionType('Usuários');
        $aclSeeder->deletePermissionType('Permissões');
    }
};
