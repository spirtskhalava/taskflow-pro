<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->string('color', 7)->default('#6366f1');
            $table->date('deadline')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('owner_id');
            $table->index('is_archived');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
