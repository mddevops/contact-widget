@php
    use SiteApps\ContactWidget\Services\PopupService;

    $preloadPopups = PopupService::embedPreload();
@endphp

<div id="cbp-modal-storage" hidden aria-hidden="true">
    @foreach ($preloadPopups as $popup)
        @include('contact-widget::popup-root', ['popup' => $popup])
    @endforeach
</div>

<script src="{{ asset(config('contact-widget.embed.script', 'embed/contact-widget.js')) }}" data-api="{{ url(config('contact-widget.routes.prefix', 'embed')) }}" defer></script>
