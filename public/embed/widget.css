/* Social floating widget — based on resources/views/social/style.css */

.social-widget-root {
    position: relative;
    z-index: 99990;
}

.social-widget-root .social-widget.container {
    --widget-main-color: var(--d-widget-main-color);
    --widget-text-color: var(--d-widget-text-color);
    --widget-main-size: var(--d-widget-main-size);
    --widget-main-icon-size: var(--d-widget-main-icon-size);
    --widget-offset-bottom: var(--d-widget-offset-bottom);
    --widget-offset-side: var(--d-widget-offset-side);
    --widget-item-icon-size: var(--d-widget-item-icon-size);
    --widget-item-font-size: var(--d-widget-item-font-size);
    --widget-panel-color: var(--d-widget-panel-color);
    --widget-panel-opacity: var(--d-widget-panel-opacity);
    --widget-radius: var(--d-widget-radius);
}

.social-widget.container {
    position: fixed;
    bottom: var(--widget-offset-bottom, 20px);
    left: auto;
    right: auto;
    transform: none;
    display: flex;
    flex-direction: column-reverse;
    align-items: center;
    z-index: 99991;
}

/* Превью и режим с единым классом позиции (админка) */
.social-widget.social-widget--right {
    left: auto;
    right: var(--widget-offset-side, 20px);
    align-items: flex-end;
}

.social-widget.social-widget--left {
    left: var(--widget-offset-side, 20px);
    right: auto;
    align-items: flex-start;
}

.social-widget.social-widget--center {
    left: 50%;
    right: auto;
    align-items: center;
    transform: translateX(-50%);
}

/* Десктоп на фронте */
@media (min-width: 768px) {
    .social-widget.social-widget--pos-desktop-right {
        left: auto;
        right: var(--widget-offset-side, 20px);
        align-items: flex-end;
        transform: none;
    }

    .social-widget.social-widget--pos-desktop-left {
        left: var(--widget-offset-side, 20px);
        right: auto;
        align-items: flex-start;
        transform: none;
    }

    .social-widget.social-widget--pos-desktop-right .media-icons {
        transform: translateX(calc(100% + 16px));
    }

    .social-widget.social-widget--pos-desktop-left .media-icons {
        transform: translateX(calc(-100% - 16px));
    }

    .social-widget.social-widget--icons-only.social-widget--pos-desktop-right .media-icons {
        transform: translateX(calc(100% + 16px + (100% - var(--widget-main-size, 35px)) / 2));
    }

    .social-widget.social-widget--icons-only.social-widget--pos-desktop-left .media-icons {
        transform: translateX(calc(-100% - 16px + (var(--widget-main-size, 35px) - 100%) / 2));
    }

    .social-widget.social-widget--pos-desktop-right.is-open .media-icons,
    .social-widget.social-widget--pos-desktop-right .close-btn.open ~ .media-icons,
    .social-widget.social-widget--pos-desktop-left.is-open .media-icons,
    .social-widget.social-widget--pos-desktop-left .close-btn.open ~ .media-icons {
        transform: translateX(0);
    }

    .social-widget.social-widget--icons-only.social-widget--pos-desktop-right.is-open .media-icons,
    .social-widget.social-widget--icons-only.social-widget--pos-desktop-right .close-btn.open ~ .media-icons {
        transform: translateX(calc((100% - var(--widget-main-size, 35px)) / 2));
    }

    .social-widget.social-widget--icons-only.social-widget--pos-desktop-left.is-open .media-icons,
    .social-widget.social-widget--icons-only.social-widget--pos-desktop-left .close-btn.open ~ .media-icons {
        transform: translateX(calc((var(--widget-main-size, 35px) - 100%) / 2));
    }
}

/* Мобилка на фронте */
@media (max-width: 767px) {
    .social-widget-root .social-widget.container {
        --widget-main-color: var(--m-widget-main-color);
        --widget-text-color: var(--m-widget-text-color);
        --widget-main-size: var(--m-widget-main-size);
        --widget-main-icon-size: var(--m-widget-main-icon-size);
        --widget-offset-bottom: var(--m-widget-offset-bottom);
        --widget-offset-side: var(--m-widget-offset-side);
        --widget-item-icon-size: var(--m-widget-item-icon-size);
        --widget-item-font-size: var(--m-widget-item-font-size);
        --widget-panel-color: var(--m-widget-panel-color);
        --widget-panel-opacity: var(--m-widget-panel-opacity);
        --widget-radius: var(--m-widget-radius);
    }

    .social-widget-root[data-desktop-only] .social-widget.container {
        display: none !important;
    }

    .social-widget.social-widget--pos-mobile-right {
        left: auto;
        right: var(--widget-offset-side, 20px);
        align-items: flex-end;
        transform: none;
    }

    .social-widget.social-widget--pos-mobile-left {
        left: var(--widget-offset-side, 20px);
        right: auto;
        align-items: flex-start;
        transform: none;
    }

    .social-widget.social-widget--pos-mobile-center {
        left: 50%;
        right: auto;
        align-items: center;
        transform: translateX(-50%);
    }

    .social-widget.social-widget--pos-mobile-right .media-icons {
        transform: translateX(calc(100% + 16px));
    }

    .social-widget.social-widget--pos-mobile-left .media-icons {
        transform: translateX(calc(-100% - 16px));
    }

    .social-widget.social-widget--pos-mobile-center .media-icons {
        transform: translateY(calc(100% + 16px));
    }

    .social-widget.social-widget--mobile-icons-only.social-widget--pos-mobile-right .media-icons {
        transform: translateX(calc(100% + 16px + (100% - var(--widget-main-size, 35px)) / 2));
    }

    .social-widget.social-widget--mobile-icons-only.social-widget--pos-mobile-left .media-icons {
        transform: translateX(calc(-100% - 16px + (var(--widget-main-size, 35px) - 100%) / 2));
    }

    .social-widget.social-widget--pos-mobile-right.is-open .media-icons,
    .social-widget.social-widget--pos-mobile-right .close-btn.open ~ .media-icons,
    .social-widget.social-widget--pos-mobile-left.is-open .media-icons,
    .social-widget.social-widget--pos-mobile-left .close-btn.open ~ .media-icons {
        transform: translateX(0);
    }

    .social-widget.social-widget--pos-mobile-center.is-open .media-icons,
    .social-widget.social-widget--pos-mobile-center .close-btn.open ~ .media-icons {
        transform: translateY(0);
    }

    .social-widget.social-widget--mobile-icons-only.social-widget--pos-mobile-right.is-open .media-icons,
    .social-widget.social-widget--mobile-icons-only.social-widget--pos-mobile-right .close-btn.open ~ .media-icons {
        transform: translateX(calc((100% - var(--widget-main-size, 35px)) / 2));
    }

    .social-widget.social-widget--mobile-icons-only.social-widget--pos-mobile-left.is-open .media-icons,
    .social-widget.social-widget--mobile-icons-only.social-widget--pos-mobile-left .close-btn.open ~ .media-icons {
        transform: translateX(calc((var(--widget-main-size, 35px) - 100%) / 2));
    }

    .social-widget-root--mobile-icons-only .social-widget.container .media-icons,
    .social-widget.social-widget--mobile-icons-only .media-icons {
        min-width: 0;
        max-width: none;
        width: auto;
        align-items: center;
    }

    .social-widget-root--mobile-icons-only .social-widget.container .media-icons a,
    .social-widget.social-widget--mobile-icons-only .media-icons a {
        width: var(--widget-item-size, 35px);
        height: var(--widget-item-size, 35px);
        min-height: var(--widget-item-size, 35px);
        max-width: none;
        justify-content: center;
        padding: 6px;
    }

    .social-widget-root--mobile-labeled .social-widget.container.social-widget--icons-only .media-icons,
    .social-widget-root--mobile-labeled .social-widget.container .media-icons {
        width: auto;
        max-width: var(--widget-panel-width, 280px);
    }

    .social-widget-root--mobile-labeled .social-widget.container.social-widget--icons-only .media-icons a,
    .social-widget-root--mobile-labeled .social-widget.container .media-icons a {
        width: auto;
        min-height: var(--widget-item-size, 35px);
        height: auto;
        max-width: var(--widget-panel-width, 280px);
        justify-content: flex-start;
        padding: 6px 10px;
    }

    .social-widget-root--mobile-icons-only .social-widget.container.social-widget--labeled .media-icons a,
    .social-widget-root--mobile-icons-only .social-widget.container.social-widget--mobile-labeled .media-icons a {
        width: var(--widget-item-size, 35px);
        height: var(--widget-item-size, 35px);
        min-height: var(--widget-item-size, 35px);
        max-width: none;
        justify-content: center;
        padding: 6px;
    }

    .social-widget__label--desktop,
    .social-widget__tooltip--desktop {
        display: none !important;
    }

    .social-widget-root--mobile-icons-only .social-widget__label--mobile,
    .social-widget.social-widget--mobile-icons-only .social-widget__label--mobile {
        display: none !important;
    }

    .social-widget-root--mobile-labeled .social-widget__label--mobile {
        display: inline;
    }

    .social-widget-root--mobile-icons-only .social-widget__tooltip--mobile {
        display: block;
    }
}

.social-widget.social-widget--center .media-icons {
    transform: translateY(calc(100% + 16px));
}

.social-widget--center.is-open .media-icons,
.social-widget--center .close-btn.open ~ .media-icons {
    transform: translateY(0);
}

.social-widget--icons-only.social-widget--center .media-icons {
    transform: translateY(calc(100% + 16px));
}

.social-widget--icons-only.social-widget--center.is-open .media-icons,
.social-widget--icons-only.social-widget--center .close-btn.open ~ .media-icons {
    transform: translateY(0);
}

.social-widget .media-icons {
    display: flex;
    align-items: stretch;
    flex-direction: column;
    justify-content: center;
    background-color: color-mix(in srgb, var(--widget-panel-color, #ffffff) var(--widget-panel-opacity, 100%), transparent);
    padding: 6px;
    border-radius: var(--widget-radius, 6px);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    width: auto;
    transition:
        transform 0.45s cubic-bezier(0.68, -0.55, 0.265, 1.55),
        opacity 0.35s ease,
        visibility 0.35s ease;
}

.social-widget--right .media-icons {
    transform: translateX(calc(100% + 16px));
}

.social-widget--left .media-icons {
    transform: translateX(calc(-100% - 16px));
}

.social-widget--icons-only.social-widget--right .media-icons {
    transform: translateX(calc(100% + 16px + (100% - var(--widget-main-size, 35px)) / 2));
}

.social-widget--icons-only.social-widget--left .media-icons {
    transform: translateX(calc(-100% - 16px + (var(--widget-main-size, 35px) - 100%) / 2));
}

.social-widget.is-open .media-icons,
.social-widget .close-btn.open ~ .media-icons {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.social-widget--right.is-open .media-icons,
.social-widget--right .close-btn.open ~ .media-icons,
.social-widget--left.is-open .media-icons,
.social-widget--left .close-btn.open ~ .media-icons {
    transform: translateX(0);
}

.social-widget--center.is-open .media-icons,
.social-widget--center .close-btn.open ~ .media-icons {
    transform: translateY(0);
}

.social-widget--icons-only.social-widget--right.is-open .media-icons,
.social-widget--icons-only.social-widget--right .close-btn.open ~ .media-icons {
    transform: translateX(calc((100% - var(--widget-main-size, 35px)) / 2));
}

.social-widget--icons-only.social-widget--left.is-open .media-icons,
.social-widget--icons-only.social-widget--left .close-btn.open ~ .media-icons {
    transform: translateX(calc((var(--widget-main-size, 35px) - 100%) / 2));
}

.social-widget--icons-only.social-widget--center.is-open .media-icons,
.social-widget--icons-only.social-widget--center .close-btn.open ~ .media-icons {
    transform: translateY(0);
}

.social-widget--icons-only .media-icons {
    min-width: 0;
    max-width: none;
    align-items: center;
}

.social-widget--icons-only .media-icons a {
    width: var(--widget-item-size, 35px);
    height: var(--widget-item-size, 35px);
    min-height: var(--widget-item-size, 35px);
    justify-content: center;
    padding: 6px;
}

.social-widget--labeled .media-icons {
    width: auto;
    max-width: var(--widget-panel-width, 280px);
}

.social-widget--labeled .media-icons a {
    min-height: var(--widget-item-size, 35px);
    height: auto;
    max-width: var(--widget-panel-width, 280px);
    justify-content: flex-start;
    padding: 6px 10px;
}

.social-widget .media-icons a {
    text-decoration: none;
    position: relative;
    display: flex;
    align-items: center;
    gap: 8px;
    border-radius: var(--widget-radius, 6px);
    margin: 6px;
    box-sizing: border-box;
    transition: filter 0.2s ease;
}

.social-widget .media-icons a:hover {
    filter: brightness(1.1);
}

.social-widget .close-btn .social-widget__icon {
    width: var(--widget-main-icon-size, 18px);
    height: var(--widget-main-icon-size, 18px);
}

.social-widget .media-icons .social-widget__icon {
    width: var(--widget-item-icon-size, 18px);
    height: var(--widget-item-icon-size, 18px);
}

.social-widget .social-widget__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: currentColor;
    flex-shrink: 0;
}

.social-widget .social-widget__icon svg {
    width: 100%;
    height: 100%;
    display: block;
}

.social-widget .social-widget__label {
    font-size: var(--widget-item-font-size, 13px);
    font-weight: 500;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    min-width: 0;
    flex: 1;
}

.social-widget .media-icons a .tooltip {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    font-size: var(--widget-item-font-size, 13px);
    font-weight: 400;
    pointer-events: none;
    background-color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    opacity: 0;
    transition: all 0.2s linear;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.05);
    white-space: nowrap;
}

.social-widget--right .media-icons a .tooltip {
    right: calc(100% + 10px);
    left: auto;
    transform: translateY(-50%) translateX(6px);
}

.social-widget--left .media-icons a .tooltip {
    left: calc(100% + 10px);
    right: auto;
    transform: translateY(-50%) translateX(-6px);
}

.social-widget--right .media-icons a:hover .tooltip {
    opacity: 1;
    transform: translateY(-50%) translateX(0);
}

.social-widget--left .media-icons a:hover .tooltip {
    opacity: 1;
    transform: translateY(-50%) translateX(0);
}

.social-widget--right .media-icons a .tooltip::before {
    content: "";
    position: absolute;
    height: 10px;
    width: 10px;
    top: 50%;
    right: -5px;
    left: auto;
    transform: translateY(-50%) rotate(45deg);
    background-color: #fff;
}

.social-widget--left .media-icons a .tooltip::before {
    content: "";
    position: absolute;
    height: 10px;
    width: 10px;
    top: 50%;
    left: -5px;
    right: auto;
    transform: translateY(-50%) rotate(45deg);
    background-color: #fff;
}

.social-widget .close-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    height: var(--widget-main-size, 35px);
    width: var(--widget-main-size, 35px);
    border-radius: 50%;
    color: var(--widget-text-color, #fff);
    margin-top: 20px;
    background-color: var(--widget-main-color, #8e36ff);
    --widget-pulse-glow: color-mix(in srgb, var(--widget-main-color, #8e36ff) 20%, transparent);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transform: none;
    transition:
        transform 0.35s cubic-bezier(0.68, -0.55, 0.265, 1.55),
        filter 0.2s ease;
}

.social-widget .close-btn:hover {
    filter: brightness(1.1);
}

.social-widget .close-btn.open,
.social-widget .close-btn.open.social-widget__main {
    transform: none;
    background-color: var(--widget-main-color, #8e36ff);
}

.social-widget__main--pulse {
    animation: social-widget-pulse 10s ease-in-out infinite;
}

@keyframes social-widget-pulse {
    0%, 49%, 100% {
        transform: scale(1);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }
    8%, 16%, 24%, 32%, 40% {
        transform: scale(1.1);
        box-shadow: 0 0 0 8px var(--widget-pulse-glow);
    }
    12%, 20%, 28%, 36%, 44% {
        transform: scale(1);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }
}

.social-widget__main--shake {
    animation: social-widget-shake 10s ease-in-out infinite;
}

@keyframes social-widget-shake {
    0%, 22%, 100% {
        transform: translateX(0);
    }
    2% { transform: translateX(-4px); }
    4% { transform: translateX(4px); }
    6% { transform: translateX(-3px); }
    8% { transform: translateX(3px); }
    10% { transform: translateX(-2px); }
    12% { transform: translateX(2px); }
    14% { transform: translateX(-3px); }
    16% { transform: translateX(3px); }
    18% { transform: translateX(-2px); }
    20% { transform: translateX(0); }
}

.social-widget__main--jump {
    animation: social-widget-jump 10s ease-in-out infinite;
}

@keyframes social-widget-jump {
    0%, 28%, 100% {
        transform: translateY(0);
    }
    4% { transform: translateY(-10px); }
    8% { transform: translateY(0); }
    12% { transform: translateY(-8px); }
    16% { transform: translateY(0); }
    20% { transform: translateY(-6px); }
    24% { transform: translateY(0); }
}

.social-widget-popup {
    position: fixed;
    inset: 0;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.social-widget-popup__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
}

.social-widget-popup__dialog {
    position: relative;
    width: min(320px, 96vw);
    background: color-mix(in srgb, var(--widget-panel-color, #ffffff) var(--widget-panel-opacity, 100%), transparent);
    border-radius: var(--widget-radius, 6px);
    box-shadow: var(--widget-popup-shadow, 0 5px 10px rgba(0, 0, 0, 0.1));
    padding: 1.25rem 1.25rem 1.5rem;
}

.social-widget-popup__close {
    position: absolute;
    top: 0.5rem;
    right: 0.75rem;
    border: 0;
    background: transparent;
    font-size: 1.5rem;
    line-height: 1;
    cursor: pointer;
    color: #64748b;
}

.social-widget-popup__title {
    margin: 0 2rem 0.75rem 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
}

.social-widget-popup__content {
    color: #4b5563;
    line-height: 1.5;
    white-space: pre-line;
}

@media (max-width: 767px) {
    .social-widget-root[data-desktop-only] .social-widget.container {
        display: none !important;
    }
}

@media (min-width: 768px) {
    .social-widget-root[data-mobile-only] .social-widget.container {
        display: none !important;
    }
}

.social-widget-preview-frame {
    min-height: 28rem;
    border-radius: 0.75rem;
    background: linear-gradient(180deg, #e3f2fd 0%, #f8fafc 100%);
    padding: 0;
    overflow: hidden;
    position: relative;
}

.social-widget-preview-frame .social-widget.container {
    position: absolute;
    bottom: var(--widget-offset-bottom, 20px);
}

.social-widget-preview-frame .social-widget.social-widget--right {
    right: var(--widget-offset-side, 20px);
    left: auto;
}

.social-widget-preview-frame .social-widget.social-widget--left {
    left: var(--widget-offset-side, 20px);
    right: auto;
}

.social-widget-preview-frame .social-widget.social-widget--center {
    left: 50%;
    right: auto;
    transform: translateX(-50%);
}

.social-widget-preview-frame--mobile {
    min-height: 520px;
    width: 100%;
    position: relative;
}

.social-widget-preview-frame--mobile .social-widget.container {
    position: absolute;
}

/* Симуляция мобилки в превью админки (без media query) */
.social-widget-preview-frame--mobile .social-widget.social-widget--icons-only.social-widget--right .media-icons {
    transform: translateX(calc(100% + 16px + (100% - var(--widget-main-size, 35px)) / 2));
}

.social-widget-preview-frame--mobile .social-widget.social-widget--icons-only.social-widget--left .media-icons {
    transform: translateX(calc(-100% - 16px + (var(--widget-main-size, 35px) - 100%) / 2));
}

.social-widget-preview-frame--mobile .social-widget.social-widget--icons-only.social-widget--center .media-icons {
    transform: translateY(calc(100% + 16px));
}

.social-widget-preview-frame--mobile .social-widget.social-widget--icons-only.social-widget--right.is-open .media-icons,
.social-widget-preview-frame--mobile .social-widget.social-widget--icons-only.social-widget--right .close-btn.open ~ .media-icons {
    transform: translateX(calc((100% - var(--widget-main-size, 35px)) / 2));
}

.social-widget-preview-frame--mobile .social-widget.social-widget--icons-only.social-widget--left.is-open .media-icons,
.social-widget-preview-frame--mobile .social-widget.social-widget--icons-only.social-widget--left .close-btn.open ~ .media-icons {
    transform: translateX(calc((var(--widget-main-size, 35px) - 100%) / 2));
}

.social-widget-preview-frame--mobile .social-widget.social-widget--icons-only.social-widget--center.is-open .media-icons,
.social-widget-preview-frame--mobile .social-widget.social-widget--icons-only.social-widget--center .close-btn.open ~ .media-icons {
    transform: translateY(0);
}

@media (min-width: 768px) {
    .social-widget__label--mobile,
    .social-widget__tooltip--mobile {
        display: none !important;
    }

    .social-widget--labeled .social-widget__label--desktop {
        display: inline;
    }

    .social-widget--icons-only .social-widget__tooltip--desktop {
        display: block;
    }
}
