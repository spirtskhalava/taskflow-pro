<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('status')->default('todo'); // todo, in_progress, in_review, done
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->unsignedInteger('position')->default(0);
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('estimated_hours', 6, 2)->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'position']);
            $table->index('assignee_id');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

