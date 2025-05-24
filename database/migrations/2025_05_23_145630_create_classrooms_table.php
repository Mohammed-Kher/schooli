<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('classrooms')) {
            Schema::create('classrooms', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('classroom_id')->constrained('classrooms');
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('classroom_id')->constrained('classrooms');
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('homeworks')) {
            Schema::create('homeworks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subject_id')->constrained('subjects');
                $table->string('name');
                $table->string('description');
                $table->string('due_date');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->date('date');
                $table->enum('status', ['present', 'absent', 'late']);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('notes')) {
            Schema::create('notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('teacher_name');
                $table->string('title');
                $table->text('content');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('subjects');
    }
};
