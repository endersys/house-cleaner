<?php

use App\Enums\ServiceStatusEnum;
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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(House::class)->constrained()->cascadeOnDelete();
            $table->date('service_date')->nullable();
            $table->string('status')->default(ServiceStatusEnum::Pending->value);
            $table->string('price')->nullable();
            $table->string('type')->nullable();
            $table->time('started_at')->nullable();
            $table->time('finished_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
