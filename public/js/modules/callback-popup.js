(function () {
    'use strict';

    var MODAL_ID = 'cbp-modal';
    var STORAGE_ID = 'cbp-modal-storage';
    var loadingPopups = {};
    var lastSubmitTime = 0;
    var submitCooldownMs = 1000;
    var formSubmitInstalled = false;

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

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');

        return meta ? meta.getAttribute('content') : '';
    }

    function toast(type, message) {
        if (typeof window.toastr !== 'undefined' && typeof window.toastr[type] === 'function') {
            window.toastr[type](message);

            return;
        }

        if (type === 'error') {
            console.error(message);
        }
    }

    function initPhoneMask(root) {
        if (typeof window.IMask === 'undefined') {
            return;
        }

        root.querySelectorAll('#phone, input[name="telephone"]').forEach(function (input) {
            if (input.dataset.imaskBound === '1') {
                return;
            }

            new window.IMask(input, {
                mask: '+{7} (000) 000-00-00',
            });

            input.dataset.imaskBound = '1';
        });
    }

    function initPopupForm(root) {
        if (! root) {
            return;
        }

        initPhoneMask(root);

        var form = root.querySelector('form#callback');

        if (! form) {
            return;
        }

        if (typeof window.bindCallbackForm === 'function') {
            window.bindCallbackForm(form);

            return;
        }

        window.dispatchEvent(new CustomEvent('contact-widget:form-ready', {
            bubbles: true,
            detail: {
                form: form,
                root: root,
            },
        }));
    }

    function collectFormData(form) {
        var telephoneInput = form.querySelector("input[name='telephone']");
        var checkbox = form.querySelector("input[type='checkbox']");
        var nameInput = form.querySelector("input[name='name']");
        var formData = {
            _token: getCsrfToken(),
            telephone: telephoneInput ? telephoneInput.value.trim() : '',
            name: nameInput ? nameInput.value.trim() : '',
            url: window.location.href,
        };

        form.querySelectorAll("input[type='hidden']").forEach(function (hiddenInput) {
            if (hiddenInput.name) {
                formData[hiddenInput.name] = hiddenInput.value;
            }
        });

        var hooks = window.CbpFormHooks || {};

        if (typeof hooks.beforeSubmit === 'function') {
            formData = hooks.beforeSubmit(formData, form) || formData;
        }

        return {
            formData: formData,
            telephoneInput: telephoneInput,
            checkbox: checkbox,
        };
    }

    function validateForm(form, telephoneInput, checkbox) {
        var telephone = telephoneInput ? telephoneInput.value.trim() : '';
        var isChecked = checkbox ? checkbox.checked : true;

        if (! isChecked) {
            toast('warning', 'Подтвердите согласие на обработку персональных данных.');

            return false;
        }

        if (telephone === '' || telephone.length < 18) {
            if (telephoneInput) {
                telephoneInput.focus();
            }

            toast('warning', 'Пожалуйста, введите свой номер правильно');

            return false;
        }

        return true;
    }

    function handlePopupFormSubmit(form) {
        var now = Date.now();

        if (now - lastSubmitTime < submitCooldownMs) {
            toast('warning', 'Пожалуйста, подождите перед повторной отправкой.');

            return;
        }

        var collected = collectFormData(form);

        if (! validateForm(form, collected.telephoneInput, collected.checkbox)) {
            return;
        }

        lastSubmitTime = now;

        var submitButton = form.querySelector("button[type='submit']");
        var originalButtonText = submitButton ? submitButton.innerHTML : '';
        var action = (window.CbpConfig && window.CbpConfig.formAction) || '/call_me';
        var hooks = window.CbpFormHooks || {};

        if (submitButton) {
            submitButton.innerHTML = 'Отправка...';
            submitButton.disabled = true;
        }

        fetch(action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': collected.formData._token,
            },
            body: JSON.stringify(collected.formData),
        })
            .then(function (response) {
                if (! response.ok) {
                    throw response;
                }

                return response.json().catch(function () {
                    return {};
                });
            })
            .then(function (responseData) {
                if (typeof hooks.onSuccess === 'function') {
                    hooks.onSuccess(form, collected.formData, responseData);

                    return;
                }

                if (typeof window.CbpModal !== 'undefined') {
                    window.CbpModal.close();
                }
            })
            .catch(function (error) {
                if (typeof hooks.onError === 'function') {
                    hooks.onError(form, error);

                    return;
                }

                if (error && error.status === 429) {
                    toast('error', 'Превышен лимит запросов. Пожалуйста, повторите попытку завтра.');

                    return;
                }

                toast('error', 'Возникла ошибка. Попробуйте отправить запрос позже.');
            })
            .finally(function () {
                if (! submitButton) {
                    return;
                }

                setTimeout(function () {
                    submitButton.innerHTML = originalButtonText;
                    submitButton.disabled = false;
                }, submitCooldownMs);
            });
    }

    function installFormSubmitHandler() {
        if (formSubmitInstalled) {
            return;
        }

        formSubmitInstalled = true;

        document.addEventListener('submit', function (event) {
            var form = event.target;

            if (! form || form.id !== 'callback' || ! form.closest('.cbp-root')) {
                return;
            }

            if (form.dataset.cbpExternalBound === '1') {
                return;
            }

            event.preventDefault();
            handlePopupFormSubmit(form);
        }, true);
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

        initPopupForm(root);

        requestAnimationFrame(function () {
            dialog.focus();
        });

        return true;
    }

    installFormSubmitHandler();

    window.CbpModal = {
        isOpen: function () {
            return !! activeRoot;
        },

        initPopupForm: initPopupForm,

        ensureRoot: function (popupId) {
            ensureModal();

            var existing = document.getElementById('cbp-' + popupId);

            if (existing) {
                initPopupForm(existing);

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
                    if (! response.ok) {
                        throw new Error('Failed to load popup');
                    }

                    return response.text();
                })
                .then(function (html) {
                    var storage = document.getElementById(STORAGE_ID);

                    if (! storage) {
                        throw new Error('Popup storage is missing');
                    }

                    storage.insertAdjacentHTML('beforeend', html.trim());

                    var root = document.getElementById('cbp-' + popupId);

                    initPopupForm(root);

                    return root;
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
                    if (! root) {
                        return false;
                    }

                    return mountRoot(root, options);
                })
                .catch(function () {
                    return false;
                });
        },

        close: function () {
            if (! activeRoot) {
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
