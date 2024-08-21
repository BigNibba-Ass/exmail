<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->foreignIdFor(\App\Models\DeparturePoint::class, 'where_from');
            $table->foreignIdFor(\App\Models\DeparturePoint::class, 'where_to');
            $table->foreignIdFor(\App\Models\Service::class);
            $table->integer('weight_max');

            $table->decimal('price_nds', 10, 2);
            $table->decimal('price_nds_free', 10, 2);
            $table->integer('terms');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
