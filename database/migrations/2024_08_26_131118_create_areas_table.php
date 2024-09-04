<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('area_number');
            $table->foreignIdFor(\App\Models\Service::class);
            $table->foreignIdFor(\App\Models\DeparturePoint::class, 'where_from');
            $table->foreignIdFor(\App\Models\DeparturePoint::class, 'where_to');
            $table->string('terms')->nullable();
//            $table->unique(['service_id', 'where_from', 'where_to']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
