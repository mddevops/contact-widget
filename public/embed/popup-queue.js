(function () {
    'use strict';

    window.CbpIdleGate = {
        idleAfterBlockMs: 3000,
        checkIntervalMs: 400,
        busySelectors: [],

        configure: function (options) {
            options = options || {};

            if (typeof options.idleAfterBlockMs === 'number') {
                this.idleAfterBlockMs = Math.max(0, options.idleAfterBlockMs);
            }

            if (typeof options.checkIntervalMs === 'number') {
                this.checkIntervalMs = Math.max(100, options.checkIntervalMs);
            }

            if (Array.isArray(options.busySelectors)) {
                this.busySelectors = options.busySelectors.filter(function (selector) {
                    return typeof selector === 'string' && selector.trim() !== '';
                });
            }
        },

        isSiteBusy: function () {
            if (typeof window.CbpModal !== 'undefined' && window.CbpModal.isOpen()) {
                return true;
            }

            if (document.querySelector('.modal.show, .modal-backdrop.show')) {
                return true;
            }

            if (document.body.classList.contains('modal-open')) {
                return true;
            }

            if (document.querySelector('.fancybox-container.fancybox-is-open')) {
                return true;
            }

            if (document.body.classList.contains('fancybox-active')) {
                return true;
            }

            var modals = document.querySelectorAll('[aria-modal="true"]');

            for (var i = 0; i < modals.length; i++) {
                var modal = modals[i];

                if (modal.closest('#' + 'cbp-modal')) {
                    continue;
                }

                if (modal.getAttribute('aria-hidden') === 'true') {
                    continue;
                }

                if (modal.offsetParent !== null || modal.getClientRects().length > 0) {
                    return true;
                }
            }

            for (var j = 0; j < this.busySelectors.length; j++) {
                if (document.querySelector(this.busySelectors[j])) {
                    return true;
                }
            }

            return false;
        },

        whenIdle: function (options) {
            options = options || {};

            var minDelayMs = Math.max(0, options.minDelayMs || 0);
            var callback = options.callback;
            var cancelled = false;
            var timerId = null;
            var startedAt = Date.now();
            var lastBusyAt = this.isSiteBusy() ? Date.now() : null;

            if (typeof callback !== 'function') {
                return { cancel: function () {} };
            }

            var cleanup = function () {
                document.removeEventListener('hidden.bs.modal', tick);

                if (typeof jQuery !== 'undefined' && jQuery.fn) {
                    jQuery(document).off('afterClose.fb', tick);
                }

                if (timerId !== null) {
                    clearTimeout(timerId);
                    timerId = null;
                }
            };

            var tick = function () {
                if (cancelled) {
                    cleanup();

                    return;
                }

                if (window.CbpIdleGate.isSiteBusy()) {
                    lastBusyAt = Date.now();
                    timerId = setTimeout(tick, window.CbpIdleGate.checkIntervalMs);

                    return;
                }

                if ((Date.now() - startedAt) < minDelayMs) {
                    timerId = setTimeout(tick, window.CbpIdleGate.checkIntervalMs);

                    return;
                }

                if (lastBusyAt !== null && (Date.now() - lastBusyAt) < window.CbpIdleGate.idleAfterBlockMs) {
                    timerId = setTimeout(tick, window.CbpIdleGate.checkIntervalMs);

                    return;
                }

                cleanup();
                callback();
            };

            document.addEventListener('hidden.bs.modal', tick);

            if (typeof jQuery !== 'undefined' && jQuery.fn) {
                jQuery(document).on('afterClose.fb', tick);
            }

            timerId = setTimeout(tick, this.checkIntervalMs);

            return {
                cancel: function () {
                    cancelled = true;
                    cleanup();
                },
            };
        },
    };

    function ensureSession() {
        const sessionKey = 'cbp-session-id';

        if (! sessionStorage.getItem(sessionKey)) {
            sessionStorage.setItem(sessionKey, String(Date.now()));
        }
    }

    function sessionCountKey(id) {
        return 'cbp-session-count-' + id;
    }

    function frequencyKey(id, frequency) {
        return 'cbp-freq-' + frequency + '-' + id;
    }

    function getSessionCount(id) {
        return parseInt(sessionStorage.getItem(sessionCountKey(id)) || '0', 10);
    }

    function incrementSessionCount(id) {
        sessionStorage.setItem(sessionCountKey(id), String(getSessionCount(id) + 1));
    }

    function sessionLimitAllowsShow(popup) {
        return getSessionCount(popup.id) < (popup.sessionLimit || 1);
    }

    function frequencyAllowsShow(popup) {
        const frequency = popup.frequency || 'visit';
        const id = popup.id;

        if (! sessionLimitAllowsShow(popup)) {
            return false;
        }

        if (frequency === 'once') {
            return ! localStorage.getItem(frequencyKey(id, 'once'));
        }

        if (frequency === 'daily') {
            const lastShown = localStorage.getItem(frequencyKey(id, 'daily'));

            if (! lastShown) {
                return true;
            }

            return new Date(lastShown).toDateString() !== new Date().toDateString();
        }

        if (frequency === 'weekly') {
            const lastShown = localStorage.getItem(frequencyKey(id, 'weekly'));

            if (! lastShown) {
                return true;
            }

            return (Date.now() - parseInt(lastShown, 10)) >= (7 * 24 * 60 * 60 * 1000);
        }

        return true;
    }

    function timeToMinutes(value) {
        const parts = String(value || '00:00').split(':');

        return (parseInt(parts[0], 10) || 0) * 60 + (parseInt(parts[1], 10) || 0);
    }

    function scheduleAllowsShow(popup) {
        const schedule = popup.schedule || {};

        if (! schedule.enabled) {
            return true;
        }

        const now = new Date();
        const day = now.getDay() === 0 ? 7 : now.getDay();
        const days = schedule.days || [];

        if (days.length > 0 && ! days.includes(day)) {
            return false;
        }

        const current = now.getHours() * 60 + now.getMinutes();
        const fromMinutes = timeToMinutes(schedule.from);
        const toMinutes = timeToMinutes(schedule.to);

        if (fromMinutes <= toMinutes) {
            return current >= fromMinutes && current <= toMinutes;
        }

        return current >= fromMinutes || current <= toMinutes;
    }

    function markFrequencyShown(popup) {
        const id = popup.id;
        const frequency = popup.frequency || 'visit';

        incrementSessionCount(id);

        if (frequency === 'once') {
            localStorage.setItem(frequencyKey(id, 'once'), '1');

            return;
        }

        if (frequency === 'daily') {
            localStorage.setItem(frequencyKey(id, 'daily'), new Date().toISOString());

            return;
        }

        if (frequency === 'weekly') {
            localStorage.setItem(frequencyKey(id, 'weekly'), String(Date.now()));
        }
    }

    function canShow(popup) {
        return frequencyAllowsShow(popup) && scheduleAllowsShow(popup);
    }

    function getScrollPercent() {
        const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrollTop = window.scrollY || document.documentElement.scrollTop;

        if (docHeight <= 0) {
            return 100;
        }

        return (scrollTop / docHeight) * 100;
    }

    function getScrollTop() {
        return window.scrollY || document.documentElement.scrollTop || 0;
    }

    function createScrollEngagementGate(config) {
        config = config || {};

        var settleMs = Math.max(0, config.settleMs || 400);
        var minTimeOnPageMs = Math.max(0, config.minTimeOnPageMs || 0);
        var userEngaged = false;
        var trackingReady = false;
        var readyAt = 0;
        var baselineScrollTop = 0;

        function markUserEngaged() {
            if (! trackingReady) {
                return;
            }

            userEngaged = true;
        }

        function onScroll() {
            if (! trackingReady) {
                return;
            }

            if (Math.abs(getScrollTop() - baselineScrollTop) > 8) {
                userEngaged = true;
            }
        }

        function isEngaged() {
            if (! trackingReady || ! userEngaged) {
                return false;
            }

            if (minTimeOnPageMs > 0 && (Date.now() - readyAt) < minTimeOnPageMs) {
                return false;
            }

            return true;
        }

        function whenReady(callback) {
            var startTracking = function () {
                baselineScrollTop = getScrollTop();
                readyAt = Date.now();
                trackingReady = true;
                callback();
            };

            var delayStart = function () {
                setTimeout(startTracking, settleMs);
            };

            if (document.readyState === 'complete') {
                delayStart();
            } else {
                window.addEventListener('load', delayStart, { once: true });
            }
        }

        window.addEventListener('wheel', markUserEngaged, { passive: true });
        window.addEventListener('touchmove', markUserEngaged, { passive: true });
        window.addEventListener('keydown', markUserEngaged, { passive: true });
        window.addEventListener('scroll', onScroll, { passive: true });

        return {
            isEngaged: isEngaged,
            whenReady: whenReady,
        };
    }

    window.CbpQueue = {
        init: function (popups, options) {
            if (! Array.isArray(popups) || popups.length === 0 || typeof window.CbpModal === 'undefined') {
                return;
            }

            window.CbpIdleGate.configure(
                (options && options.idleGate)
                || (window.CbpConfig && window.CbpConfig.idleGate)
                || {},
            );

            ensureSession();

            const isOpening = {};

            const openPopup = function (popup, done) {
                if (
                    ! canShow(popup)
                    || isOpening[popup.id]
                    || window.CbpModal.isOpen()
                    || window.CbpIdleGate.isSiteBusy()
                ) {
                    done?.();

                    return;
                }

                isOpening[popup.id] = true;

                window.CbpModal.open(popup.id, {
                    onClose: function () {
                        markFrequencyShown(popup);
                        isOpening[popup.id] = false;
                        done?.();
                    },
                }).then(function (opened) {
                    if (! opened) {
                        isOpening[popup.id] = false;
                        done?.();
                    }
                });
            };

            const schedulePopup = function (popup, minDelayMs, done) {
                window.CbpIdleGate.whenIdle({
                    minDelayMs: minDelayMs,
                    callback: function () {
                        if (! canShow(popup)) {
                            done?.();

                            return;
                        }

                        openPopup(popup, done);
                    },
                });
            };

            const sortedPopups = popups.slice().sort(function (a, b) {
                return (a.sortOrder || 0) - (b.sortOrder || 0);
            });

            const exitPopups = sortedPopups.filter(function (popup) {
                return popup.mode === 'exit_intent';
            });

            const timerPopups = sortedPopups.filter(function (popup) {
                return popup.mode !== 'exit_intent';
            });

            const delayPopups = timerPopups.filter(function (popup) {
                return (popup.trigger || 'delay') !== 'scroll';
            });

            const scrollPopups = timerPopups.filter(function (popup) {
                return (popup.trigger || 'delay') === 'scroll';
            });

            const scrollGate = scrollPopups.length > 0
                ? createScrollEngagementGate(
                    (options && options.scroll)
                    || (window.CbpConfig && window.CbpConfig.scroll)
                    || {},
                )
                : null;

            const processDelayQueue = function (index) {
                if (index >= delayPopups.length) {
                    return;
                }

                const popup = delayPopups[index];

                schedulePopup(popup, (popup.delay || 0) * 1000, function () {
                    processDelayQueue(index + 1);
                });
            };

            const processScrollQueue = function (index) {
                if (index >= scrollPopups.length) {
                    return;
                }

                const popup = scrollPopups[index];
                let triggered = false;
                let idleHandle = null;

                const tryOpen = function () {
                    if (triggered) {
                        return true;
                    }

                    if (! scrollGate || ! scrollGate.isEngaged()) {
                        return false;
                    }

                    if (getScrollPercent() < (popup.scrollPercent || 50)) {
                        return false;
                    }

                    if (! canShow(popup)) {
                        return false;
                    }

                    if (idleHandle) {
                        return false;
                    }

                    idleHandle = window.CbpIdleGate.whenIdle({
                        minDelayMs: 0,
                        callback: function () {
                            idleHandle = null;

                            if (triggered || ! canShow(popup) || window.CbpModal.isOpen()) {
                                return;
                            }

                            triggered = true;
                            window.removeEventListener('scroll', onScroll);
                            openPopup(popup, function () {
                                processScrollQueue(index + 1);
                            });
                        },
                    });

                    return false;
                };

                const onScroll = function () {
                    tryOpen();
                };

                window.addEventListener('scroll', onScroll, { passive: true });
            };

            processDelayQueue(0);

            if (scrollGate) {
                scrollGate.whenReady(function () {
                    processScrollQueue(0);
                });
            }

            exitPopups.forEach(function (popup) {
                let exitTriggered = false;
                let exitIdleHandle = null;

                document.addEventListener('mouseout', function (event) {
                    if (exitTriggered || exitIdleHandle || ! canShow(popup)) {
                        return;
                    }

                    if (event.clientY > 0 || (event.relatedTarget !== null && event.toElement !== null)) {
                        return;
                    }

                    exitIdleHandle = window.CbpIdleGate.whenIdle({
                        minDelayMs: 0,
                        callback: function () {
                            exitIdleHandle = null;

                            if (exitTriggered || ! canShow(popup)) {
                                return;
                            }

                            exitTriggered = true;
                            openPopup(popup);
                        },
                    });
                });
            });
        },
    };
})();
