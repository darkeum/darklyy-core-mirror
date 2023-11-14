// Quill + textarea а то были бали... Использовать это!
function quill(selector = null, options = null) {
    let elements;

    if (selector) {
        elements = Array.prototype.slice.call(document.querySelectorAll(selector));
    } else {
        elements = Array.prototype.slice.call(document.querySelectorAll('[data-quilljs]'));
    }
    elements.forEach(function (el) {
        let container, placeholder, defaults, type = el.type, editor;

        if (selector && el.hasAttribute("data-quilljs")) {
            return
        }

        if (type == 'textarea') {
            let value = el.value;
            container = document.createElement('div');
            container.innerHTML = value;
            el.parentNode.insertBefore(container, el.nextSibling);
            el.style.display = "none";
            placeholder = el.placeholder;
        } else {
            placeholder = null;
            container = el;
        }

        if (!options) {
            defaults = {
                theme: 'snow',
                placeholder: placeholder
            };
        } else {

            if (el.placeholder) {
                options.placeholder = placeholder;
            } else if (options.placeholder) {
                // options.placeholder = options.placeholder;
            } else if (!options.placeholder || !el.placeholder) {
                // options.placeholder = null;
            }
            defaults = options;
        }

        editor = new Quill(container, defaults);
        editor.on('text-change', function () {
            el.value = editor.root.innerHTML;
        });

        // Минимальная высота от параметра rows
        let rows = el.getAttribute('rows');
        editor.root.style.minHeight = (rows * 21) + 'px';
    });
}

(function () {
    quill();
})();