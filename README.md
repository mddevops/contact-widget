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

### 5. Формы попапов (mdform.js без изменений)

Попапы **рендерятся в HTML** через `@include('contact-widget::embed-script')` ещё до загрузки `mdform.js`.  
Ваш `mdform.js` на `DOMContentLoaded` находит `form#callback` и `#phone` как обычные формы на странице — счётчики, toastr, mgo, ym остаются вашими.

**Порядок в layout важен:**

```blade
@include('contact-widget::embed-script')   {{-- 1. попапы в #cbp-modal-storage --}}
<script src="/views/js/mdform.js"></script> {{-- 2. маска + submit --}}
```

`embed-script` выводит скрытый `#cbp-modal-storage` с попапами для текущей страницы и кнопок виджета, затем `contact-widget.js`.

Endpoint формы задаётся в `config/contact-widget.php` (`form.action`, по умолчанию `/call_me`) — используется вашим `mdform.js`.

На странице нужны `<meta name="csrf-token">`, IMask, toastr (как у вас сейчас).

## Конфигурация

| Ключ | Описание |
|------|----------|
| `user_model` | Модель пользователя для `popups.user_id` |
| `legal.privacy_url` | Ссылка в чекбоксе согласия |
| `legal.terms_url` | Ссылка на положение об обработке ПДн |
| `form.action` | URL отправки формы попапа |
| `routes.prefix` | Префикс API embed (по умолчанию `embed`) |
| `popup.idle_after_block_ms` | Пауза после закрытия чужого модала перед автопоказом (мс) |
| `popup.busy_check_interval_ms` | Интервал проверки «сайт занят» (мс) |
| `popup.busy_selectors` | Доп. CSS-селекторы открытых оверлеев на конкретном сайте |
| `popup.scroll_settle_ms` | Пауза после `load` перед учётом якорной прокрутки (мс) |
| `popup.scroll_min_time_on_page_ms` | Мин. время на странице до scroll-триггера (0 = выкл.) |

## Автопоказ и чужие модалки

Автопопапы (delay / scroll / exit intent) **не показываются**, пока на странице открыт другой оверлей: Bootstrap modal, Fancybox, или ваш `CbpModal`. После закрытия чужого модала срабатывает пауза (`idle_after_block_ms`, по умолчанию 3 сек), затем условие проверяется снова. Лимиты frequency/session при отложенном показе **не тратятся**.

**Scroll-триггер** срабатывает только после **осознанного скролла** пользователя (колёсико, тач, клавиши, перетаскивание скроллбара). Стартовая позиция по якорю `#section` не считается. После `load` выдерживается `scroll_settle_ms` (400 мс), чтобы якорь успел отработать.

Клик по кнопке виджета (`data-popup-id`) по-прежнему открывает попап сразу — это явное действие пользователя.

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
