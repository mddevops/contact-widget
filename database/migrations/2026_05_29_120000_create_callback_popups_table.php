<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('callback_popups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->json('list_items')->nullable();
            $table->string('list_style')->default('check');
            $table->string('button_label')->default('Заказать звонок');
            $table->string('button_bg_color')->default('#0571D5');
            $table->string('button_text_color')->default('#ffffff');
            $table->string('image')->nullable();
            $table->string('image_position')->default('right');
            $table->json('show_schedule')->nullable();
            $table->json('page_ids')->nullable();
            $table->boolean('show_on_all_pages')->default(true);
            $table->boolean('show_on_exit')->default(false);
            $table->unsignedTinyInteger('session_show_limit')->default(1);
            $table->boolean('status')->default(true);
            $table->unsignedInteger('order_col')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('callback_popups');
    }
};
