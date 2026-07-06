(function () {
    'use strict';

    function loadCss(href) {
        if (! href || document.querySelector('link[rel="stylesheet"][href="' + href + '"]')) {
            return;
        }

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
    }

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            if (! src) {
                resolve();

                return;
            }

            if (document.querySelector('script[src="' + src + '"]')) {
                resolve();

                return;
            }

            const script = document.createElement('script');
            script.src = src;
            script.defer = true;
            script.onload = function () {
                resolve();
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    function closeWidget(root) {
        const container = root.querySelector('.social-widget');
        const toggle = root.querySelector('[data-widget-toggle]');

        container?.classList.remove('is-open');
        toggle?.classList.remove('open');
    }

    function initWidget(root) {
        const container = root.querySelector('.social-widget');
        const toggle = root.querySelector('[data-widget-toggle]');

        toggle?.addEventListener('click', function () {
            container?.classList.toggle('is-open');
            toggle.classList.toggle('open');
        });

        toggle?.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                container?.classList.toggle('is-open');
                toggle.classList.toggle('open');
            }
        });

        root.querySelectorAll('[data-popup-id]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                const popupId = parseInt(button.getAttribute('data-popup-id') || '0', 10);

                if (! popupId || typeof window.CbpModal === 'undefined') {
                    return;
                }

                closeWidget(root);
                window.CbpModal.open(popupId);
            });
        });

        root.querySelectorAll('[data-widget-close]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeWidget(root);
            });
        });
    }

    async function bootstrap() {
        const currentScript = document.currentScript;
        const apiBase = (currentScript && currentScript.getAttribute('data-api')) || '/embed';
        const path = window.location.pathname;
        const configUrl = apiBase.replace(/\/$/, '') + '/config?path=' + encodeURIComponent(path);

        const response = await fetch(configUrl, {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (! response.ok) {
            return;
        }

        const config = await response.json();

        loadCss(config.assets?.popupCss);
        loadCss(config.assets?.widgetCss);

        await loadScript(config.assets?.modalJs);
        await loadScript(config.assets?.queueJs);

        window.CbpConfig = window.CbpConfig || {};
        window.CbpConfig.renderBaseUrl = config.popups?.renderBaseUrl || '/popups';
        window.CbpConfig.formAction = config.form?.action || '/call_me';
        window.CbpConfig.idleGate = config.popups?.idleGate || {};
        window.CbpConfig.scroll = config.popups?.scroll || {};

        if (config.popups?.queue?.length && window.CbpQueue) {
            window.CbpQueue.init(config.popups.queue, {
                idleGate: config.popups?.idleGate,
                scroll: config.popups?.scroll,
            });
        }

        if (! config.widget?.enabled || ! config.widget?.htmlUrl) {
            return;
        }

        const widgetResponse = await fetch(config.widget.htmlUrl, {
            credentials: 'same-origin',
            headers: {
                Accept: 'text/html',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (! widgetResponse.ok) {
            return;
        }

        const html = await widgetResponse.text();
        const mount = document.createElement('div');
        mount.id = 'contact-widget-embed';
        mount.innerHTML = html.trim();
        document.body.appendChild(mount);
        initWidget(mount);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            bootstrap().catch(function () {});
        });
    } else {
        bootstrap().catch(function () {});
    }
})();
