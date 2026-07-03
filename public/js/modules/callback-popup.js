(function () {
    'use strict';

    var MODAL_ID = 'cbp-modal';
    var STORAGE_ID = 'cbp-modal-storage';
    var loadingPopups = {};

    function ensureModal() {
        if (document.getElementById(MODAL_ID)) {
            return;
        }

        var modal = document.createElement('div');
        modal.id = MODAL_ID;
        modal.className = 'cbp-modal';
        modal.setAttribute('aria-hidden', 'true');
        modal.innerHTML =
            '<div class="cbp-modal__backdrop" data-cbp-close></div>' +
            '<div class="cbp-modal__viewport">' +
                '<div class="cbp-modal__dialog" role="dialog" aria-modal="true" tabindex="-1">' +
                    '<button type="button" class="cbp-modal__close" data-cbp-close aria-label="Закрыть">' +
                        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>' +
                        '</svg>' +
                    '</button>' +
                    '<div class="cbp-modal__body"></div>' +
                '</div>' +
            '</div>';

        var storage = document.createElement('div');
        storage.id = STORAGE_ID;
        storage.hidden = true;
        storage.setAttribute('aria-hidden', 'true');

        document.body.appendChild(modal);
        document.body.appendChild(storage);

        modal.addEventListener('click', function (event) {
            if (event.target.closest('[data-cbp-close]')) {
                window.CbpModal.close();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && window.CbpModal.isOpen()) {
                window.CbpModal.close();
            }
        });
    }

    function renderUrl(popupId) {
        var baseUrl = (window.CbpConfig && window.CbpConfig.renderBaseUrl) || '/popups';

        return String(baseUrl).replace(/\/$/, '') + '/' + popupId;
    }

    var activeRoot = null;
    var onCloseCallback = null;
    var previousOverflow = '';

    function mountRoot(root, options) {
        var modal = document.getElementById(MODAL_ID);
        var body = modal.querySelector('.cbp-modal__body');
        var dialog = modal.querySelector('.cbp-modal__dialog');

        onCloseCallback = typeof options.onClose === 'function' ? options.onClose : null;
        activeRoot = root;

        root.classList.add('cbp-root--active');
        root.style.display = 'block';
        body.appendChild(root);

        modal.classList.add('cbp-modal--open');
        modal.setAttribute('aria-hidden', 'false');

        previousOverflow = document.body.style.overflow;
        document.body.classList.add('cbp-modal-open');
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(function () {
            dialog.focus();
        });

        return true;
    }

    window.CbpModal = {
        isOpen: function () {
            return !!activeRoot;
        },

        ensureRoot: function (popupId) {
            ensureModal();

            var existing = document.getElementById('cbp-' + popupId);

            if (existing) {
                return Promise.resolve(existing);
            }

            if (loadingPopups[popupId]) {
                return loadingPopups[popupId];
            }

            loadingPopups[popupId] = fetch(renderUrl(popupId), {
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'text/html',
                },
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Failed to load popup');
                    }

                    return response.text();
                })
                .then(function (html) {
                    var storage = document.getElementById(STORAGE_ID);

                    if (!storage) {
                        throw new Error('Popup storage is missing');
                    }

                    storage.insertAdjacentHTML('beforeend', html.trim());

                    return document.getElementById('cbp-' + popupId);
                })
                .finally(function () {
                    delete loadingPopups[popupId];
                });

            return loadingPopups[popupId];
        },

        open: function (popupId, options) {
            ensureModal();

            options = options || {};

            if (this.isOpen()) {
                return Promise.resolve(false);
            }

            return this.ensureRoot(popupId)
                .then(function (root) {
                    if (!root) {
                        return false;
                    }

                    return mountRoot(root, options);
                })
                .catch(function () {
                    return false;
                });
        },

        close: function () {
            if (!activeRoot) {
                return;
            }

            var modal = document.getElementById(MODAL_ID);
            var storage = document.getElementById(STORAGE_ID);
            var callback = onCloseCallback;

            activeRoot.classList.remove('cbp-root--active');
            activeRoot.style.display = 'none';
            storage.appendChild(activeRoot);

            modal.classList.remove('cbp-modal--open');
            modal.setAttribute('aria-hidden', 'true');

            document.body.classList.remove('cbp-modal-open');
            document.body.style.overflow = previousOverflow;

            activeRoot = null;
            onCloseCallback = null;

            if (callback) {
                callback();
            }
        },
    };
})();
