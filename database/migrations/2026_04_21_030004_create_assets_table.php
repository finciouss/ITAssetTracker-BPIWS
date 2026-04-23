<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('tag_number', 50)->unique();
            $table->string('name', 200);
            $table->text('specifications')->nullable();
            $table->date('purchase_date');
            $table->string('status', 50)->default('InStock'); // InStock, Allocated, Maintenance, Retired
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
