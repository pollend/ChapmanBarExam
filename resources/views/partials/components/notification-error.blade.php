<div data-notification class="bx--inline-notification bx--inline-notification--error" role="alert">
    <div class="bx--inline-notification__details">
        <svg focusable="false" preserveAspectRatio="xMidYMid meet" style="will-change: transform;" xmlns="http://www.w3.org/2000/svg" class="bx--inline-notification__icon" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1c-5 0-9 4-9 9s4 9 9 9 9-4 9-9-4-9-9-9zm3.5 13.5l-8-8 1-1 8 8-1 1z"></path><path d="M13.5 14.5l-8-8 1-1 8 8-1 1z" data-icon-path="inner-path" opacity="0"></path></svg>
        <div class="bx--inline-notification__text-wrapper">
            <p class="bx--inline-notification__title">{{ $title }}</p>
            <p class="bx--inline-notification__subtitle">{{ $sub_title }}</p>
        </div>
    </div>
    <button data-notification-btn class="bx--inline-notification__close-button" type="button" aria-label="close">
        <svg focusable="false" preserveAspectRatio="xMidYMid meet" style="will-change: transform;" xmlns="http://www.w3.org/2000/svg" class="bx--inline-notification__close-icon" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M12 4.7l-.7-.7L8 7.3 4.7 4l-.7.7L7.3 8 4 11.3l.7.7L8 8.7l3.3 3.3.7-.7L8.7 8z"></path></svg>
    </button>
</div>