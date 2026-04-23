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
            $table->string('project_name', 200);
            $table->string('location', 300)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status', 50)->default('Ongoing'); // Ongoing, Completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
