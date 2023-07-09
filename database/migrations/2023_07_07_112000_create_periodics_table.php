<?php

use App\Models\House;
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
        Schema::create('periodics', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(House::class)->constrained()->cascadeOnDelete();
            $table->string('periodicity');
            $table->boolean('can_alert')->default(true);
            $table->date('next_service_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodics');
    }
};
