<?php

namespace SiteApps\ContactWidget\Support\Popup;

use SiteApps\ContactWidget\Enums\PopupImagePosition;

class PopupSettings

{
    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return array_merge([
            'title_size' => 32,
            'subtitle_size' => 16,
            'benefits_size' => 14,
            'list_marker' => 'check',
            'list_marker_color' => '#22c55e',
            'button_color' => '#22c55e',
            'button_text_color' => '#ffffff',
            'button_icon_id' => null,
            'button_icon_size' => 18,
            'button_icon_color' => '#ffffff',
            'border_radius' => 16,
            'content_padding' => 20,
            'content_background' => '#ffffff',
            'mobile_title_size' => 32,
            'mobile_subtitle_size' => 16,
            'mobile_benefits_size' => 14,
            'mobile_content_padding' => 20,
            'image_position' => 'left',
            'content_width' => 480,
            'image_width' => 480,
            'image_scale' => 100,
            'image_x' => 50,
            'image_y' => 50,
            'desktop_hide_image' => false,
            'mobile_hide_image' => false,
            'mobile_content_width' => 360,
            'mobile_image_position' => 'top',
            'mobile_image_height_px' => 300,
            'mobile_image_scale' => 100,
            'mobile_image_x' => 50,
            'mobile_image_y' => 50,
        ], self::formDefaults());
    }

    /**
     * @return array<string, string>
     */
    public static function formDefaults(): array
    {
        return [
            'name_placeholder' => 'Имя',
            'phone_placeholder' => '+7 (___) ___-__-__',
            'consent_text' => 'Подтверждаю что ознакомлен(а) с политикой конфиденциальности и положением об обработке персональных данных и даю согласие на обработку моих персональных данных',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    public static function merge(?array $settings): array
    {
        $merged = array_replace(self::defaults(), $settings ?? [], self::formDefaults());
        $merged['content_width'] = self::contentWidth($merged, false);
        $merged['mobile_content_width'] = self::contentWidth($merged, true);
        $merged['image_width'] = self::imageWidthPx($merged);
        $merged['mobile_image_width_px'] = self::contentWidth($merged, true);
        $merged['image_scale'] = self::imageScale($merged);
        $merged['desktop_hide_image'] = self::toBool($merged['desktop_hide_image'] ?? false);
        $merged['mobile_hide_image'] = self::toBool($merged['mobile_hide_image'] ?? false);
        $merged['mobile_image_height_px'] = self::mobileImageHeightPx($merged);
        $merged['mobile_image_scale'] = self::mobileImageScale($merged);
        $merged['content_padding'] = self::contentPadding($merged, false);
        $merged['mobile_content_padding'] = self::contentPadding($merged, true);
        $merged['title_size'] = self::titleSize($merged, false);
        $merged['subtitle_size'] = self::subtitleSize($merged, false);
        $merged['benefits_size'] = self::benefitsSize($merged, false);
        $merged['mobile_title_size'] = self::titleSize($merged, true);
        $merged['mobile_subtitle_size'] = self::subtitleSize($merged, true);
        $merged['mobile_benefits_size'] = self::benefitsSize($merged, true);
        $merged['content_background'] = self::contentBackground($merged);
        $merged['list_marker_color'] = self::listMarkerColor($merged);
        $merged['button_icon_id'] = filled($merged['button_icon_id'] ?? null) ? (int) $merged['button_icon_id'] : null;
        $merged['button_icon_size'] = max(12, min(48, (int) ($merged['button_icon_size'] ?? 18)));
        $merged['button_icon_color'] = self::buttonIconColor($merged);
        $merged['image_position'] = self::imagePosition($merged)->value;
        $merged['image_x'] = self::imageAxisPercent($merged, 'image_x');
        $merged['image_y'] = self::imageAxisPercent($merged, 'image_y');
        $merged['mobile_image_x'] = self::imageAxisPercent($merged, 'mobile_image_x');
        $merged['mobile_image_y'] = self::imageAxisPercent($merged, 'mobile_image_y');

        return $merged;
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function contentPadding(?array $settings, bool $mobile = false): int
    {
        $data = $settings ?? [];
        $key = $mobile ? 'mobile_content_padding' : 'content_padding';
        $fallback = (int) ($data['content_padding'] ?? 20);
        $value = (int) ($data[$key] ?? ($mobile ? $fallback : 20));

        return max(0, min(80, $value));
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function contentBackground(?array $settings): string
    {
        $color = ($settings ?? [])['content_background'] ?? '#ffffff';

        return is_string($color) && $color !== '' ? $color : '#ffffff';
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function buttonIconColor(?array $settings): string
    {
        $color = ($settings ?? [])['button_icon_color'] ?? '#ffffff';

        return is_string($color) && $color !== '' ? $color : '#ffffff';
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function listMarkerColor(?array $settings): string
    {
        $color = ($settings ?? [])['list_marker_color'] ?? '#22c55e';

        return is_string($color) && $color !== '' ? $color : '#22c55e';
    }

    public static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return $value !== 0;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));

            if (in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
                return true;
            }

            if (in_array($normalized, ['0', 'false', 'no', 'off', ''], true)) {
                return false;
            }
        }

        return (bool) $value;
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function titleSize(?array $settings, bool $mobile = false): int
    {
        return self::boundedSize(
            $settings,
            $mobile ? 'mobile_title_size' : 'title_size',
            $mobile ? 'title_size' : null,
            18,
            72,
            32,
        );
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function subtitleSize(?array $settings, bool $mobile = false): int
    {
        return self::boundedSize(
            $settings,
            $mobile ? 'mobile_subtitle_size' : 'subtitle_size',
            $mobile ? 'subtitle_size' : null,
            8,
            24,
            16,
        );
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function benefitsSize(?array $settings, bool $mobile = false): int
    {
        return self::boundedSize(
            $settings,
            $mobile ? 'mobile_benefits_size' : 'benefits_size',
            $mobile ? 'benefits_size' : null,
            8,
            24,
            14,
        );
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, string>
     */
    public static function cssVariables(?array $settings, bool $hasImage = true): array
    {
        $settings = self::merge($settings);

        return [
            '--cbp-content-bg' => self::contentBackground($settings),
            '--cbp-content-padding-desktop' => self::contentPadding($settings, false).'px',
            '--cbp-content-padding-mobile' => self::contentPadding($settings, true).'px',
            '--cbp-title-size-desktop' => self::titleSize($settings, false).'px',
            '--cbp-title-size-mobile' => self::titleSize($settings, true).'px',
            '--cbp-subtitle-size-desktop' => self::subtitleSize($settings, false).'px',
            '--cbp-subtitle-size-mobile' => self::subtitleSize($settings, true).'px',
            '--cbp-benefits-size-desktop' => self::benefitsSize($settings, false).'px',
            '--cbp-benefits-size-mobile' => self::benefitsSize($settings, true).'px',
            '--cbp-content-width-desktop' => self::contentWidth($settings, false).'px',
            '--cbp-image-width-desktop' => self::imageWidthPx($settings).'px',
            '--cbp-content-width-mobile' => self::contentWidth($settings, true).'px',
            '--cbp-image-width-mobile' => self::contentWidth($settings, true).'px',
            '--cbp-modal-width-desktop' => self::modalWidthPx($settings, true, $hasImage).'px',
            '--cbp-modal-width-mobile' => self::modalWidthPx($settings, false, $hasImage).'px',
            '--cbp-list-marker-color' => self::listMarkerColor($settings),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    protected static function boundedSize(
        ?array $settings,
        string $key,
        ?string $fallbackKey,
        int $min,
        int $max,
        int $default,
    ): int {
        $data = $settings ?? [];
        $value = (int) ($data[$key] ?? ($fallbackKey ? ($data[$fallbackKey] ?? $default) : $default));

        return max($min, min($max, $value));
    }

    /**
     * User-facing axis: 0–100. Horizontal: 0 = left, 100 = right. Vertical: 0 = bottom, 100 = top.
     *
     * @param  array<string, mixed>|null  $settings
     */
    public static function imageAxisPercent(?array $settings, string $key, int $default = 50): int
    {
        $value = ($settings ?? [])[$key] ?? $default;

        if (is_numeric($value)) {
            return max(0, min(100, (int) $value));
        }

        $normalized = strtolower(trim((string) $value));

        if (str_contains($key, '_x')) {
            return match ($normalized) {
                'left' => 0,
                'right' => 100,
                default => 50,
            };
        }

        return match ($normalized) {
            'bottom' => 0,
            'top' => 100,
            default => 50,
        };
    }

    /**
     * CSS background-position from user axis percents.
     *
     * @param  array<string, mixed>|null  $settings
     */
    public static function backgroundPositionCss(?array $settings, bool $mobile = false): string
    {
        $prefix = $mobile ? 'mobile_' : '';
        $x = self::imageAxisPercent($settings, $prefix.'image_x');
        $y = self::imageAxisPercent($settings, $prefix.'image_y');

        return $x.'% '.(100 - $y).'%';
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function imagePosition(?array $settings): PopupImagePosition
    {
        $value = ($settings ?? [])['image_position'] ?? 'left';

        if ($value instanceof PopupImagePosition) {
            return $value;
        }

        $normalized = match ((string) $value) {
            'row-reverse' => PopupImagePosition::Left,
            'row' => PopupImagePosition::Right,
            'right' => PopupImagePosition::Right,
            'left' => PopupImagePosition::Left,
            default => PopupImagePosition::Left,
        };

        return $normalized;
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function mobileImageHeightPx(?array $settings): int
    {
        $data = $settings ?? [];
        $height = (int) ($data['mobile_image_height_px'] ?? $data['mobile_image_height'] ?? 300);

        return max(50, min(500, $height));
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function contentWidth(?array $settings, bool $mobile = false): int
    {
        $data = $settings ?? [];

        if ($mobile) {
            $value = (int) ($data['mobile_content_width'] ?? 360);

            if ($value <= 100) {
                $value = (int) round(360 * ($value / 100));
            }

            return max(260, min(480, $value));
        }

        $value = (int) ($data['content_width'] ?? 480);

        if ($value <= 90) {
            $value = (int) round(960 * ($value / 100));
        }

        return max(280, min(720, $value));
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function imageWidthPx(?array $settings): int
    {
        $data = $settings ?? [];
        $value = (int) ($data['image_width'] ?? 480);

        if ($value <= 80) {
            $value = (int) round(960 * ($value / 100));
        }

        return max(200, min(720, $value));
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function mobileImageWidthPx(?array $settings): int
    {
        return self::contentWidth($settings, true);
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function modalWidthPx(?array $settings, bool $desktop = true, bool $hasImage = true): int
    {
        $settings = self::merge($settings);

        if ($desktop) {
            $width = self::contentWidth($settings, false);

            if ($hasImage && ! self::toBool($settings['desktop_hide_image'] ?? false)) {
                $width += self::imageWidthPx($settings);
            }

            return $width;
        }

        return self::contentWidth($settings, true);
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @deprecated Use imageWidthPx()
     */
    public static function imageWidth(?array $settings): int
    {
        return self::imageWidthPx($settings);
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function imageScale(?array $settings): int
    {
        $scale = (int) (($settings ?? [])['image_scale'] ?? 100);

        return max(50, min(500, $scale));
    }

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public static function mobileImageScale(?array $settings): int
    {
        $scale = (int) (($settings ?? [])['mobile_image_scale'] ?? 100);

        return max(50, min(500, $scale));
    }

    /**
     * @return list<string>
     */
    public static function benefitLines(?string $benefits): array
    {
        if (! filled($benefits)) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $benefits))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
