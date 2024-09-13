<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('area_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Service::class);

            // TODO: продумать отношение цен к зонам
            $table->string('area_number');

            $table->float('weight_min');
            $table->float('weight_max');
            $table->decimal('price', 10);
            $table->decimal('price_per_extra', 10)->nullable();
            $table->float('extra_definition')->default(1);

            $table->unique(['service_id', 'area_number', 'weight_min', 'weight_max']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_prices');
    }
};
