$(function(){
    const range = $("#finance-trust-range");
    range.on("input", function(){
        const type = +this.value;
        const reliability = JSON.parse($("#reliability").html());
        const o = getOrganizationDetail();
        const procent = $(".organization-reliability__procent");
        const sum = $(".organization-reliability__sum");
        const text = $(".organization-reliability__text");
        const image = $(".organization-reliability__image");
        const color = ["organization-reliability__text_green", "organization-reliability__text_blue", "organization-reliability__text_yellow", "organization-reliability__text_red"];
        const images = [`<i class="far fa-thumbs-up text-success"></i>`, `<i class="far fa-handshake text-alert"></i>`, `<i class="far fa-handshake text-warning"></i>`, `<i class="far fa-thumbs-down text-danger"></i>`];
        image.html(``);
        color.forEach(value => {
            text.removeClass(value);
        });
        if(reliability.length > 0){
            const r = reliability[type];
            const price = priceView(r.dealSum);
            procent.html(`${r.percent}%`);
            sum.html(price);
            switch(type){
                case 0:
                case 1:
                case 2:
                    text.html(`Аванс на сумму ${price} имеет низкий риск`);
                    text.addClass(color[0]);
                    image.html(images[0]);
                break;
                case 3:
                    text.html(`Безопасная сумма аванса ${price}`);
                    text.addClass(color[1]);
                    image.html(images[1]);
                break;
                case 4:
                case 5:
                case 6:
                    text.html(`Аванс на сумму ${price} имеет средний риск`);
                    text.addClass(color[2]);
                    image.html(images[2]);
                break;
                case 7:
                case 8:
                case 9:
                case 10:
                    text.html(`Аванс на сумму ${price} имеет высокий риск`);
                    text.addClass(color[3]);
                    image.html(images[3]);
                break;
            }
        } else {
            const range_value = $(".organization-reliability__range-value");
            switch(o.reliabityStatus){
                case "SUPER_RELIABLE":
                    text.html(`Надежная компания`);
                    text.addClass(color[0]);
                    image.html(images[0]);
                    procent.html(`0 %`);
                    sum.html(`∞`);
                    range_value.hide();
                break;
                case "TERMINATED":
                    text.html(`Недействующая компания`);
                    text.addClass(color[3]);
                    image.html(images[3]);
                    procent.html(`—`);
                    sum.html(`—`);
                    range_value.hide();
                break;
            }
        }
    });
    range.trigger('input');

    function getMainId(){
        return $("#mainId").val();
    }

    function priceView(value){
        const pl = String(Math.round(value)).length;
        let new_price = 0;
        let postfix = false;
        if(pl >=7 && pl < 10){
            new_price = value / 1000000;
            postfix = "млн"
        } else if(pl >= 10 && pl < 13){
            new_price = value / 1000000000;
            postfix = "млрд";
        } else if(pl >= 13 && pl < 16){
            new_price = value / 1000000000000;
            postfix = "трлн"
        } else {
            new_price = value;
        }
        const p = postfix ? ` ${postfix} ₽` : ` ₽`;
        return Price.priceSet(new_price, p).replace(".", ",");
    }

    function getOrganizationDetail(){
        return JSON.parse($("#organization_detail").html());
    }

    function init_contracts(type){
        const contracts_customer = JSON.parse($("#contracts_customer_data").html());
        const contracts_supplier = JSON.parse($("#contracts_supplier_data").html());
        const contracts_total = JSON.parse($("#contracts_total_data").html());
        const text = $(".organization-contract_text");
        const o = getOrganizationDetail();
        const sum = priceView(contracts_total[type].RUB.sum);
        switch(type){
            case "customer":
                text.html(`Организация ${o.shortName} выступила заказчиком в <strong>${contracts_total.customer?.RUB.count}</strong> госконтрактах на сумму <strong>${sum}</strong>`);
                contracts_list(contracts_customer);
            break;
            case "supplier":
                text.html(`Организация ${o.shortName} являлась поставщиком в <strong>${contracts_total.supplier?.RUB.count}</strong> госконтрактах на сумму <strong>${sum}</strong>`);
                contracts_list(contracts_supplier);
            break;
        }
    };

    function contracts_list(items){
        const con = $("#organization-contract_items");
        con.html(``);
        items.content.forEach((item, idx) => {
            const price = Price.priceSet(item.stats.RUB.sum.toFixed(0), ` ₽`).replace(".", ",").replace(",00", "");
            con.append(`<div class="contract-item">
                <div class="contract-item_title">${item.name}</div>
                <div><strong>${item.totalCount}</strong> контрактов на сумму <strong>${price}</strong></div>
            </div>`);
            if (idx != items.content.length - 1) con.append(`<hr>`);
        });
    }

    $(".organization-contract_buttons").each((i, el) => {
        const item = $(el);
        const type = item.data("type");
        if(item.prop("checked")) init_contracts(type);
        item.on("change", function(){
            init_contracts(type);
        });
    });

    $(".contacts-buttons").on("click", function(){
        const item = $(this);
        const next = item.data("next");
        const before = item.html();
        item.html(next);
        item.data("next", before);
        $(`.contacts-items`).toggleClass(`contacts-items__short`);
    });
});