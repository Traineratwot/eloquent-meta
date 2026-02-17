<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('eloquent_meta', function (Blueprint $table) {
            $table->id();

            $table->morphs('model');
            $table->string('column');
            $table->jsonb('data');

            $table->unique(['model_id', 'model_type', 'column']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eloquent_column_meta');
    }
};
