<?php

use App\Enums\MaterialStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('price')->nullable();
            $table->string('reference')->nullable();
            $table->string('measurement_unit')->nullable();
            $table->string('expiration_date')->nullable();
            $table->string('shelf')->nullable();
            $table->string('status')->default(MaterialStatusEnum::Active->value);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
