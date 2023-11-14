class UiCreator {
    constructor() {
        this.text = '';
    }

    // Один тег
    tag(tag, counter = 1, css = false, html = '', type = '', value = false, attr = false) {

        if (css) {
            css = css.split(' ');
        }

        if (counter > 1) {
            let i = 0;
            let tags = [];

            while (i < counter) {
                let unit = document.createElement(tag);
                unit = this._init(unit, css, type, html, value, attr);

                tags.push(unit);
                i++;
            }

            return tags;
        } else {
            let unit = document.createElement(tag);
            unit = this._init(unit, css, type, html, value, attr);

            return unit;
        }
    }

    // Массив в качестве карты для создания
    tags(tags) {
        //
    }

    button(counter, css, html, type, value, attr) {
        return this.tag('button', counter, css, html, 'button', value, attr);
    }
    select(counter, css, type, value, attr) {
        return this.tag('select', counter, css, type, value, attr);
    }
    input(counter, css, type, value, attr) {
        return this.tag('input', counter, css, type, value, attr);
    }

    tr(counter, css, html, rowspan) {
        let arr = [];
        if (rowspan) {
            arr.push(['rowspan', rowspan]);
        }

        return this.tag('tr', counter, css, html, false, false, arr);
    }
    td(counter, css, html, colspan) {
        let arr = [];
        if (colspan) {
            arr.push(['colspan', colspan]);
        }

        return this.tag('td', counter, css, html, false, false, arr);
    }

    // Обработка всего ...
    _init (unit, css, type, html, value, attr) {
        if (css) { unit.classList.add(...css) }
        if (type) { unit.type = type }
        if (html && typeof (html) === 'object') {
            unit.appendChild(html)
        } else {
            unit.innerHTML = html
        }
        if (value) { unit.value = value }
        if (attr) {
            attr.forEach(function(elem) {
                unit.setAttribute(elem[0], elem[1]);
            });
        }

        return unit;
    }
}
