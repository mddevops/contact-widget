# Contact Widget (Laravel Package)

Виджет связи, попапы и иконки для Laravel + Filament 3.

## Установка на новый проект

### 1. Composer

**Из Git (рекомендуется для других проектов):**

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/YOUR_USER/contact-widget.git"
    }
],
"require": {
    "site-apps/contact-widget": "dev-main"
},
"minimum-stability": "dev",
"prefer-stable": true
```

```bash
composer update site-apps/contact-widget
```

**Локально (разработка):**

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/contact-widget"
    }
],
"require": {
    "site-apps/contact-widget": "@dev"
}
```

### 2. Установка ассетов и конфига

```bash
php artisan contact-widget:install
php artisan migrate
php artisan db:seed --class="SiteApps\ContactWidget\Database\Seeders\SocialIconSeeder"
```

Иконки не создаются автоматически — сидер нужно запустить отдельно (безопасен для повторного запуска).

### 3. Filament

В `AdminPanelProvider`:

```php
use SiteApps\ContactWidget\Filament\ContactWidgetPlugin;

->plugins([
    ContactWidgetPlugin::make(),
    // ...
])
```

### 4. Фронт (одна строка в layout)

```blade
@include('contact-widget::embed-script')
```

Или вручную:

```html
<script src="/embed/contact-widget.js" data-api="/embed" defer></script>
```

### 5. Формы попапов

Попап подгружается через AJAX **после** `DOMContentLoaded`, поэтому обычный `mdform.js` (с `querySelectorAll` на старте) на форму попапа не срабатывает.

Пакет сам:
- вешает маску `#phone` (IMask) при открытии попапа;
- отправляет `form#callback` внутри `.cbp-root` на `/call_me` (или `form.action` из конфига).

**Интеграция с вашим `mdform.js` (mgo, ym, toastr):**

```html
<script>
window.CbpFormHooks = {
    onSuccess: function (form, formData) {
        mgo.postForm({
            name: formData.name,
            number: formData.telephone,
            customParam: formData.form_name,
            comment: formData.comment,
        });
        ym(107721773, 'reachGoal', 'send_form');
        var url = window.location.href;
        window.location.href = url + (url.includes('?') ? '&' : '?') + 'success=form';
    },
};
</script>
```

Либо вынесите логику из `mdform.js` в `window.bindCallbackForm = function (form) { ... }` — пакет вызовет её при открытии попапа.

В `config/contact-widget.php`:

```php
'form' => [
    'action' => '/call_me',
],
```

На странице нужны `<meta name="csrf-token">`, IMask и (опционально) toastr.

## Конфигурация

| Ключ | Описание |
|------|----------|
| `user_model` | Модель пользователя для `popups.user_id` |
| `legal.privacy_url` | Ссылка в чекбоксе согласия |
| `legal.terms_url` | Ссылка на положение об обработке ПДн |
| `form.action` | URL отправки формы попапа |
| `routes.prefix` | Префикс API embed (по умолчанию `embed`) |

## API

| Маршрут | Описание |
|---------|----------|
| `GET /embed/config?path=/contact` | JSON: ассеты, очередь попапов, виджет |
| `GET /embed/widget` | HTML виджета |
| `GET /popups/{id}` | HTML попапа (lazy-load) |

## Условия показа попапов

- Типы: все страницы, выбранные URL, exit intent, только по кнопке
- URL вводятся вручную: `contact`, `catalog/kia` (без домена)
- Триггеры: задержка, скролл, расписание, лимит сессии

## Права (Filament Shield)

По умолчанию доступ к попапам открыт всем пользователям панели. Если используете Shield, в `.env`:

```
CONTACT_WIDGET_AUTHORIZE_WITH_SHIELD=true
```

и сгенерируйте права для ресурсов `Popup`, `SocialWidget`, `SocialIcon`.
