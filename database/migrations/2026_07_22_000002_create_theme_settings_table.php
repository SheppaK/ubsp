<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->string('color_lavender')->default('#b8b8d1');
            $table->string('color_indigo')->default('#5b5f97');
            $table->string('color_indigo_dark')->default('#4a4d7a');
            $table->string('color_amber')->default('#ffc145');
            $table->string('color_cream')->default('#fffffb');
            $table->string('color_coral')->default('#ff6b6c');
            $table->string('color_page_dark')->default('#2a2d52');
            $table->string('color_surface_dark')->default('#45497a');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
