(function () {
    'use strict';

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

    window.CbpQueue = {
        init: function (popups) {
            if (! Array.isArray(popups) || popups.length === 0 || typeof window.CbpModal === 'undefined') {
                return;
            }

            ensureSession();

            const isOpening = {};

            const openPopup = function (popup, done) {
                if (! canShow(popup) || isOpening[popup.id] || window.CbpModal.isOpen()) {
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

            const processDelayQueue = function (index) {
                if (index >= delayPopups.length) {
                    return;
                }

                const popup = delayPopups[index];

                setTimeout(function () {
                    if (! canShow(popup)) {
                        processDelayQueue(index + 1);

                        return;
                    }

                    openPopup(popup, function () {
                        processDelayQueue(index + 1);
                    });
                }, (popup.delay || 0) * 1000);
            };

            const processScrollQueue = function (index) {
                if (index >= scrollPopups.length) {
                    return;
                }

                const popup = scrollPopups[index];
                let triggered = false;

                const tryOpen = function () {
                    if (triggered) {
                        return true;
                    }

                    if (getScrollPercent() < (popup.scrollPercent || 50)) {
                        return false;
                    }

                    if (! canShow(popup) || window.CbpModal.isOpen()) {
                        return false;
                    }

                    triggered = true;
                    window.removeEventListener('scroll', onScroll);
                    openPopup(popup, function () {
                        processScrollQueue(index + 1);
                    });

                    return true;
                };

                const onScroll = function () {
                    tryOpen();
                };

                window.addEventListener('scroll', onScroll, { passive: true });
                tryOpen();
            };

            processDelayQueue(0);
            processScrollQueue(0);

            exitPopups.forEach(function (popup) {
                let exitTriggered = false;

                document.addEventListener('mouseout', function (event) {
                    if (exitTriggered || ! canShow(popup)) {
                        return;
                    }

                    if (event.clientY <= 0 && (event.relatedTarget === null || event.toElement === null)) {
                        exitTriggered = true;
                        openPopup(popup);
                    }
                });
            });
        },
    };
})();
