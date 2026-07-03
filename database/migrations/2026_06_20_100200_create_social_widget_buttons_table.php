<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_widget_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('widget_id')->constrained('social_widgets')->cascadeOnDelete();
            $table->foreignId('icon_id')->nullable()->constrained('social_icons')->nullOnDelete();
            $table->string('type')->default('custom');
            $table->string('title');
            $table->string('url')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('popup_id')->nullable()->constrained('popups')->nullOnDelete();
            $table->string('popup_title')->nullable();
            $table->text('popup_content')->nullable();
            $table->string('background_color')->default('#8e36ff');
            $table->string('text_color')->default('#ffffff');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('enabled')->default(true);
            $table->string('open_type')->default('url');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_widget_buttons');
    }
};
