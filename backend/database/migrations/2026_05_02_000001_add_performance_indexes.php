<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Covering index for Kanban board queries (project + status + position)
            $table->index(['project_id', 'status', 'position'], 'tasks_kanban_index');
        });

        Schema::table('projects', function (Blueprint $table) {
            // Speed up "active projects for user" query used on dashboard
            $table->index(['owner_id', 'is_archived', 'created_at'], 'projects_owner_active_index');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_kanban_index');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_owner_active_index');
        });
    }
};
