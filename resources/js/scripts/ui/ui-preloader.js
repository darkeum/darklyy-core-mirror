class UiPreloader {
    constructor(anchor = 'module-preloader', to = 'app-content', shift = 'main-menu') {

        this.anchor = anchor;
        this.to = $('.' + to);
        this.shift = $('.' + shift);

        this.css = 'position-fixed';

        this.html = this._loadHtml();
        this.preloader = $(this.html).addClass([anchor, this.css].join(' '));
        this.to.append(this.preloader);

        // Наблюдатель за to
        let resizeObserver = new ResizeObserver(() => {
            $('.' + anchor).css({
                    width: ($(document).width() - this.shift.width()) + 'px',
                    left: this.shift.width() + 'px'
                });
        });
        resizeObserver.observe(this.to[0]);
    }

    on(css = this.css) {
        const container = $('.' + this.anchor);
        container.addClass(css);
        container.toggleClass('d-none');
    }

    off(css = this.css) {
        const container = $('.' + this.anchor);
        container.removeClass(css);
        container.toggleClass('d-none');
    }

    _loadHtml() {
        return $.ajax({
            type: "GET",
            url: "/preloader",
            async: false
        }).responseText;
    }
}