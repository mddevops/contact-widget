<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_widgets', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(true);
            $table->boolean('show_on_site')->default(false);
            $table->string('title')->default('Обратная связь');
            $table->foreignId('main_icon_id')->nullable()->constrained('social_icons')->nullOnDelete();
            $table->unsignedSmallInteger('main_icon_size')->default(30);
            $table->unsignedSmallInteger('item_icon_size')->default(18);
            $table->unsignedTinyInteger('item_font_size')->default(14);
            $table->unsignedSmallInteger('main_button_size')->default(48);
            $table->string('main_button_color')->default('#8e36ff');
            $table->string('main_button_text_color')->default('#ffffff');
            $table->string('main_button_hover_color')->default('#de0611');
            $table->string('popup_background')->default('#ffffff');
            $table->unsignedTinyInteger('panel_background_opacity')->default(100);
            $table->unsignedSmallInteger('popup_border_radius')->default(6);
            $table->string('popup_shadow')->default('0 5px 10px rgba(0, 0, 0, 0.1)');
            $table->unsignedSmallInteger('popup_width')->default(280);
            $table->string('position')->default('right');
            $table->unsignedSmallInteger('offset_bottom')->default(40);
            $table->unsignedSmallInteger('offset_side')->default(40);
            $table->string('animation')->default('none');
            $table->boolean('tooltip_enabled')->default(false);
            $table->boolean('show_labels')->default(true);
            $table->string('open_direction')->default('up');
            $table->boolean('mobile_only')->default(false);
            $table->boolean('desktop_only')->default(false);
            $table->json('mobile_settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_widgets');
    }
};
