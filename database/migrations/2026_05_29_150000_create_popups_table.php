<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('popups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('benefits')->nullable();
            $table->string('button_text')->default('Отправить заявку');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->json('display_rules')->nullable();
            $table->string('template')->nullable();
            $table->json('ab_test')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        if (! Schema::hasTable('callback_popups')) {
            return;
        }

        DB::table('callback_popups')->orderBy('id')->each(function (object $row): void {
            $listItems = json_decode($row->list_items ?? '[]', true) ?: [];
            $benefits = collect($listItems)
                ->pluck('text')
                ->filter()
                ->implode("\n");

            $schedule = json_decode($row->show_schedule ?? '[]', true) ?: [];
            $delays = collect($schedule)
                ->pluck('delay')
                ->map(fn ($delay) => max(0, (int) $delay))
                ->values()
                ->all();

            $imagePosition = in_array($row->image_position ?? '', ['left', 'right'], true)
                ? $row->image_position
                : 'right';

            DB::table('popups')->insert([
                'name' => $row->title,
                'title' => $row->title,
                'subtitle' => $row->subtitle,
                'benefits' => $benefits ?: null,
                'button_text' => $row->button_label ?? 'Отправить заявку',
                'image' => $row->image,
                'is_active' => (bool) ($row->status ?? true),
                'sort_order' => (int) ($row->order_col ?? 0),
                'settings' => json_encode([
                    'title_size' => 28,
                    'subtitle_size' => 16,
                    'benefits_size' => 14,
                    'list_marker' => $row->list_style ?? 'check',
                    'button_color' => $row->button_bg_color ?? '#22c55e',
                    'button_text_color' => $row->button_text_color ?? '#ffffff',
                    'border_radius' => 16,
                    'image_position' => $imagePosition,
                    'image_scale' => 100,
                    'image_x' => 'center',
                    'image_y' => 'center',
                    'name_placeholder' => 'Ваше имя',
                    'phone_placeholder' => '+7 (___) ___-__-__',
                    'consent_text' => 'Подтверждаю, что ознакомлен(а) с политикой конфиденциальности и положением об обработке персональных данных.',
                ], JSON_UNESCAPED_UNICODE),
                'display_rules' => json_encode([
                    'mode' => ($row->show_on_all_pages ?? true) ? 'all_pages' : 'selected_pages',
                    'page_ids' => json_decode($row->page_ids ?? '[]', true) ?: [],
                    'delay' => $delays[0] ?? 5,
                    'frequency' => 'visit',
                    'exit_intent' => (bool) ($row->show_on_exit ?? false),
                    'schedule' => ($row->show_on_exit ?? false) ? [] : ($delays ?: [5]),
                    'session_limit' => max(1, (int) ($row->session_show_limit ?? 1)),
                ], JSON_UNESCAPED_UNICODE),
                'user_id' => $row->user_id,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        });

        Schema::dropIfExists('callback_popups');
    }

    public function down(): void
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

        Schema::dropIfExists('popups');
    }
};
